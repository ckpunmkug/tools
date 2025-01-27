const VERBOSE = true;
const DEBUG = true;

async function backendRequest($parameters)
{//{{{//
	var $backend = localStorage.getItem("backend");
	if($backend == null) {
		console.error("`backend` is not set in `localStorage`");
		return(false);
	}
	
	$backend = JSON.parse($backend);
	
	if(typeof($parameters) == 'object' && $parameters != null) {
		$parameters.token = $backend.token;
		try {
			var $data = JSON.stringify($parameters);
		}
		catch($error) {
			console.error("Can't stringify `parameters` to JSON");
			return(false);
		}
	}
	else {
		console.error("Incorrect `parameters` type");
		return(false);
	}
	
	$result = await HTTPLoader('POST', $backend.url, $data, $backend.user, $backend.password);
	if(!($result.event == 'load' && $result.status == 200)) {
		console.error("`HTTPLoader` return error", $result);
		return(false);
	}
	
	return($result.body);
	
}//}}}//

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

async function browserOnCommand($command)
{//{{{//
	
	var $return;
	
	switch($command) {
		case('parser'): 
			$return = parser.browserOnCommand();
			return($return);
	}
	
	return(null);
	
}//}}}//
browser.commands.onCommand.addListener(browserOnCommand);
