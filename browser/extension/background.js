const VERBOSE = true;
const DEBUG = true;

async function backendRequest($object, $query = "")
{//{{{//

	if(!($object instanceof Object)) {
		console.error("Passed 'object' is not object");
		return(false);
	}
	try {
		var $data = JSON.stringify($object);
	}
	catch($error) {
		console.error("Can't stringify 'object' to JSON");
		return(false);
	}

	var $backend = localStorage.getItem("backend");
	if($backend == null) {
		console.error("'backend' is not set in 'localStorage'");
		return(false);
	}
	$backend = JSON.parse($backend);
	
	$result = await HTTPLoader('POST', $backend.url+$query, $data, $backend.user, $backend.password);
	if(!($result.event == 'load' && $result.status == 200)) {
		console.error("'HTTPLoader' return error", $result);
		return(false);
	}
	
	return($result.body);
	
}//}}}//

// open extension index
//{{{

async function openTab($URL)
{
	var $createProperties = {
		"url": $URL
	};
	var $Tab = await browser.tabs.create($createProperties);
}
function onClickBrowserAction($tab, $OnClickData)
{
	var $URL = browser.runtime.getURL("index.html");
	openTab($URL);
}
browser.browserAction.onClicked.addListener(onClickBrowserAction)

//}}}

async function browserOnCommand($command)
{//{{{//
	
	var $return, $parameters, $tab, $result;
	
	switch($command) {
		case('parser'):
			$parameters = {
				active: true
			};
			$tab = await browser.tabs.query($parameters);
			$tab = $tab[0];
			$parameters = {
				file: 'script/parser.js'
			};
			$return = await browser.tabs.executeScript($tab.id, $parameters);
			$object = $return[0];
			backendRequest($object, "?component=duckduckgo&action=add_results");
	}
	
	return(null);
	
}//}}}//
browser.commands.onCommand.addListener(browserOnCommand);
