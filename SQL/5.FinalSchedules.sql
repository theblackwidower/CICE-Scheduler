DROP TABLE IF EXISTS tbl_schedule CASCADE;
DROP TABLE IF EXISTS tbl_assigned_students CASCADE;
DROP VIEW IF EXISTS view_complete_schedule;

CREATE TABLE tbl_schedule(
	semester_id CHAR(5) NOT NULL,
	course_rn CHAR(5) NOT NULL,
	day_id SMALLINT NOT NULL,
	start_time SMALLINT NOT NULL,
	facilitator VARCHAR(255) NOT NULL REFERENCES tbl_facilitators (email) ON UPDATE CASCADE,
	FOREIGN KEY (semester_id, course_rn, day_id, start_time)
		REFERENCES tbl_class_times (semester_id, course_rn, day_id, start_time) ON DELETE CASCADE,
	PRIMARY KEY (semester_id, course_rn, day_id, start_time, facilitator),
	UNIQUE (semester_id, day_id, start_time, facilitator)
);

CREATE TABLE tbl_assigned_students(
	semester_id CHAR(5) NOT NULL,
	course_rn CHAR(5) NOT NULL,
	day_id SMALLINT NOT NULL,
	start_time SMALLINT NOT NULL,
	facilitator VARCHAR(255) NOT NULL,
	student_id CHAR(9) NOT NULL,
	FOREIGN KEY (semester_id, course_rn, day_id, start_time, facilitator)
		REFERENCES tbl_schedule (semester_id, course_rn, day_id, start_time, facilitator) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (student_id, semester_id, course_rn)
		REFERENCES tbl_student_classes (student_id, semester_id, course_rn) ON DELETE CASCADE,
	PRIMARY KEY (semester_id, course_rn, day_id, start_time, facilitator, student_id),
	UNIQUE (semester_id, day_id, start_time, student_id)
);

CREATE VIEW view_complete_schedule(semester_id, facilitator, day_id, start_time, end_time, course_rn, room_number, students, class_role) AS
	(SELECT tbl_class_times.semester_id, facilitator, tbl_class_times.day_id, tbl_class_times.start_time,
		tbl_class_times.end_time, tbl_class_times.course_rn, tbl_class_times.room_number, ARRAY(
			SELECT student_id FROM tbl_assigned_students WHERE tbl_schedule.semester_id = tbl_assigned_students.semester_id AND
				tbl_schedule.course_rn = tbl_assigned_students.course_rn AND tbl_schedule.day_id = tbl_assigned_students.day_id AND
				tbl_schedule.start_time = tbl_assigned_students.start_time AND tbl_schedule.facilitator = tbl_assigned_students.facilitator
		), 'F' FROM tbl_schedule, tbl_class_times
		WHERE tbl_schedule.semester_id = tbl_class_times.semester_id AND tbl_schedule.course_rn = tbl_class_times.course_rn AND
		tbl_schedule.day_id = tbl_class_times.day_id AND tbl_schedule.start_time = tbl_class_times.start_time
	UNION
	SELECT tbl_class_times.semester_id, tbl_facilitators.email, tbl_class_times.day_id, tbl_class_times.start_time,
		tbl_class_times.end_time, tbl_class_times.course_rn, tbl_class_times.room_number, NULL, 'T' FROM tbl_class_times, tbl_classes, tbl_professors, tbl_facilitators
		WHERE tbl_facilitators.email = tbl_professors.email AND tbl_professors.professor_id = tbl_classes.professor_id AND
		tbl_classes.semester_id = tbl_class_times.semester_id AND tbl_classes.course_rn = tbl_class_times.course_rn);
