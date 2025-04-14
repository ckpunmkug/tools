var $screen;
var $display;
var $fullscreen;
var $input;

function setDisplayHeight()
{//{{{//
	
	var $height = window.innerHeight;
	
	switch(window.innerHeight) {
		
		case(720):
		$display.style.setProperty('height', '441px');
		break;
		
		case(575):
		$display.style.setProperty('height', '326px');
		break;
		
	} // switch
	
}//}}}//

function windowOnLoad(event)
{//{{{//
	
	$screen = document.querySelector("div[name='screen']");
	$display = document.querySelector("div[name='display']");
	
	setDisplayHeight();
	
	/*
	$fullscreen = document.querySelector("button[name='fullscreen']");
	$fullscreen.addEventListener('click', function() {
		$screen.requestFullscreen();
	});
	*/
	
	/*
	$input.focus();
	$input.addEventListener("focusout", function() {
		setTimeout(function() {
			$input.focus();
		}, 30);
	});
	*/
	
}//}}}//

window.addEventListener('load', windowOnLoad);

function windowOnResize()
{//{{{//
	
	setDisplayHeight();
	
}//}}}//

window.addEventListener('resize', windowOnResize);
