<?php
/*
search_for_classes_with_unpaired_students:
semester_id: Semester id
returns all classes with students that haven't been paired with facilitators in associative array
*/
function search_for_classes_with_unpaired_students($semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT DISTINCT tbl_class_times.course_rn, day_id, start_time, end_time
		FROM tbl_class_times, tbl_student_classes
		WHERE tbl_class_times.semester_id = :semester AND
			tbl_class_times.semester_id = tbl_student_classes.semester_id AND
			tbl_class_times.course_rn = tbl_student_classes.course_rn AND
			student_id NOT IN (SELECT student_id FROM tbl_assigned_students
				WHERE tbl_assigned_students.semester_id = :semester AND
					tbl_class_times.course_rn = tbl_assigned_students.course_rn AND
					tbl_class_times.day_id = tbl_assigned_students.day_id AND
					tbl_class_times.start_time = tbl_assigned_students.start_time)
		ORDER BY day_id, start_time, course_rn');
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
search_for_overbooked_classes:
semester_id: Semester id
returns all classes with more facilitators than necessary in associative array
*/
function search_for_overbooked_classes($semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT DISTINCT course_rn, day_id, start_time, end_time
		FROM tbl_class_times WHERE tbl_class_times.semester_id = :semester AND
			(SELECT COUNT(student_id) FROM tbl_student_classes
					WHERE semester_id = :semester AND
					tbl_class_times.course_rn = tbl_student_classes.course_rn) <=
			(SELECT COUNT(facilitator) - 1 FROM tbl_schedule
					WHERE tbl_schedule.semester_id = tbl_class_times.semester_id AND
						tbl_schedule.course_rn = tbl_class_times.course_rn AND
						tbl_schedule.day_id = tbl_class_times.day_id AND
						tbl_schedule.start_time = tbl_class_times.start_time) * '.MAX_STUDENTS_PER_FACILITATOR.'
		ORDER BY day_id, start_time, course_rn');
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
unassigned_students_by_class:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
returns all students that haven't been paired with facilitators during class in associative array
*/
function unassigned_students_by_class($semester_id, $course_rn, $day_id, $start_time)
{
	global $conn;
	$stmt = $conn->prepare('SELECT student_id FROM tbl_class_times, tbl_student_classes
		WHERE tbl_class_times.semester_id = :semester AND
			tbl_class_times.course_rn = :crn AND day_id = :day AND start_time = :time AND
			tbl_class_times.semester_id = tbl_student_classes.semester_id AND
			tbl_class_times.course_rn = tbl_student_classes.course_rn
		EXCEPT
			SELECT student_id FROM tbl_assigned_students
				WHERE semester_id = :semester AND
				course_rn = :crn AND day_id = :day AND start_time = :time
		ORDER BY student_id');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	return execute_fetch_all($stmt);
}

/*
count_students_in_class:
semester_id: Semester id
course_rn: CRN, course registration number
returns number of students in class
*/
function count_students_in_class($semester_id, $course_rn)
{
	global $conn;
	$stmt = $conn->prepare('SELECT COUNT(student_id) FROM tbl_student_classes
		WHERE semester_id = :semester AND course_rn = :crn');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	return execute_fetch_param($stmt, 'count');
}

/*
count_assigned_students:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
facilitator: facilitator email
returns number of students assigned to facilitator during class
*/
function count_assigned_students($semester_id, $course_rn, $day_id, $start_time, $facilitator)
{
	global $conn;
	$stmt = $conn->prepare('SELECT COUNT(student_id) FROM tbl_assigned_students
		WHERE semester_id = :semester AND day_id = :day AND start_time = :time AND
			course_rn = :crn AND facilitator = :facilitator');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':facilitator', $facilitator);
	return execute_fetch_param($stmt, 'count');
}

/*
count_assigned_facilitators:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
returns number of facilitators assigned to class
*/
function count_assigned_facilitators($semester_id, $course_rn, $day_id, $start_time)
{
	global $conn;
	$stmt = $conn->prepare('SELECT COUNT(facilitator) FROM tbl_schedule
		WHERE semester_id = :semester AND day_id = :day AND start_time = :time AND
			course_rn = :crn');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	return execute_fetch_param($stmt, 'count');
}

/*
get_booked_facilitators:
semester_id: Semester id
day_id: Id of day
start_time: Time class starts (24h)
course_rn: CRN, course registration number
returns all facilitators assigned to class in associative array
*/
function get_booked_facilitators($semester_id, $day_id, $start_time, $course_rn)
{
	global $conn;
	$stmt = $conn->prepare('SELECT facilitator
			FROM tbl_schedule WHERE semester_id = :semester AND day_id = :day AND
			start_time = :time AND course_rn = :crn ORDER BY facilitator');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':crn', $course_rn);
	return execute_fetch_all($stmt);
}

/*
is_facilitator_booked:
semester_id: Semester id
day_id: Id of day
start_time: Time class starts (24h)
course_rn: CRN, course registration number
facilitator: facilitator email
returns true if facilitator is assigned to class
*/
function is_facilitator_booked($semester_id, $day_id, $start_time, $course_rn, $facilitator)
{
	global $conn;
	$stmt = $conn->prepare('SELECT facilitator
			FROM tbl_schedule WHERE semester_id = :semester AND day_id = :day AND
			start_time = :time AND course_rn = :crn AND facilitator = :facilitator');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':facilitator', $facilitator);
	return execute_exists($stmt);
}

/*
get_assigned_students:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
facilitator: facilitator email
returns all students assigned to facilitator during class in associative array
*/
function get_assigned_students($semester_id, $course_rn, $day_id, $start_time, $facilitator)
{
	global $conn;
	$stmt = $conn->prepare('SELECT student_id
			FROM tbl_assigned_students WHERE semester_id = :semester AND day_id = :day AND
			start_time = :time AND course_rn = :crn AND facilitator = :facilitator
			ORDER BY student_id');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':facilitator', $facilitator);
	return execute_fetch_all($stmt);
}

/*
schedule_facilitator:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
facilitator: facilitator email
Assign facilitator to class
returns true if successful
*/
function schedule_facilitator($semester_id, $course_rn, $day_id, $start_time, $facilitator)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_schedule (semester_id, course_rn, day_id, start_time, facilitator)
	VALUES (:semester, :crn, :day, :time, :facilitator)');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':facilitator', $facilitator);
	return execute_no_data($stmt);
}

/*
unschedule_facilitator:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
facilitator: facilitator email
remove facilitator from class
returns true if successful
*/
function unschedule_facilitator($semester_id, $course_rn, $day_id, $start_time, $facilitator)
{
	global $conn;
	$stmt = $conn->prepare('DELETE FROM tbl_schedule WHERE semester_id = :semester AND
		course_rn = :crn AND day_id = :day AND start_time = :time AND facilitator = :facilitator');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':facilitator', $facilitator);
	return execute_no_data($stmt);
}

/*
assign_student:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
facilitator: facilitator email
student_id: Student's unique banner id
assign student to facilitator during class
returns true if successful
*/
function assign_student($semester_id, $course_rn, $day_id, $start_time, $facilitator, $student_id)
{
	global $conn;
	$stmt = $conn->prepare('INSERT INTO tbl_assigned_students
		(semester_id, course_rn, day_id, start_time, facilitator, student_id)
		VALUES (:semester, :crn, :day, :time, :facilitator, :student)');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':facilitator', $facilitator);
	$stmt->bindValue(':student', $student_id);
	return execute_no_data($stmt);
}

/*
unassign_student:
semester_id: Semester id
course_rn: CRN, course registration number
day_id: Id of day
start_time: Time class starts (24h)
facilitator: facilitator email
student_id: Student's unique banner id
remove student from facilitator during class
returns true if successful
*/
function unassign_student($semester_id, $course_rn, $day_id, $start_time, $facilitator, $student_id)
{
	global $conn;
	$stmt = $conn->prepare('DELETE FROM tbl_assigned_students
		WHERE semester_id = :semester AND course_rn = :crn AND day_id = :day AND
			start_time = :time AND facilitator = :facilitator AND student_id = :student');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':crn', $course_rn);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':time', $start_time);
	$stmt->bindValue(':facilitator', $facilitator);
	$stmt->bindValue(':student', $student_id);
	return execute_no_data($stmt);
}

/*
all_scheduled_classes:
semester_id: Semester id
returns all classes with assigned facilitators in associative array
*/
function all_scheduled_classes($semester_id)
{
	global $conn;
	$stmt = $conn->prepare("SELECT DISTINCT course_rn, day_id, start_time, end_time
			FROM view_complete_schedule
			WHERE semester_id = :semester AND
				class_role = '".SCHEDULE_ROLE_FACILITATE."'
			ORDER BY day_id, start_time, course_rn");
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
get_facilitator_schedule:
email: facilitator email
semester_id: Semester id
returns the full schedule of facilitator in associative array
*/
function get_facilitator_schedule($email, $semester_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT day_id, start_time, end_time, view_complete_schedule.course_rn,
			room_number, course_code, professor_id, students, class_role
			FROM view_complete_schedule, tbl_classes
			WHERE facilitator = :email AND view_complete_schedule.semester_id = :semester AND
				view_complete_schedule.semester_id = tbl_classes.semester_id AND
				view_complete_schedule.course_rn = tbl_classes.course_rn');
	$stmt->bindValue(':email', $email);
	$stmt->bindValue(':semester', $semester_id);
	return execute_fetch_all($stmt);
}

/*
search_available_facilitators:
semester_id: Semester id
day_id: Id of day
start_time: Time class starts (24h)
end_time: Time class ends (24h)
campus_id: Id of campus
returns emails of all facilitators available for booking in associative array
*/
function search_available_facilitators($semester_id, $day_id, $start_time, $end_time, $campus_id)
{
	global $conn;
	$stmt = $conn->prepare('SELECT email FROM tbl_facilitators
			WHERE is_active
		EXCEPT --direct conflict
			SELECT DISTINCT facilitator FROM view_complete_schedule WHERE
				semester_id = :semester AND day_id = :day AND
				end_time > :start AND start_time < :end
		EXCEPT --4 hr stretch limit
			SELECT email FROM tbl_facilitators WHERE
				count_neighbouring_hours(:semester, :day, :start, :end, email) >
					('.MAX_HOURS_STRAIGHT.' - (:end - :start))
		EXCEPT --8 hr day cap
			SELECT DISTINCT facilitator FROM view_complete_schedule WHERE
				semester_id = :semester AND day_id = :day AND
				(start_time < (:end - '.MAX_HOURS_IN_DAY.') OR
				end_time > (:start + '.MAX_HOURS_IN_DAY.'))
		EXCEPT --traveltime
			SELECT DISTINCT facilitator FROM view_complete_schedule, tbl_rooms WHERE
				semester_id = :semester AND day_id = :day AND
				view_complete_schedule.room_number = tbl_rooms.room_number AND campus_id <> :campus AND
				((start_time < (:end + '.MIN_HOURS_TRAVEL_TIME.') AND start_time > :start) OR
				(end_time > (:start - '.MIN_HOURS_TRAVEL_TIME.') AND end_time < :end))
		ORDER BY email');
	$stmt->bindValue(':semester', $semester_id);
	$stmt->bindValue(':day', $day_id);
	$stmt->bindValue(':start', $start_time);
	$stmt->bindValue(':end', $end_time);
	$stmt->bindValue(':campus', $campus_id);
	return execute_fetch_all($stmt);
}
