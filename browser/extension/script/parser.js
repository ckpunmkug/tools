
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
		
		//console.log("parser.browserOnCommand");
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
		
		$parameters = {
			code: 'var $return = typeof(parser); $return;'
		};
		$return = await browser.tabs.executeScript($tab.id, $parameters);
		
		$parameters = {
			file: "/script/parser.js"
		};
		await browser.tabs.executeScript($tab.id, $parameters);
		
		$parameters = {
		 	code: 'var $return = prompt("Enter `parser` command"); $return;'
		};
		$return = await browser.tabs.executeScript($tab.id, $parameters);
		//console.log($return[0]);
		
		return($return[0]);
		
	}//}}}//
	
}; 

