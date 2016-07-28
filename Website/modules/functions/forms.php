<?php
/*
form_ajax_search:
id: type of records to search for.
Builds AJAX search field and search results box.
*/
function form_ajax_search($id)
{
	echo '
	<form id="ajax_search" onsubmit="return false;">
		<label for="search_box">Search</label>
		<input name="search" id="search_box" type="text" value="" size="20" maxlength="255"
			oninput="ajax_search(this, \''.$id.'\');" />
	</form>
	<div class="search_results">';

	if ($id == 'users')
		$all_items = get_all_users();
	else if ($id == 'facilitators')
		$all_items = get_all_facilitators();
	else if ($id == 'inactive-facilitators')
		$all_items = get_inactive_facilitators();
	else if ($id == 'professors')
		$all_items = get_all_professors();
	else if ($id == 'inactive-professors')
		$all_items = get_inactive_professors();
	else if ($id == 'students')
		$all_items = get_all_students();
	else if ($id == 'unregistered-students')
		$all_items = get_all_unregistered_students(get_default_semester());
	else if ($id == 'registered-students')
		$all_items = get_all_registered_students(get_default_semester());
	else if ($id == 'inactive-students')
		$all_items = get_inactive_students();
	else if ($id == 'courses')
		$all_items = get_all_courses();
	else if ($id == 'inactive-courses')
		$all_items = get_inactive_courses();

	if (ends_with($id, 'facilitators'))
		$id = 'facilitators';
	else if (ends_with($id, 'professors'))
		$id = 'professors';
	else if (ends_with($id, 'students'))
		$id = 'students';
	else if (ends_with($id, 'courses'))
		$id = 'courses';

	display_search_results($id, $all_items);

	echo '</div>';/*
	<script type="text/javascript">
	<!--
		ajax_search(document.getElementById('search_box'), 'students');
	//-->
	</script>';//*/
}

/*
form_open_post:
Builds the opening form tag for a 'post' submission.
*/
function form_open_post()
{
	echo '
	<form method="post" action="'.$_SERVER['PHP_SELF'].
	((count($_GET) > 0)?'?'.http_build_query($_GET):'').
	'" onreset="reset_form(this); return false;">';
}

/************
* READ ONLY *
************/
/*
form_back_button:
url: address for previous page
Display link to go to previous page.
*/
function form_back_button($url)
{
	echo '
	<li>
		<a href="'.$url.'">Back</a>
	</li>';
	echo '</ul><ul>';
}

/*
form_read_only:
id: name of field
label: human readable name of field
value: value of field
Display read only value, essential to record.
*/
function form_read_only($id, $label, $value)
{
	echo '
	<li>
		<label for="'.$id.'">'.$label.'</label>
		<span>';
			if ($id == 'day_id')
				echo get_day_name($value);
			else if ($id == 'facilitator')
				echo get_facilitator_name($value, NAME_FORMAT_LAST_NAME_FIRST);
			else if ($id == 'role_id')
				echo get_role_name($value);
			else
				echo $value;
		echo '
		</span>
		<input name="'.$id.'" id="'.$id.'" type="hidden" value="'.$value.'" />
	</li>';
}

/*
form_read_only_list:
id: name of field
label: human readable name of field
items: items to display
param: field to display in item list
Display multiple read only values
*/
function form_read_only_list($id, $label, $items, $param)
{
	echo '
	<li>
		<label>'.$label.'</label>
		<ul>';
			foreach ($items as $value)
			{
				echo '<li>';
				if (isset($param))
					$value = $value[$param];
				if ($id == 'students')
					echo get_student_name($value, NAME_FORMAT_LAST_NAME_FIRST);
				else
					echo $value;
				echo '</li>';
			}
		echo '
		</ul>
	</li>';
}

