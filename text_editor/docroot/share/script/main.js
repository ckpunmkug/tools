var editor = null;
const min_height = 'calc(100% - 1lh)';
const max_height = 'calc(100% - 18lh)';

function save()
{//{{{//
	
	let form = document.querySelector("form");
	let action = document.querySelector('input[name="action"]');
	action.value = 'save';
	form.submit();
	
}//}}}//

function close()
{//{{{//
	
	let div = document.getElementById("window");
	$propertyValue = div.style.getPropertyValue("height");
	if($propertyValue != min_height) {
		div.style.setProperty("height", min_height);
	}
	
}//}}}//

function iframeOnLoad(div, event)
{//{{{//
	
	let iframe = event.target;
	let $title = iframe.contentDocument.title;
	if($title == 'complete') {
		//console.log('ok');
	}
	else {
		div.style.setProperty("height", max_height);
	}
	
}//}}}//

function windowOnLoad(event)
{//{{{//

	let textarea = document.querySelector("textarea");
	editor = new editorObject(textarea);
	
	let div = document.getElementById("window");
	div.style.setProperty("height", min_height);
	
	let iframe = document.querySelector("iframe");
	iframe.addEventListener("load", iframeOnLoad.bind(null, div));
	
	setInterval(save, 60*1000);
	
}//}}}//
window.addEventListener("load", windowOnLoad);

function windowOnKeydown(event)
{//{{{//
	
	//console.log(event);
	
	if(
		event.altKey == false
		&& event.ctrlKey == false
		&& event.metaKey == false
		&& event.shiftKey == false
	) {
		switch(event.key) {
			case('Escape'):
			close();
			return(true);
			
			default:
			return(true);
		}
		
		event.preventDefault();
		event.stopPropagation();
		return(true);
	}
	
	if(
		event.altKey == false
		&& event.ctrlKey == true
		&& event.metaKey == false
		&& event.shiftKey == false
	) {
		switch(event.key) {
			case('s'):
			case('ы'):
			save();
			break;
			
			default:
			return(true);
		}
		
		event.preventDefault();
		event.stopPropagation();
		return(true);
	}
	
	
}//}}}//
window.addEventListener("keydown", windowOnKeydown);

