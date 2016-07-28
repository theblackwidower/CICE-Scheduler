
INSERT INTO tbl_campuses(campus_id, campus_name) VALUES('O', 'Oshawa');
INSERT INTO tbl_campuses(campus_id, campus_name) VALUES('W', 'Whitby');

INSERT INTO tbl_days(day_id, day_name) VALUES(1, 'Monday');
INSERT INTO tbl_days(day_id, day_name) VALUES(2, 'Tuesday');
INSERT INTO tbl_days(day_id, day_name) VALUES(3, 'Wednesday');
INSERT INTO tbl_days(day_id, day_name) VALUES(4, 'Thursday');
INSERT INTO tbl_days(day_id, day_name) VALUES(5, 'Friday');

INSERT INTO tbl_role(role_id, description) VALUES('A', 'Administrator');
INSERT INTO tbl_role(role_id, description) VALUES('E', 'Data Entry');
INSERT INTO tbl_role(role_id, description) VALUES('F', 'Facilitator');
INSERT INTO tbl_role(role_id, description) VALUES('N', 'Password Change Required');
INSERT INTO tbl_role(role_id, description) VALUES('D', 'Disabled Account');

INSERT INTO tbl_users(email, password, role_id) VALUES ('admin', '$2y$10$ueAfsBzgkB5HNAtMh7Ecq.QqPnZ4gqBh4jcz8VnKd3lhQaZkeCEq6', 'A');
