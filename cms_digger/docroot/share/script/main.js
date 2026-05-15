function setInputs($query, $result, $status, event)
{//{{{//
	
	let $input;
	
	$input = document.querySelector('input[name="query"]');
	$input.value = $query;
	
	$input = document.querySelector('input[name="result"]');
	$input.value = $result;
	
	$input = document.querySelector('input[name="status"]');
	$input.value = $status;
	
}//}}}//

function windowOnLoad(event)
{//{{{//
	
	let $ITEM = document.querySelectorAll('span[class="item"]');
	for(let $i = 0; $i < $ITEM.length; $i += 1) {
	
		let $item = $ITEM[$i];
		
		let $pattern = $item.querySelector('span[class="pattern"]');
		let $query = $pattern.getAttribute("title");
		
		let $STATUS = $item.querySelectorAll('span[class="status"]');
		for(let $j = 0; $j < $STATUS.length; $j += 1) {
			
			let $span = $STATUS[$j];
			let $result = $span.getAttribute("title");
			let $status = $span.innerText;
			
			$span.addEventListener("click", setInputs.bind(null, $query, $result, $status));
			
		}// for(let $j = 0; $j < $STATUS.length; $j += 1)
		
	}// for(let $index = 0; $index < $ITEM.length; $index += 1)
	
}//}}}//
window.addEventListener("load", windowOnLoad);