/*
form_read_only_time:
start_time: start time (24h) to display
end_time: end time (24h) to display
Display read only time span
*/
function form_read_only_time($start_time, $end_time)
{
	echo '
	<li>
		<label for="start_time">Time</label>
		<span>'.format_time($start_time).' - '.format_time($end_time).'</span>
		<input name="start_time" id="start_time" type="hidden" value="'.$start_time.'" />
		<input name="end_time" id="end_time" type="hidden" value="'.$end_time.'" />
	</li>';
}

/*******
* TEXT *
*******/
/*
form_text_box:
id: name of field
label: human readable name of field
value: value of field
Display text box.
*/
function form_text_box($id, $label, $value)
{
	switch ($id)
	{
		case 'email':
		case 'new_email':
			$size = 30; $maxlength = MAX_EMAIL_LENGTH;
			break;
		case 'login_email':
			$size = 20; $maxlength = MAX_EMAIL_LENGTH;
			break;
		case 'first_name':
		case 'last_name':
		case 'course_name':
			$size = 20; $maxlength = MAX_NAME_FIELD_LENGTH;
			break;
		case 'student_id':
			$size = 10; $maxlength = 9;
			break;
		case 'course_rn':
			$size = 10; $maxlength = 5;
			break;
		case 'course_code':
			$size = 10; $maxlength = 10;
			break;
		case 'room_number':
			$size = 10; $maxlength = 12;
			break;
	}
	echo '
	<li class="text_box_casing">
		<label for="'.$id.'">'.$label.'</label>
		<input name="'.$id.'" id="'.$id.'" type="text" value="'.$value.'" size="'.$size.'"
			maxlength="'.$maxlength.'" class="form_item" />
	</li>';
}

/*
form_password_box:
id: name of field
label: human readable name of field
Display password box.
*/
function form_password_box($id, $label)
{
	echo '
	<li class="password_box_casing">
		<label for="'.$id.'">'.$label.'</label>
		<input name="'.$id.'" id="'.$id.'" type="password" value="" size="20" class="form_item" />
	</li>';
}

/*
form_autocomplete_box:
id: name of field
label: human readable name of field
field: category of data to look up
value: value of field
Display autocomplete-enabled text box
*/
function form_autocomplete_box($id, $label, $field, $value)
{
	if ($field == 'professor')
		$size = 20;
	else
		$size = 10;

	echo '
	<li id="'.$field.'_auto_complete_casing" class="auto_complete_casing">
		<label for="'.$id.'">'.$label.'</label>
		<input name="'.$field.'_search" id="'.$id.'" type="text" value="';
			if ($value != "")
			{
				if ($field == 'professor')
					echo get_professor_name($value, NAME_FORMAT_LAST_NAME_FIRST);
				else
					echo $value;
				echo '" class="set';
			}
			echo '" size="'.$size.'" maxlength="255"
			oninput="auto_complete(this, \''.$field.'\');"
			onkeydown="auto_complete_keyboard_controls(event);"
			onblur="close_auto_complete(this.parentNode);" class="form_item" />
		<div class="auto_complete_box"></div>
		<input name="'.$id.'" type="hidden" value="'.$value.'" />
		<div class="auto_complete_default">'.$value.'</div>
	</li>';
}

/*
popup_casing:
id: name of field
Create casing for potential popup box.
*/
function popup_casing($id)
{
	echo '<div class="popup_casing" id="'.$id.'_popup" onclick="close_popup(this, event);"></div>';
}

/**********
* OPTIONS *
**********/
/*
form_drop_down_box:
id: name of field
label: human readable name of field
selected: id of item selected
Display drop down box
*/
function form_drop_down_box($id, $label, $selected)
{
	echo '
	<li class="ddl_casing">
		<label for="'.$id.'">'.$label.'</label>
		<select name="'.$id.'" id="'.$id.'" class="form_item">';
			switch ($id)
			{
				case 'day_id':
					echo '<option value=""></option>';
					print_ddl(get_all_days(), 'day_id', 'day_name', $selected);
					break;
				case 'campus_id':
					print_ddl(get_all_campuses(), 'campus_id', 'campus_name', $selected);
					break;
				case 'role_id':
					print_ddl(get_all_roles(), 'role_id', 'description', $selected);
					break;
				case 'start_time':
					print_time_ddl(START_SCHEDULE, END_SCHEDULE - 1, $selected);
					break;
				case 'end_time':
					print_time_ddl(START_SCHEDULE + 1, END_SCHEDULE, $selected);
					break;
			}
		echo '
		</select>
	</li>';
}

