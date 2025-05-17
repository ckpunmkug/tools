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
{
	var $return, $parameters;
	
	if($command == "content") {
		$parameters = {
			active: true
		};
		var $tab = await browser.tabs.query($parameters);
		$tab = $tab[0];
		
		$parameters = {
			files: [
				"/content.js"
			]
			,target: {
				tabId: $tab.id
			}
		};
		$return = await browser.scripting.executeScript($parameters);
		console.log($return[0].result);
		
		return(null);
	}
}
browser.commands.onCommand.addListener(browserOnCommand);
