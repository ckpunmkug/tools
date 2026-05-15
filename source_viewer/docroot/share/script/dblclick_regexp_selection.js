
window.addEventListener("dblclick", function(event) // Double click selection with regular expression
{//{{{	
	let $string, $return;
	
	var $Selection = document.getSelection();
	if(!($Selection.type == "Range" && $Selection.rangeCount == 1)) {
		return(null);
	}

	var $Range = $Selection.getRangeAt(0);
	$Selection.removeRange($Range);
	
	var $RegExp = new RegExp("^[\-A-Za-z0-9~/\.&\$_:>]+$");
	
	while(true) {
		var $startOffset = $Range.startOffset - 1;
		if($startOffset >= 0) {
			$Range.setStart($Range.startContainer, $startOffset);
		}
		else {
			break;
		}
		
		$string = $Range.toString();
		$return = $RegExp.test($string);
		if($return == false) {
			$startOffset += 1;
			$Range.setStart($Range.startContainer, $startOffset);
			break;
		}
	}
	
	while(true) {
		$endOffset = $Range.endOffset + 1;
		if($endOffset < $Range.endContainer.length) {
			$Range.setEnd($Range.endContainer, $endOffset);
		}
		else {
			break;
		}
		
		$string = $Range.toString();
		$return = $RegExp.test($string);
		if($return == false) {
			$endOffset -= 1;
			$Range.setEnd($Range.endContainer, $endOffset);
			break;
		}
	}
	
	//console.log($Range.toString());
	$Selection.addRange($Range);
});//}}}

