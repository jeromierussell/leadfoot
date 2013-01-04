// GLOBALS
var messageboard_form;
var messageboard_submit;
var messageboard_name;
var messageboard_message;
var messageboard_posts;
var messageboard_status;

window.onload = setUpMessageBoard;

function setUpMessageBoard()
{
	messageboard_form = document.getElementById('messageboard_form');
	messageboard_submit = document.getElementById('messageboard_submit');
	messageboard_name = document.getElementById('messageboard_name');
	messageboard_message = document.getElementById('messageboard_message');
	messageboard_posts = document.getElementById('posts');
	messageboard_form.action = "javascript:addMessageBoard()";
}

function addMessageBoard()
{	
	var name = messageboard_name.value;
	var message = messageboard_message.value;
	name = escape(name);
	message = escape(message);
	var request = getXMLHttpRequestObject();
	messageboard_submit.disabled = "disabled";
	messageboard_submit.value = "Posting...";
	request.open('POST', 'add_messageboard.php?t=' + new Date().getTime());
	request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	request.onreadystatechange = function()
	{
		if(request.readyState == 4)
		{
			messageboard_submit.disabled = "";
			messageboard_submit.value = "Post";
			messageboard_posts.innerHTML = request.responseText;
		}
	}
	var params = "name=" + name + "&message=" + message;
	request.send(params);
}

function getXMLHttpRequestObject()
{
	if(window.XMLHttpRequest)
	{	return new XMLHttpRequest();
	}
	else if(window.ActiveXObject)
	{	return new ActiveXObject('MICROSOFT.XMLHTTP');
	}
	else
	{	alert('Your browser does not support ajax!');
	}
}