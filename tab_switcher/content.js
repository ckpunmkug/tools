var tabSwitcher = {
	
	div: null,
	
	divCreate: function()
	{//{{{//
		
		var div = document.createElement("div");
		div.innerText = "Switch browser tab";
		
		div.setAttribute("style", `
			all: unset;
			display: block;
			position: absolute; 
			top: 0px;
			left: 0px;
			width: calc(100% - 40px);
			height: calc(100% - 40px);
			font-family: 'Terminus';
			font-size: 20px;
			line-height: 20px;
			background-color: #000000;
			color: #00AA00;
			padding: 20px;
		`);
		div.setAttribute("tabindex", "65536");
		
		this.div = document.body.appendChild(div);
		
		this.div.focus();
		this.div.addEventListener("keydown", this.divOnKeyDown.bind(this));
		
		return(null);
		
	},//}}}//
	
	divRemove: function()
	{//{{{//
		
		for($key in this.BUTTON) {
			var button = this.BUTTON[$key];
			if(button === null) continue;
			
			button.removeEventListener("click", this.buttonOnClick.bind(this, $key));
			this.BUTTON[$key] = this.div.removeChild(button);
			this.BUTTON[$key] = null;
		}
		
		this.div.removeEventListener("keydown", this.divOnKeyDown.bind(this));
		this.div = this.div.parentNode.removeChild(this.div);
		this.div = null;
		
		return(null);
		
	},//}}}//
	
	divOnKeyDown: function(event)
	{//{{{//
	
		if(event.key == "Escape") {
			event.preventDefault();
			event.stopPropagation();
			
			this.divRemove();
			return(null);
		}
	
		var $KEY = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];
	
		if($KEY.includes(event.key) !== true) return(null);
		
		event.preventDefault();
		event.stopPropagation();
		
		this.divRemove();
		
		this.switchToTab(event.key);
		
		return(null);
		
	},//}}}//
	
	BUTTON: { "1": null, "2": null, "3": null, "4": null, "5": null, "6": null, "7": null, "8": null, "9": null, "0": null },
	
	BUTTONCreate: function($TITLE)
	{//{{{//
		
		var $key, $title;
		for($key in $TITLE) {
			if($TITLE[$key] === null) continue;
			
			$title = $TITLE[$key];
			
			var button = document.createElement("button");
			button.innerText = `${$key} - ${$title}`;
			button.setAttribute("style", `
				all: unset;
				display: block;
				position: relative; 
				font-family: 'Terminus';
				font-size: 20px;
				line-height: 20px;
				background-color: #000000;
				color: #00AA00;
				cursor: pointer;
			`);
			
			this.BUTTON[$key] = this.div.appendChild(button);
			this.BUTTON[$key].addEventListener("click", this.buttonOnClick.bind(this, $key));
			
		} // for($key in $TITLE)
		
		return(null);
		
	},//}}}//
	
	buttonOnClick: function($key)
	{//{{{//
		
		this.divRemove();
		
		this.switchToTab($key);
		
		return(null);
		
	},//}}}//
	
	getTabTitles: function()
	{//{{{//
		
		sending = browser.runtime.sendMessage({
			command: "get_tab_titles",
		});
		
		return(null);
		
	},//}}}//
	
	switchToTab: function($key)
	{//{{{//
	
		sending = browser.runtime.sendMessage({
			command: "switch_to_tab",
			key: $key,
		});
		
		return(null);
		
	},//}}}//	
	
};
	
tabSwitcher.windowOnKeyDown = function(event)
{//{{{//

	if(event.key != "Escape") return(null);
	
	this.divCreate();
	
	this.getTabTitles();
	
	return(null);
	
};//}}}//
window.addEventListener("keydown", tabSwitcher.windowOnKeyDown.bind(tabSwitcher));
	
tabSwitcher.onMessage = function($message)
{//{{{//

	var $TITLE = JSON.parse($message);

	this.BUTTONCreate($TITLE);
	
	return(null);
	
};//}}}//
browser.runtime.onMessage.addListener(tabSwitcher.onMessage.bind(tabSwitcher));

