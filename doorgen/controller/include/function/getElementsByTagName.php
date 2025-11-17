<?php
//================================================================== Description
// In:
// 	string $html - html in which to look
// 	string $tag_name - search tag name
// Out:
// 	array = [
// 		string 'text' => text inside tag
// 		array 'attributes' => [
// 			'attribute_0 name' => 'attribute_0 value'
// 			...
// 			'attribute_N name' => 'attribute_N value'
// 		]
// 	]
// 	false - if error
//==============================================================================

function getElementsByTagName(string $html, string $tag_name)
{//{{{//

	$result = [];

	$dom = new DOMDocument();
	$return = $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

	if ($return == false) {
		Logger::error("can't load html to DOMDocument", ['xml' => $xml]);
		return false;
	}

	$list = $dom->getElementsByTagName($tag_name);

	for ($i = 0; $i < $list->length; $i++) {

		$node = $list->item($i);
		$text = $node->textContent;
		$attributes = array();
		
		for ($j = 0; $j < $node->attributes->length; $j++) {
		
			$attribute = $node->attributes->item($j);
			$attributes[$attribute->nodeName] = $attribute->nodeValue;
		}
		
		array_push($result, [
			'text' => $text
			,'attributes' => $attributes
		]);
	}

	return $result;	
	
}//}}}//

