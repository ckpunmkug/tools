
var parser = {

	action: async function($command)
	{//{{{//
		
		//console.log($parserAction);
		var $return, $parameters;
		
		switch($command) {
			case('h'):
			case('help'):
				$help = ''
///////////////////////////////////////////////////////////////{{{//
+`
h help - print this help into extension console
n next - get next query
`;
///////////////////////////////////////////////////////////////}}}//
				console.log($help);
				break;
			case('n'):
			case('next'):
				$parameters = {
					"component": "parser"
					,"action": "next"
				};
				$return = await backendRequest($parameters);
				if(typeof($return) != "string") {
					this.error("Can't perform backend request");
					return(false);
				}
				await navigator.clipboard.writeText($return);
				break;
		}
		return(null);
		
	}//}}}//
	
	,error: async function($message)
	{//{{{//
		console.error($message);
		
		var $return, $parameters;
		$parameters = {
			active: true
		};
		var $tab = await browser.tabs.query($parameters);
		$tab = $tab[0];
		$parameters = {
		 	code: 'alert("'+$message+'");'
		};
		$return = await browser.tabs.executeScript($tab.id, $parameters);
	}//}}}//
	
	,browserOnCommand: async function()
	{//{{{//
		
		if(typeof(VERBOSE) == 'boolean' && VERBOSE === true) {
			console.log("parser.browserOnCommand");
		}
		var $return;
		
		$return = await this.getCommand();
		this.action($return);
		
	}//}}}//
	
	,getCommand: async function()
	{//{{{//
	
		var $return, $parameters;
		
		$parameters = {
			active: true
		};
		var $tab = await browser.tabs.query($parameters);
		$tab = $tab[0];
		if(typeof(DEBUG) == 'boolean' && DEBUG === true) {
			console.debug('active tab', $tab);
		}
		
		$parameters = {
		 	code: '$return = prompt("Enter parser command"); $return;'
		};
		$return = await browser.tabs.executeScript($tab.id, $parameters);
		var $command = $return[0];
		if(typeof(DEBUG) == 'boolean' && DEBUG === true) {
			console.debug('parser command', $command);
		}
		
		return($command);
		
	}//}}}//
	
}; 

