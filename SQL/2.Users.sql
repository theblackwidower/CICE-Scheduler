DROP TABLE IF EXISTS tbl_users CASCADE;
DROP TABLE IF EXISTS tbl_role CASCADE;

CREATE TABLE tbl_role(
	role_id CHAR(1) PRIMARY KEY,
	description VARCHAR(30) NOT NULL,
	CONSTRAINT reserved_role_id CHECK (role_id <> 'P' AND role_id <> 'U'), --reserved for public access and all users
	CHECK (UPPER(role_id) = role_id)
);

CREATE TABLE tbl_users(
	email VARCHAR(255) PRIMARY KEY,
	password VARCHAR(255) NOT NULL,
	role_id CHAR(1) NOT NULL REFERENCES tbl_role (role_id),
	force_new_password BOOLEAN NOT NULL DEFAULT true
);
