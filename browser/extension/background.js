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
