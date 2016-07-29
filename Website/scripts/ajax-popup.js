/*
ajax_submit:
form: form object containing all information to submit
Will submit all information, and if process is successful, will return message saying so before closing
popup box. If there are errors, they will be displayed.
*/
function ajax_submit(form)
{
	var ajaxRequest;
	if (window.XMLHttpRequest)
		ajaxRequest = new XMLHttpRequest();
	else
		ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");

	var messageBox = form.parentNode.getElementsByClassName('popup_message')[0];

	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState === 4 && ajaxRequest.status === 200)
		{
			var response = ajaxRequest.response;
			if (response.success)
			{
				/*formItems = form.getElementsByTagName('input');
				for (var i = 0; i < formItems.length; i++)
					formItems[i].disabled = true;*/

				var popup_casing = form.parentNode.parentNode;
				form.parentNode.innerHTML = "<h1>Success!</h1>";

				//populate autocomplete box with new information
				var fields = document.getElementById(response.field + "_auto_complete_casing").getElementsByTagName('input');
				for (var i = 0; i < fields.length; i++)
				{
					if (fields[i].type === 'hidden')
						fields[i].value = response.id;
					else if (fields[i].type === 'text')
					{
						fields[i].value = response.display;
						fields[i].classList.add('set');
						fields[i].focus();
					}
				}

				//close popup after delay
				setTimeout(function ()
				{
					popup_casing.innerHTML = '';
				}, 1000);
			}
			else
				messageBox.innerHTML = response.message;
		}
	}

	messageBox.innerHTML = LOADING_IMAGE_WHITE;

	ajaxRequest.responseType = "json"

	formData = new FormData(form);

	ajaxRequest.open("POST", "ajax/autocomplete/add-popup-post.php");
	ajaxRequest.send(formData);
}

/*
close_popup:
casing: perminant casing that contains the popup and provides a clickable background
event: click event that causes the closure
Closes popup box, confirming that it only closes when the casing is cliked, not its contents
*/
function close_popup(casing, event)
{
	if (event.target === event.currentTarget)
		casing.innerHTML = "";
}


/*
new_popup:
id: unique word defining the popup
data: url identifying source of popup contents
Generates a new popup box
*/
function new_popup(id, data)
{
	var casing = document.getElementById(id + '_popup');

	//build empty popup box with identifiable close button
	casing.innerHTML =
	'<div class="popup_box"></div><span class="popup_close" onclick="close_popup(this.parentNode, event);">Close</a>'

	var popupBox = casing.getElementsByClassName('popup_box')[0];

	popupBox.innerHTML = LOADING_IMAGE_WHITE;

	var ajaxRequest;
	if (window.XMLHttpRequest)
		ajaxRequest = new XMLHttpRequest();
	else
		ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");

	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState === 4 && ajaxRequest.status === 200)
		{
			popupBox.innerHTML = ajaxRequest.responseText;
			var contents = casing.getElementsByTagName('input');
			//sets focus to first form element
			if (contents.length > 1)
				contents[1].focus();
		}
	}

	ajaxRequest.open("GET", "ajax/" + data);
	ajaxRequest.send();
}
