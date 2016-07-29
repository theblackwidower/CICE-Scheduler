const LOADING_IMAGE_WHITE = '<img alt="Loading..." src="images/loading_white.gif" class="loading" />';
const LOADING_IMAGE_YELLOW = '<img alt="Loading..." src="images/loading_yellow.gif" class="loading" />';
const MAX_CLASS_LENGTH = 4;

/*
sidebar_click:
event: click event on sidebar menu category
Will open the selected category
*/
function sidebar_click(event)
{
	//check if it's the menu item, or its contents
	if (event.target === event.currentTarget)
		event.currentTarget.classList.toggle('open');
}

/*
reset_form:
form: form object to reset
Ensures form is fully reset, for the default reset function doesn't reset everything.
*/
function reset_form(form)
{
	var casings;

	casings = form.getElementsByClassName('text_box_casing');
	for (var i = 0; i < casings.length; i++)
	{
		var box = casings[i].getElementsByTagName('input')[0];
		box.value = box.defaultValue;
	}

	casings = form.getElementsByClassName('password_box_casing');
	for (var i = 0; i < casings.length; i++)
		casings[i].getElementsByTagName('input')[0].value = '';

	casings = form.getElementsByClassName('auto_complete_casing');
	for (var i = 0; i < casings.length; i++)
	{
		casings[i].getElementsByClassName('auto_complete_box')[0].innerHTML = '';

		var fields = casings[i].getElementsByTagName('input');
		var entryBox;
		var hiddenItem;
		for (var j = 0; j < fields.length; j++)
		{
			if (fields[j].type === 'text')
				entryBox = fields[j];
			else if (fields[j].type === 'hidden')
				hiddenItem = fields[j];
		}

		var defaultValue = casings[i].getElementsByClassName('auto_complete_default')[0].innerHTML;

		if (defaultValue === '')
		{
			entryBox.classList.remove('set');
			entryBox.value = '';
			hiddenItem.value = '';
		}
		else
		{
			entryBox.classList.add('set');
			entryBox.value = entryBox.defaultValue;
			hiddenItem.value = defaultValue;
		}
	}

	casings = form.getElementsByClassName('ddl_casing');
	for (var i = 0; i < casings.length; i++)
	{
		var options = casings[i].getElementsByTagName('select')[0].options;
		for (var j = 0; j < options.length; j++)
			if (options[j].defaultSelected)
				options[j].selected = true;
	}

	casings = form.getElementsByClassName('checkbox_casing');
	for (var i = 0; i < casings.length; i++)
	{
		var box = casings[i].getElementsByTagName('input')[0];
		box.checked = box.defaultChecked;
	}
}

/*
default_semester:
source: text box where semester id is entered
Will automatically generate the default month and year for the semester from the id
*/
function default_semester(source)
{
	if (source.value.substr(0,1).toUpperCase() === 'F')
	{
		document.getElementById('start_month').value = "09";
		document.getElementById('end_month').value = "12";
	}
	else if (source.value.substr(0,1).toUpperCase() === 'W')
	{
		document.getElementById('start_month').value = "01";
		document.getElementById('end_month').value = "04";
	}
	document.getElementById('start_year').value = source.value.substr(1);
	document.getElementById('end_year').value = source.value.substr(1);
}

/*
control_time:
read_only: option box that was just edited
Will automatically control the time, so end time isn't before start time, and a class isn't too long.
	Will only change the time that wasn't just edited.
*/
function control_time(read_only)
{
	var start_time = document.getElementById('start_time');
	var end_time = document.getElementById('end_time');

	if (start_time === read_only)
	{
		if (parseInt(end_time.value, 10) <= parseInt(start_time.value, 10))
			end_time.value = parseInt(start_time.value, 10) + 1;
		else if ((parseInt(end_time.value, 10) - parseInt(start_time.value, 10)) > MAX_CLASS_LENGTH)
			end_time.value = parseInt(start_time.value, 10) + MAX_CLASS_LENGTH;
	}
	else if (end_time === read_only)
	{
		if (parseInt(end_time.value, 10) <= parseInt(start_time.value, 10))
			start_time.value = parseInt(end_time.value, 10) - 1;
		else if ((parseInt(end_time.value, 10) - parseInt(start_time.value, 10)) > MAX_CLASS_LENGTH)
			start_time.value = parseInt(end_time.value, 10) - MAX_CLASS_LENGTH;
	}
}

/*************
* SCHEDULING *
*************/

/*
quick_post_submit:
id: id of relevant record
operation: code word of operation to conduct on record
Will quickly populate a hidden form and submit it.
*/
function quick_post_submit(id, operation)
{
	document.getElementById('selected_id').value = id;
	document.getElementById('operation').value = operation;
	document.getElementById('class_data').submit();
}

/*
quick_schedule:
course_rn: crn of relevant class record
day_id: day class is on
start_time: time class starts
Will quickly populate and submit a hidden form for quick scheduling of facilitators for a specific class.
*/
function quick_schedule(course_rn, day_id, start_time)
{
	document.getElementById('course_rn').value = course_rn;
	document.getElementById('day_id').value = day_id;
	document.getElementById('start_time').value = start_time;
	document.getElementById('quick_add').submit();
}
