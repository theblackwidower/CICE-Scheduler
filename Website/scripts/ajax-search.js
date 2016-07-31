/*
ajax_search:
searchBox: search box containing the search criteria
name: type of data to search for
Will search for records using AJAX and populate the results box
*/
function ajax_search(searchBox, name)
{
	var list = document.getElementsByClassName('search_results')[0];

	list.innerHTML = LOADING_IMAGE_YELLOW;

	var ajaxRequest;
	if (window.XMLHttpRequest)
		ajaxRequest = new XMLHttpRequest();
	else
		ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");

	ajaxRequest.onreadystatechange = function()
	{
		if(ajaxRequest.readyState === 4 && ajaxRequest.status === 200)
			list.outerHTML = ajaxRequest.responseText;
	}

	ajaxRequest.open("GET", "ajax/search.php?q=" + encodeURI(searchBox.value) + "&f=" + encodeURI(name));
	ajaxRequest.send();
}

/*
search_page:
pageNumber: page number to view
resultsBox: box containing search results, including page navigation
Displays indicated page
*/
function search_page(pageNumber, resultsBox)
{
	resultsBox.classList.remove('view_all');

	var currentPage = resultsBox.getElementsByClassName('page_' + pageNumber)[0];
	//checks if page exists
	if (currentPage !== null)
	{
		var pages = resultsBox.getElementsByClassName('block_list');

		//displays selected page and hides all others
		for (var i = 0; i < pages.length; i++)
		{
			if (pages[i] === currentPage)
				pages[i].classList.remove('hidden');
			else
				pages[i].classList.add('hidden');
		}

		var pageNav = resultsBox.getElementsByClassName('page_nav');

		//loops for each set of nav links
		for (var i = 0; i < pageNav.length; i++)
		{
			var pageLink = pageNav[i].getElementsByClassName('number');

			//disables any buttons on the nav bar that need to be disabled
			for (var j = 0; j < pageLink.length; j++)
			{
				if ((j + 1) === pageNumber)
					pageLink[j].classList.add('selected_page');
				else
					pageLink[j].classList.remove('selected_page');
			}

			//ensure view all link is enabled
			pageNav[i].getElementsByClassName('view_all')[0].classList.remove('selected_page');

			//check if it's the first page
			if (pageNumber > 1)
				pageNav[i].getElementsByClassName('back_arrow')[0].classList.remove('invalid');
			else
				pageNav[i].getElementsByClassName('back_arrow')[0].classList.add('invalid');

			//check if it's the last page
			if (resultsBox.getElementsByClassName('page_' + (pageNumber + 1))[0] !== undefined)
				pageNav[i].getElementsByClassName('forward_arrow')[0].classList.remove('invalid');
			else
				pageNav[i].getElementsByClassName('forward_arrow')[0].classList.add('invalid');
		}

		//record current page number
		resultsBox.getElementsByClassName('active_page_store')[0].innerHTML = pageNumber;
	}
}

/*
search_page_shift:
change: -1 for back, 1 for forward
resultsBox: box containing search results, including page navigation
For scrolling forward and backward through pagination
*/
function search_page_shift(change, resultsBox)
{
	var currentPage = parseInt(resultsBox.getElementsByClassName('active_page_store')[0].innerHTML, 10);
	if (currentPage != 'NaN')
		search_page(currentPage + change, resultsBox);
}

/*
search_page_all:
resultsBox: box containing search results, including page navigation
Displays all results.
*/
function search_page_all(resultsBox)
{
	resultsBox.classList.add('view_all');

	var pages = resultsBox.getElementsByClassName('block_list');

	//ensure all numbers on nav bar are enabled
	for (var i = 0; i < pages.length; i++)
		pages[i].classList.remove('hidden');

	var pageNav = resultsBox.getElementsByClassName('page_nav');

	//loops for each set of nav links
	for (var i = 0; i < pageNav.length; i++)
	{
		var pageLink = pageNav[i].getElementsByClassName('number');
		//ensure all links are enabled
		for (var j = 0; j < pageLink.length; j++)
			pageLink[j].classList.remove('selected_page');

		//disable view all link
		pageNav[i].getElementsByClassName('view_all')[0].classList.add('selected_page');

		//hide arrows
		pageNav[i].getElementsByClassName('back_arrow')[0].classList.add('invalid');
		pageNav[i].getElementsByClassName('forward_arrow')[0].classList.add('invalid');
	}

	//record that all pages are being displayed
	resultsBox.getElementsByClassName('active_page_store')[0].innerHTML = 'all';
}
