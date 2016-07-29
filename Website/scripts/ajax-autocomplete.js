/*
auto_complete:
source: input box object that needs to be autocompleted
field: string defining what type of data to call from the database
	professor, course, room, crn
Will display the search results for the autocomplete function below the input box.
*/
function auto_complete(source, field)
{
	var ajaxRequest;
	if (window.XMLHttpRequest)
		ajaxRequest = new XMLHttpRequest();
	else
		ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");

	var autoCompleteBox = source.parentNode.getElementsByClassName('auto_complete_box')[0];

	autoCompleteBox.innerHTML = '<span>' + LOADING_IMAGE_WHITE + '</span>';

	clear_hidden(source);

	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState === 4 && ajaxRequest.status === 200)
			autoCompleteBox.innerHTML = ajaxRequest.responseText;
	}

	ajaxRequest.open("GET", "ajax/autocomplete/search.php?f=" + field + "&q=" + encodeURI(source.value));
	ajaxRequest.send();
}

/*
complete:
source: 'a' (hyperlink) tag that was selected, can be used to find the input box that needs to be filled
id: id of item that has been selected. Normally the displayed name, but can be a hidden surrogate key
Will fill the input box with the selected information.
*/
function complete(source, id)
{
	var display = source.getElementsByClassName('title')[0].innerHTML;
	var fields = source.parentNode.parentNode.getElementsByTagName('input');
	for (var i = 0; i < fields.length; i++)
	{
		if (fields[i].type === 'hidden')
			fields[i].value = id;
		else if (fields[i].type === 'text')
		{
			fields[i].value = display;
			fields[i].classList.add('set');
		}
	}
	source.parentNode.innerHTML = '';
}

/*
close_auto_complete:
parent_li: li tag that contains all elements of the autocomplete object
Will close the autocomplete display box after a slight half-second delay.
*/
function close_auto_complete(parent_li)
{
	setTimeout(function ()
	{
		var autoCompleteBoxes = document.getElementsByClassName('auto_complete_box');

		for (var i = 0; i < autoCompleteBoxes.length; i++)
		{
			if (parent_li.contains(autoCompleteBoxes[i]))
				autoCompleteBoxes[i].innerHTML = '';
		}
	}, 500); //half-second delay
}

/*
clear_hidden:
source: input box object that is being autocompleted
Will clear the hidden input fields that contain information in the event that the main input box is
being edited.
Also, will make it clear that the data in the input box has not been set by autocomplete.
*/
function clear_hidden(source)
{
	var fields = source.parentNode.getElementsByTagName('input');
	for (var i = 0; i < fields.length; i++)
	{
		if (fields[i].type === 'hidden')
			fields[i].value = '';
	}
	source.classList.remove('set');
}

/*
auto_complete_keyboard_controls:
event: keyboard event from within input box that needs to be interpreted.
Will respond to arrow key movements by selecting the next or previous item in the autocomplete box.
Will respond to tabs and enters by placing the currently selected item in the input box, if any item
is currently selected, and moving to the next input field in the tab order, or the previous one
if the shift key is held.
*/
function auto_complete_keyboard_controls(event)
{
	var autoCompleteBox = event.currentTarget.parentNode.getElementsByClassName('auto_complete_box')[0];
	var selected = document.getElementById('auto_complete_selected');
	var success = true;
	if (event.key === "Tab" || event.keyCode === 9 ||
			event.key === "Enter" || event.keyCode === 13)
	{
		//activate selected item
		if (autoCompleteBox.contains(selected))
			selected.click();
		var formItems = event.currentTarget.form.getElementsByClassName('form_item');
		var foundThis = false;
		//go to previous
		if (event.shiftKey)
		{
			for (var i = formItems.length - 1; i >= 0; i--)
			{
				if (formItems[i] === event.currentTarget)
					foundThis = true;
				else if (foundThis && formItems[i].type !== 'hidden')
				{
					formItems[i].focus();
					break;
				}
			}
		}
		//go to next
		else
		{
			for (var i = 0; i < formItems.length; i++)
			{
				if (formItems[i] === event.currentTarget)
					foundThis = true;
				else if (foundThis && formItems[i].type !== 'hidden')
				{
					formItems[i].focus();
					break;
				}
			}
		}
	}
	else if (event.key === "Up" || event.key === "ArrowUp" || event.keyCode === 38)
	{
		if (autoCompleteBox.contains(selected))
		{
			selected.id = "";
			if (autoCompleteBox.firstElementChild === selected)
				autoCompleteBox.lastElementChild.id = "auto_complete_selected";
			else
				selected.previousElementSibling.id = "auto_complete_selected";
		}
		else if (autoCompleteBox.innerHTML !== "")
			autoCompleteBox.lastElementChild.id = "auto_complete_selected";
	}
	else if (event.key === "Down" || event.key === "ArrowDown" || event.keyCode === 40)
	{
		if (autoCompleteBox.contains(selected))
		{
			selected.id = "";
			if (autoCompleteBox.lastElementChild === selected)
				autoCompleteBox.firstElementChild.id = "auto_complete_selected";
			else
				selected.nextElementSibling.id = "auto_complete_selected";
		}
		else if (autoCompleteBox.innerHTML !== "")
			autoCompleteBox.firstElementChild.id = "auto_complete_selected";
	}
	else
		success = false;
	if (success)
		event.preventDefault();
}
