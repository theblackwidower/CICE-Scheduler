const LOGIN_CHECK_INTERVAL = 30000; //milliseconds
var is_login_popup = false;

/*
check_login:
Will check if the user is still logged in, and their session has not expired. If they have been logged out,
display a login popup box.
*/
function check_login()
{
	if (!is_login_popup)
	{
		var ajaxRequest;
		if (window.XMLHttpRequest)
			ajaxRequest = new XMLHttpRequest();
		else
			ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");

		ajaxRequest.onreadystatechange = function()
		{
			if(ajaxRequest.readyState === 4 && ajaxRequest.status === 200)
			{
				if (!ajaxRequest.response.is_logged_in)
					login_popup();
			}
		}

		ajaxRequest.responseType = "json"

		ajaxRequest.open("GET", "ajax/login/check-login.php");
		ajaxRequest.send();
	}
}

/*
login_popup:
Displays a login popup box in the middle of the screen.
*/
function login_popup()
{
	is_login_popup = true;
	var clickShield = document.createElement("div");
	clickShield.id = "click_shield";
	document.body.appendChild(clickShield);

	var loginPopup = document.createElement("div");
	loginPopup.id = "login_popup";

	clickShield.appendChild(loginPopup);

	loginPopup.innerHTML = LOADING_IMAGE_WHITE;

	var ajaxRequest;
	if (window.XMLHttpRequest)
		ajaxRequest = new XMLHttpRequest();
	else
		ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");

	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState === 4 && ajaxRequest.status === 200)
			loginPopup.innerHTML = ajaxRequest.responseText;
	}

	ajaxRequest.open("GET", "ajax/login/login-popup.php");
	ajaxRequest.send();
}

/*
ajax_login:
form: form object containing all login information
Will process login information, and if successful, log the user in without refreshing the page.
*/
function ajax_login(form)
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
			if (response.login_successful)
			{
				document.getElementById("login_popup").innerHTML = "<h1>Login Successful</h1>";
				setTimeout(function ()
				{
					document.body.removeChild(document.getElementById("click_shield"));
				}	, 2000);
				is_login_popup = false;
			}
			else
				messageBox.innerHTML = response.message;
		}
	}

	messageBox.innerHTML = LOADING_IMAGE_WHITE;

	ajaxRequest.responseType = "json"
	formData = new FormData(form);

	ajaxRequest.open("POST", "ajax/login/login-post.php");
	ajaxRequest.send(formData);
}
