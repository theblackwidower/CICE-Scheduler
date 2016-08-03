CREATE OR REPLACE FUNCTION count_neighbouring_hours(in_semester_id TEXT, in_day_id INTEGER, in_start_time INTEGER, in_end_time INTEGER, in_facilitator TEXT) RETURNS INTEGER AS
$$
	DECLARE
		count_hours INTEGER := 0;
		time_find_mark INTEGER;
		time_result_mark INTEGER;
	BEGIN
		time_find_mark := in_start_time;
		LOOP
			SELECT view_complete_schedule.start_time INTO time_result_mark FROM view_complete_schedule WHERE
				view_complete_schedule.semester_id = in_semester_id AND
				view_complete_schedule.day_id = in_day_id AND
				view_complete_schedule.facilitator = in_facilitator AND
				view_complete_schedule.end_time = time_find_mark;
			IF FOUND THEN
				count_hours := count_hours + (time_find_mark - time_result_mark);
				time_find_mark := time_result_mark;
			ELSE
				EXIT;
			END IF;
		END LOOP;

		time_find_mark := in_end_time;
		LOOP
			SELECT view_complete_schedule.end_time INTO time_result_mark FROM view_complete_schedule WHERE
				view_complete_schedule.semester_id = in_semester_id AND
				view_complete_schedule.day_id = in_day_id AND
				view_complete_schedule.facilitator = in_facilitator AND
				view_complete_schedule.start_time = time_find_mark;
			IF FOUND THEN
				count_hours := count_hours + (time_result_mark - time_find_mark);
				time_find_mark := time_result_mark;
			ELSE
				EXIT;
			END IF;
		END LOOP;

		RETURN count_hours;
	END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION erase_facilitator_conflicts(in_semester_id TEXT, in_day_id INTEGER, in_start_time INTEGER, in_end_time INTEGER, in_facilitator TEXT, in_campus_id TEXT) RETURNS void AS
$$
	DECLARE
		one_record RECORD;
		const_day_limit CONSTANT INTEGER := 8;
		const_min_travel_time CONSTANT INTEGER := 2;
		--const_hour_limit CONSTANT INTEGER := 4;
	BEGIN

		FOR one_record IN SELECT DISTINCT course_rn, start_time FROM view_complete_schedule WHERE class_role = 'F' AND
				view_complete_schedule.semester_id = in_semester_id AND
				view_complete_schedule.day_id = in_day_id AND
				view_complete_schedule.facilitator = in_facilitator AND
				--looking for direct overlaps
				((view_complete_schedule.end_time > in_start_time AND
				view_complete_schedule.start_time < in_end_time) OR
				--looking for cases of overtime
				(view_complete_schedule.end_time > (in_start_time + const_day_limit) OR
				view_complete_schedule.start_time < (in_end_time - const_day_limit)) OR
				--looking for reduced travel time
				(room_number IN (SELECT room_number FROM tbl_rooms WHERE campus_id <> in_campus_id) AND
				((view_complete_schedule.start_time < (in_end_time + const_min_travel_time) AND
					view_complete_schedule.start_time > in_start_time) OR
				(view_complete_schedule.end_time > (in_start_time - const_min_travel_time) AND
					view_complete_schedule.end_time < in_end_time)))) LOOP

			DELETE FROM tbl_schedule WHERE
					tbl_schedule.semester_id = in_semester_id AND
					tbl_schedule.day_id = in_day_id AND
					tbl_schedule.facilitator = in_facilitator AND
					tbl_schedule.course_rn = one_record.course_rn AND
					tbl_schedule.start_time = one_record.start_time;

		END LOOP;
	END;
$$
LANGUAGE plpgsql;
