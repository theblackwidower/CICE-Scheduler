DROP TRIGGER IF EXISTS check_class_overlaps ON tbl_class_times;
DROP TRIGGER IF EXISTS check_facilitator_overlaps ON tbl_schedule;
DROP TRIGGER IF EXISTS check_stretch_length ON tbl_schedule;
DROP TRIGGER IF EXISTS check_day_length ON tbl_schedule;
DROP TRIGGER IF EXISTS check_travel_time ON tbl_schedule;
DROP TRIGGER IF EXISTS check_max_paired_students ON tbl_assigned_students;

CREATE OR REPLACE FUNCTION check_class_overlaps() RETURNS TRIGGER AS
$$
	DECLARE
		overlap_count INTEGER;
	BEGIN
		SELECT COUNT(*) INTO overlap_count FROM tbl_class_times WHERE
				tbl_class_times.semester_id = NEW.semester_id AND
				tbl_class_times.day_id = NEW.day_id AND
				tbl_class_times.end_time > NEW.start_time AND
				tbl_class_times.start_time < NEW.end_time AND
				(tbl_class_times.course_rn = NEW.course_rn OR
				tbl_class_times.room_number = NEW.room_number);
		IF (overlap_count > 1) THEN
			RAISE EXCEPTION 'overlap detected with same room or same class';
		END IF;
		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_facilitator_overlaps() RETURNS TRIGGER AS
$$
	DECLARE
		var_end_time INTEGER;
		overlap_count INTEGER;
	BEGIN
		SELECT end_time INTO var_end_time FROM tbl_class_times WHERE
				tbl_class_times.semester_id = NEW.semester_id AND
				tbl_class_times.day_id = NEW.day_id AND
				tbl_class_times.course_rn = NEW.course_rn AND
				tbl_class_times.start_time = NEW.start_time;

		SELECT COUNT(*) INTO overlap_count FROM view_complete_schedule WHERE --class_role = 'F' AND
				view_complete_schedule.semester_id = NEW.semester_id AND
				view_complete_schedule.day_id = NEW.day_id AND
				view_complete_schedule.facilitator = NEW.facilitator AND
				view_complete_schedule.end_time > NEW.start_time AND
				view_complete_schedule.start_time < var_end_time;

		IF (overlap_count > 1) THEN
			RAISE EXCEPTION 'overlap detected';
		END IF;
		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_stretch_length() RETURNS TRIGGER AS
$$
	DECLARE
		const_hour_limit CONSTANT INTEGER := 4;
	BEGIN
		IF (count_neighbouring_hours(NEW.semester_id, NEW.day_id,
				NEW.start_time, NEW.start_time, NEW.facilitator) > const_hour_limit) THEN
			RAISE EXCEPTION 'too many hours in a row';
		END IF;
		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_day_length() RETURNS TRIGGER AS
$$
	DECLARE
		var_end_time INTEGER;
		const_day_limit CONSTANT INTEGER := 8;
	BEGIN
		SELECT end_time INTO var_end_time FROM tbl_class_times WHERE
				tbl_class_times.semester_id = NEW.semester_id AND
				tbl_class_times.day_id = NEW.day_id AND
				tbl_class_times.course_rn = NEW.course_rn AND
				tbl_class_times.start_time = NEW.start_time;

		PERFORM * FROM view_complete_schedule WHERE --class_role = 'F' AND
				view_complete_schedule.semester_id = NEW.semester_id AND
				view_complete_schedule.day_id = NEW.day_id AND
				view_complete_schedule.facilitator = NEW.facilitator AND
				(view_complete_schedule.end_time > (NEW.start_time + const_day_limit) OR
				view_complete_schedule.start_time < (var_end_time - const_day_limit));

		IF FOUND THEN
			RAISE EXCEPTION 'working too long this day';
		END IF;
		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_travel_time() RETURNS TRIGGER AS
$$
	DECLARE
		var_end_time INTEGER;
		var_campus CHAR(1);
		const_min_travel_time CONSTANT INTEGER := 2;
	BEGIN
		SELECT end_time, campus_id INTO var_end_time, var_campus
				FROM tbl_class_times, tbl_rooms WHERE
				tbl_class_times.semester_id = NEW.semester_id AND
				tbl_class_times.day_id = NEW.day_id AND
				tbl_class_times.course_rn = NEW.course_rn AND
				tbl_class_times.start_time = NEW.start_time AND
				tbl_class_times.room_number = tbl_rooms.room_number;

		PERFORM * FROM view_complete_schedule, tbl_rooms WHERE --class_role = 'F' AND
				semester_id = NEW.semester_id AND day_id = NEW.day_id AND facilitator = NEW.facilitator AND
				view_complete_schedule.room_number = tbl_rooms.room_number AND campus_id <> var_campus AND
				((start_time < (var_end_time + const_min_travel_time) AND start_time > NEW.start_time) OR
				(end_time > (NEW.start_time - const_min_travel_time) AND end_time < var_end_time));

		IF FOUND THEN
			RAISE EXCEPTION 'not enough travel time';
		END IF;
		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_max_paired_students() RETURNS TRIGGER AS