/*
form_time_ddl:
start_time: start time (24h) currently selected
end_time: end time (24h) currently selected
Display drop down boxes for inputting time span
*/
function form_time_ddl($start_time, $end_time)
{
	echo '
	<li class="ddl_casing">
		<label for="start_time">Start Time</label>
		<select name="start_time" id="start_time" oninput="control_time(this)" class="form_item">';
			print_time_ddl(START_SCHEDULE, END_SCHEDULE - 1, $start_time);
		echo '
		</select>
	</li>
	<li class="ddl_casing">
		<label for="end_time">End Time</label>
		<select name="end_time" id="end_time" oninput="control_time(this)" class="form_item">';
			print_time_ddl(START_SCHEDULE + 1, END_SCHEDULE, $end_time);
		echo '
		</select>
	</li>';

}

/*
form_checkbox:
id: name of field
label: human readable name of field
value: is box selected
Display checkbox.
*/
function form_checkbox($id, $label, $value)
{
	if (is_string($value))
		$value = ($value == 'true');
	echo '
	<li class="checkbox_casing">
		<label for="'.$id.'">'.$label.'</label>
		<input name="'.$id.'" id="'.$id.'" type="checkbox" value="true"'.($value?' checked="checked"':'').' class="form_item"/>
	</li>';
}

/**********
* BUTTONS *
**********/
/*
form_submit_buttons:
type: label for submit button.
Display form submission button and reset button.
*/
function form_submit_buttons($type)
{
	echo '
	<li>
		<input type="submit" value="'.$type.'" class="form_item" />
		<input type="reset" value="Reset" class="form_item" />
	</li>';
}

/*
form_question_buttons:
url: url of 'no' option
get: get array for 'no' option
Display pair of buttons saying, 'Yes' and 'No.'
	The 'Yes' button submits the form, the 'No' button goes back to the previous page.
*/
function form_question_buttons($url, $get)
{
	echo '
	<li>
		<input type="submit" value="Yes" class="form_item" />
		<input type="button" value="No"
		onclick="window.location.href=\''.$url;
		if (count($get) > 0)
			echo '?'.http_build_query($get);
		echo '\';" class="form_item" />
	</li>';
}

/**************
* DDL OPTIONS *
**************/
/*
print_time_ddl:
start_time: earliest time (24h) available
end_time: latest time (24h) available
Display drop down options for a dropdown box for selecting time
*/
function print_time_ddl($start, $end, $selected)
{
	for ($i = $start; $i <= $end; $i++)
	{
		echo '<option value="'.$i;
		if ($i == $selected)
			echo '" selected="selected';
		echo '">'.format_time($i).'</option>';
	}
}

/*
print_month_ddl:
Display drop down options for a dropdown box for selecting a month
*/
function print_month_ddl()
{
	$months = array(
		'01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
		'05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
		'09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
	);

	echo '<option value="00"></option>';
	foreach ($months as $value => $name)
	{
		echo '<option value="'.$value.'">'.$name.'</option>';
	}
}

/*
print_ddl:
options: all options in drop down list, as associative array
value: name of field holding value
display: name of field holding display name
selected: value of selected item
Display drop down options for a dropdown box
*/
function print_ddl($options, $value, $display, $selected)
{
	foreach ($options as $item)
	{
		echo '<option value="'.$item[$value];
		if ($item[$value] == $selected)
			echo '" selected="selected';
		echo '">'.$item[$display].'</option>';
	}
}
