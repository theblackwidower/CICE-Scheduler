DROP TABLE IF EXISTS tbl_campuses CASCADE;
DROP TABLE IF EXISTS tbl_days CASCADE;
DROP TABLE IF EXISTS tbl_semesters CASCADE;

CREATE TABLE tbl_campuses(
	campus_id CHAR(1) PRIMARY KEY,
	campus_name VARCHAR(10) NOT NULL
	CHECK (UPPER(campus_id) = campus_id)
);
CREATE TABLE tbl_days(
	day_id SMALLINT PRIMARY KEY,
	day_name VARCHAR(10) NOT NULL
);
CREATE TABLE tbl_semesters(
	semester_id CHAR(5) PRIMARY KEY,
	start_date DATE NOT NULL,
	end_date DATE NOT NULL,
	CHECK (UPPER(semester_id) = semester_id),
	CHECK (start_date < end_date)
);