$$
	DECLARE
		student_count INTEGER;
		const_max_students CONSTANT INTEGER := 3;
	BEGIN
		SELECT COUNT(student_id) INTO student_count FROM tbl_assigned_students WHERE
				tbl_assigned_students.semester_id = NEW.semester_id AND
				tbl_assigned_students.day_id = NEW.day_id AND
				tbl_assigned_students.start_time = NEW.start_time AND
				tbl_assigned_students.course_rn = NEW.course_rn AND
				tbl_assigned_students.facilitator = NEW.facilitator;

		IF (student_count > const_max_students) THEN
			RAISE EXCEPTION 'too many students paired with facilitator';
		END IF;
		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE CONSTRAINT TRIGGER check_class_overlaps AFTER INSERT OR UPDATE ON tbl_class_times
	FOR EACH ROW EXECUTE PROCEDURE check_class_overlaps();
CREATE CONSTRAINT TRIGGER check_facilitator_overlaps AFTER INSERT OR UPDATE ON tbl_schedule
	FOR EACH ROW EXECUTE PROCEDURE check_facilitator_overlaps();
CREATE CONSTRAINT TRIGGER check_stretch_length AFTER INSERT OR UPDATE ON tbl_schedule
	FOR EACH ROW EXECUTE PROCEDURE check_stretch_length();
CREATE CONSTRAINT TRIGGER check_day_length AFTER INSERT OR UPDATE ON tbl_schedule
	FOR EACH ROW EXECUTE PROCEDURE check_day_length();
CREATE CONSTRAINT TRIGGER check_travel_time AFTER INSERT OR UPDATE ON tbl_schedule
	FOR EACH ROW EXECUTE PROCEDURE check_travel_time();
CREATE CONSTRAINT TRIGGER check_max_paired_students AFTER INSERT OR UPDATE ON tbl_assigned_students
	FOR EACH ROW EXECUTE PROCEDURE check_max_paired_students();

DROP TRIGGER IF EXISTS erase_facilitator_conflicts ON tbl_class_times;
DROP TRIGGER IF EXISTS erase_facilitator_conflicts ON tbl_classes;

CREATE OR REPLACE FUNCTION erase_facilitator_conflicts_class_times() RETURNS TRIGGER AS
$$
	DECLARE
		var_campus CHAR(1);
		var_facilitator VARCHAR(255);
	BEGIN
		SELECT tbl_facilitators.email INTO var_facilitator
				FROM tbl_classes, tbl_professors, tbl_facilitators WHERE
				tbl_facilitators.email = tbl_professors.email AND
				tbl_professors.professor_id = tbl_classes.professor_id AND
				tbl_classes.semester_id = NEW.semester_id AND
				tbl_classes.course_rn = NEW.course_rn;

		IF FOUND THEN
			SELECT campus_id INTO var_campus FROM tbl_rooms WHERE NEW.room_number = tbl_rooms.room_number;
			EXECUTE erase_facilitator_conflicts(NEW.semester_id,
					NEW.day_id, NEW.start_time, NEW.end_time, var_facilitator, var_campus);
		END IF;

		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION erase_facilitator_conflicts_class_professor() RETURNS TRIGGER AS
$$
	DECLARE
		one_record RECORD;
		var_facilitator VARCHAR(255);
	BEGIN
		SELECT tbl_facilitators.email INTO var_facilitator
				FROM tbl_professors, tbl_facilitators WHERE
				tbl_facilitators.email = tbl_professors.email AND
				tbl_professors.professor_id = NEW.professor_id;

		IF FOUND THEN
			FOR one_record IN SELECT day_id, start_time, end_time, campus_id
					FROM tbl_class_times, tbl_rooms WHERE
					tbl_class_times.semester_id = NEW.semester_id AND
					tbl_class_times.course_rn = NEW.course_rn AND
					tbl_class_times.room_number = tbl_rooms.room_number LOOP
				EXECUTE erase_facilitator_conflicts(NEW.semester_id, one_record.day_id, one_record.start_time, one_record.end_time, var_facilitator, one_record.campus_id);
			END LOOP;
		END IF;
		RETURN NULL;
	END;
$$
LANGUAGE plpgsql;

CREATE TRIGGER erase_facilitator_conflicts AFTER INSERT OR UPDATE ON tbl_class_times
	FOR EACH ROW EXECUTE PROCEDURE erase_facilitator_conflicts_class_times();
CREATE TRIGGER erase_facilitator_conflicts AFTER UPDATE ON tbl_classes
	FOR EACH ROW WHEN (OLD.professor_id <> NEW.professor_id)
	EXECUTE PROCEDURE erase_facilitator_conflicts_class_professor();
