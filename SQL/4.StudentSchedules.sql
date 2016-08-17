DROP TABLE IF EXISTS tbl_rooms CASCADE;
DROP TABLE IF EXISTS tbl_courses CASCADE;
DROP TABLE IF EXISTS tbl_professors CASCADE;
DROP TABLE IF EXISTS tbl_classes CASCADE;
DROP TABLE IF EXISTS tbl_class_times CASCADE;
DROP TABLE IF EXISTS tbl_students CASCADE;
DROP TABLE IF EXISTS tbl_student_classes CASCADE;

CREATE TABLE tbl_students(
	student_id CHAR(9) PRIMARY KEY,
	first_name VARCHAR(30) NOT NULL,
	last_name VARCHAR(30) NOT NULL,
	is_active BOOLEAN NOT NULL DEFAULT true
);

CREATE TABLE tbl_courses(
	course_code VARCHAR(10) PRIMARY KEY,
	course_name VARCHAR(30) NOT NULL,
	is_active BOOLEAN NOT NULL DEFAULT true,
	CHECK (UPPER(course_code) = course_code)
);

CREATE TABLE tbl_professors(
	professor_id SERIAL PRIMARY KEY,
	first_name VARCHAR(30) NOT NULL,
	last_name VARCHAR(30) NOT NULL,
	email VARCHAR(255) UNIQUE,
	is_active BOOLEAN NOT NULL DEFAULT true,
	CHECK (LOWER(email) = email)
);

CREATE TABLE tbl_classes(
	semester_id CHAR(5) NOT NULL REFERENCES tbl_semesters (semester_id),
	course_rn CHAR(5) NOT NULL,
	course_code VARCHAR(10) NOT NULL REFERENCES tbl_courses (course_code),
	professor_id INTEGER REFERENCES tbl_professors (professor_id),
	PRIMARY KEY (semester_id, course_rn)
);

CREATE TABLE tbl_student_classes(
	student_id CHAR(9) NOT NULL REFERENCES tbl_students (student_id),
	semester_id CHAR(5) NOT NULL,
	course_rn CHAR(5) NOT NULL,
	FOREIGN KEY (semester_id, course_rn) REFERENCES tbl_classes (semester_id, course_rn),
	PRIMARY KEY (student_id, semester_id, course_rn)
	--Possibly restrict to a max number of courses one can be enrolled in
);

CREATE TABLE tbl_rooms(
	room_number VARCHAR(12) PRIMARY KEY,
	campus_id CHAR(1) NOT NULL REFERENCES tbl_campuses (campus_id),
	CHECK (UPPER(room_number) = room_number)
);


CREATE TABLE tbl_class_times(
	semester_id CHAR(5) NOT NULL,
	course_rn CHAR(5) NOT NULL,
	day_id SMALLINT NOT NULL REFERENCES tbl_days (day_id),
	start_time SMALLINT NOT NULL,
	end_time SMALLINT NOT NULL,
	room_number VARCHAR(12) NOT NULL REFERENCES tbl_rooms (room_number),
	FOREIGN KEY (semester_id, course_rn) REFERENCES tbl_classes (semester_id, course_rn),
	PRIMARY KEY (semester_id, course_rn, day_id, start_time),
	UNIQUE (semester_id, room_number, day_id, start_time),
	CHECK (start_time < end_time),
	CHECK (end_time - start_time <= 4)
);
