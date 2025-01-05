
var parser = {

	action: async function($command)
	{//{{{//
		
		//console.log($parserAction);
		var $return;
		
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
				return(true);
		}
		
		return(null);
		
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
		
		if($return[0] == 'undefined') {
			$parameters = {
				file: "/parser.js"
			};
			await browser.tabs.executeScript($tab.id, $parameters);
		}
		
		$parameters = {
			code: 'var $return = parser.commandLine(); $return;'
		};
		$return = await browser.tabs.executeScript($tab.id, $parameters);
		//console.log($return[0]);
		
		return($return[0]);
		
	}//}}}//
	
	,commandLine: function()
	{//{{{//

		$result = new Promise(function(resolve) {
			var $return;
			
			$return = document.querySelector("div[name='ckpunmkug.commandLine.container']");
			if($return !== null) {
				return(null);
			}
			
			var $container = document.createElement("div");
			$container.setAttribute('name', 'ckpunmkug.commandLine.container');
			
			var $style = {
				'all': 'unset'
				,'display': 'block'
				,'position': 'absolute'
				,'top': '0px'
				,'left': '0px'
				,'width': 'calc(100% - 12px)'
				,'margin': '4px'
				,'border': 'solid 2px black'
			};
			for(var [$key, $value] of Object.entries($style)) {
				$container.style.setProperty($key, $value);
			}
			
			$container.innerHTML = ''
///////////////////////////////////////////////////////////////{{{//
+`
<input name="commandline" value="" type="text" style="
	all: unset;
	width: calc(100% - 12px);
	font-family: Monospace;
	font-size: 16px;
	line-height: 24px;
	padding: 4px;
	background: black;
	color: white;
	border: solid 2px white;
	" />
`;
///////////////////////////////////////////////////////////////}}}//
			$container = document.body.appendChild($container);
			
			$input = $container.querySelector("input");
			$input.focus();
			
			var inputOnChange = function($input, $container, resolve) {
				var $result = $input.value;
				var $parent = $container.parentNode;
				$parent.removeChild($container);
				resolve($result);
			};
			
			$input.addEventListener("change", inputOnChange.bind(null, $input, $container, resolve));
		});

		return($result);
	
	}//}}}//
	
};

