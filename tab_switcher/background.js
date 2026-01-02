async function tabsOnUpdated(tabId, changeInfo)
{//{{{//
	
	if(changeInfo.status != 'complete') return(null);
	
	$parameters = {
		files: [
			"/content.css"
		]
		,target: {
			tabId: tabId
		}
	};
	$return = await browser.scripting.insertCSS($parameters);
	
	$parameters = {
		files: [
			"/content.js"
		]
		,target: {
			tabId: tabId
		}
	};
	$return = await browser.scripting.executeScript($parameters);
	
	return(null);
	
}//}}}//
browser.tabs.onUpdated.addListener(tabsOnUpdated);

var TAB = {
	"1" : null, "2" : null, "3" : null, "4" : null, "5" : null, "6" : null, "7" : null, "8" : null, "9" : null, "0" : null
};

async function getTabTitles()
{//{{{//
	
	var $TITLE = {};
	
	for($key in TAB) {
		TAB[$key] = null;
		$TITLE[$key] = null;
	}
	
	var $queryInfo = {
		currentWindow: true,
	};
	var $TAB = await browser.tabs.query($queryInfo);
	
	var $index = $TAB.length;
	for($key in TAB) {
		$index -= 1;
		if($index < 0) break;
		
		TAB[$key] = $TAB[$index].id;
		$TITLE[$key] = $TAB[$index].title;
	}
	
	return($TITLE);
	
}//}}}//

async function switchToTab($key)
{//{{{//
	
	if(typeof(TAB[$key]) == "number") {
		var $tab = await browser.tabs.update(TAB[$key],{
			active: true,
		});
	}
	
	return(null);
	
}//}}}//

async function runtimeOnMessage(request, sender)
{//{{{//
	
	var $tabId = sender.tab.id;
	
	if(request.command == 'get_tab_titles') {
		var $TITLE = await getTabTitles();
		var $message = JSON.stringify($TITLE);
		browser.tabs.sendMessage($tabId, $message);
		return(null);
	}
	
	if(request.command == 'switch_to_tab') {
		switchToTab(request.key);
		return(null);
	}

	return(null);

}//}}}//
browser.runtime.onMessage.addListener(runtimeOnMessage);

