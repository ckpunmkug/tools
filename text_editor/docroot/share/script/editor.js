function editorObject(textarea) {
	if(!(
		textarea instanceof Element
		&& textarea.tagName == "TEXTAREA"
	)) {
		throw new Error('Incorrect textarea passed into editorObject');
	}
	this.textarea = textarea;
	
	this.autoIndent = function()
	{ //{{{
		let $indent = "";
		let $index = this.textarea.selectionStart;
		for($index = $index; $index > 0; $index -= 1) {
			let $char = this.textarea.value.substring(($index-1), $index);
			if($char == "\n") { break; }
			if($char == "\t" || $char == " ") {
				$indent = $char + $indent;
			} else {
				$indent = "";
			}
		}
		$indent = "\n" + $indent;
		this.textarea.setRangeText($indent);
		this.textarea.selectionStart += $indent.length;
		
		return(true);
	}; //}}}

	this.increaseIndent = function()
	{ //{{{
		let $selectionStart = this.textarea.selectionStart;
		let $selectionEnd = this.textarea.selectionEnd;
		if($selectionStart == $selectionEnd) { return(null); }
		
		let $text = "";
		let $textBefore = "";
		let $textInside = "";
		let $textAfter = "";
		
		let $index = 0;
		let $char = "";
		let $length = 0;
		
		for($index = $selectionStart; $index > 0; $index -= 1) {
			$char = this.textarea.value.substring(($index - 1), $index);
			if($char == "\n") {	break; }
		}
		$selectionStart = $index;
		if($selectionStart > 0) {
			$textBefore = this.textarea.value.substring(0, $selectionStart);
		}
	
		$length = this.textarea.value.length;
		for($index = $selectionEnd; $index <= $length; $index += 1) {
			$char = this.textarea.value.substring(($index - 1), $index);
			if($char == "\n") { break; }
		}
		$selectionEnd = $index;
		if($selectionEnd < $length) {
			$textAfter = this.textarea.value.substring($selectionEnd, $length);
		}
		
		$textInside = this.textarea.value.substring($selectionStart, $selectionEnd);
		
		$index = 0;
		$length = $textInside.length;
		$text = "";
		while($index < $length) {
			$text += "\t";
			while($index < $length) {
				$char = $textInside.substring($index, $index + 1);
				$text += $char;
				if($char == "\n") { break; }
				$index += 1;
			}
			$index += 1;
		}
		$textInside = $text;
		
		$text = $textBefore + $textInside + $textAfter;
		this.textarea.value = $text;
		
		$selectionEnd = $selectionStart + $textInside.length;
		this.textarea.selectionStart = $selectionStart;
		this.textarea.selectionEnd = $selectionEnd;
		
		return(true);
	}; //}}}
	
	this.decreaseIndent = function()
	{ //{{{
		let $selectionStart = this.textarea.selectionStart;
		let $selectionEnd = this.textarea.selectionEnd;
		if($selectionStart == $selectionEnd) { return(null); }
		
		let $text = "";
		let $textBefore = "";
		let $textInside = "";
		let $textAfter = "";
		
		let $index = 0;
		let $char = "";
		let $length = 0;
		
		for($index = $selectionStart; $index > 0; $index -= 1) {
			$char = this.textarea.value.substring(($index - 1), $index);
			if($char == "\n") {	break; }
		}
		$selectionStart = $index;
		if($selectionStart > 0) {
			$textBefore = this.textarea.value.substring(0, $selectionStart);
		}
	
		$length = this.textarea.value.length;
		for($index = $selectionEnd; $index <= $length; $index += 1) {
			$char = this.textarea.value.substring(($index - 1), $index);
			if($char == "\n") { break; }
		}
		$selectionEnd = $index;
		if($selectionEnd < $length) {
			$textAfter = this.textarea.value.substring($selectionEnd, $length);
		}
		
		$textInside = this.textarea.value.substring($selectionStart, $selectionEnd);
		
		$index = 0;
		$length = $textInside.length;
		$text = "";
		while($index < $length) {
			$char = $textInside.substring($index, $index + 1);
			if($char != "\t") {
				$text += $char;
			}
			$index += 1;
			while($index < $length) {
				$char = $textInside.substring($index, $index + 1);
				$text += $char;
				$index += 1;
				if($char == "\n") { break; }
			}
		}
		$textInside = $text;
		
		$text = $textBefore + $textInside + $textAfter;
		this.textarea.value = $text;
		
		$selectionEnd = $selectionStart + $textInside.length;
		this.textarea.selectionStart = $selectionStart;
		this.textarea.selectionEnd = $selectionEnd;
		
		return(true);
	}; //}}}
	
	this.onKeyDown = function(event)
	{ //{{{
		if (event.ctrlKey === false && event.altKey === false && event.shiftKey === false) {
			if(event.keyCode == 9) {
				event.preventDefault();
				this.textarea.setRangeText("\t");
				this.textarea.selectionStart += 1;
				return(null);
			}
			if(event.keyCode == 13) {
				event.preventDefault();
				this.autoIndent();
				return;
			}
		}
		if (event.ctrlKey === false && event.altKey === true && event.shiftKey === true) {
			if(event.keyCode == 39) {
				event.preventDefault();
				this.increaseIndent();
				return(null);
			}
			if(event.keyCode == 37) {
				event.preventDefault();
				this.decreaseIndent();
				return(null);
			}
		}
		if (event.ctrlKey === true && event.altKey === true && event.shiftKey === true) {
			console.log(event);
		}
	}; //}}}

	this.textarea.addEventListener("keydown", this.onKeyDown.bind(this));	
}

