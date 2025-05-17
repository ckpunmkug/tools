function treeObject(ul) 
{
	if(!(
		ul instanceof Element
		&& ul.tagName == "UL"
	)) {
		throw new Error('Incorrect ul passed into treeObject');
	}
	this.ul = ul;
	
	var $e;
	
	this.ul.innerHTML = ""
+`<li name="branch"
	><span name="html"
		>branch</span
	><ul name="child"
	></ul
></li
><li name="leaf"
	><span name="html"
		>leaf</span
></li>`;

	this.branch = {};
	$e = this.ul.querySelector("li[name='branch']");
	this.branch.li = $e.cloneNode(true);
	this.ul.removeChild($e);

	this.leaf = {};
	$e = this.ul.querySelector("li[name='leaf']");
	this.leaf.li = $e.cloneNode(true);
	this.ul.removeChild($e);
	
	this.create = function($json)
	{//{{{//
		
		try {
			var $o = JSON.parse($json);
		}
		catch {
			console.warn("Can't parse tree json");
			return(false);
		}
		
		if(!(
			typeof($o) == "object"
			&& $o instanceof Object 
		)) {
			console.warn("Passed tree is not object");
			return(false);
		}
		
		this.addNodes(this.ul, $o);
		
		return(true);
		
	};//}}}//
	
	this.nodes = [];
	this.addNodes = function($parent, $object)
	{//{{{//
		
		var $name, $item, $element, $node, $child, $text;
		
		for($name in $object) {
			$item = $object[$name];
			
			if(typeof($item) == "object" && $item instanceof Object) {	
				$element = this.branch.li.cloneNode(true);
				$node = $parent.appendChild($element);
				this.nodes.push($node);
				
				$html = $node.querySelector("span[name='html']");
				$html.innerHTML = $name;
				
				$child = $node.querySelector("ul[name='child']");
				this.addNodes($child, $item);
				
				continue;
			}
			
			if(typeof($item) == "string") {
				$element = this.leaf.li.cloneNode(true);
				$node = $parent.appendChild($element);
				this.nodes.push($node);
				
				$html = $node.querySelector("span[name='html']");
				$html.innerHTML = $item;
				
				continue;
			}
			
			console.warn("Incorrect 'tree' object item");
			return(false);
		} // for($name in $object)
		
		return(true);
		
	};//}}}//

	this.collapse = function($index) 
	{//{{{//
		
		var $node, $name, $child;
		
		if(this.nodes[$index] == undefined) {
			console.warn("node with passed index not exists");
			return(false);
		}
		
		$node = this.nodes[$index];
		$name = $node.getAttribute("name");
		if($name != "branch") {
			console.warn("Can't collapse not 'branche' node");
			return(false);
		}
		
		$child = $node.querySelector("ul[name='child']");
		$child.style.setProperty("display", "none");
		
	};//}}}//

	this.expand = function($index) 
	{//{{{//
		
		var $node, $name, $child;
		
		if(this.nodes[$index] == undefined) {
			console.warn("node with passed index not exists");
			return(false);
		}
		
		$node = this.nodes[$index];
		$name = $node.getAttribute("name");
		if($name != "branch") {
			console.warn("Can't expand not 'branche' node");
			return(false);
		}
		
		$child = $node.querySelector("ul[name='child']");
		$child.style.setProperty("display", "block");
		
	};//}}}//

	this.collapseAll = function($index) 
	{//{{{//
		
		var $index;
		var $node, $name, $NodeList, $item;
		
		if(this.nodes[$index] == undefined) {
			console.warn("node with passed index not exists");
			return(false);
		}
		
		$node = this.nodes[$index];
		$name = $node.getAttribute("name");
		if($name != "branch") {
			console.warn("Can't collapse not 'branche' node");
			return(false);
		}
		
		$NodeList = $node.querySelectorAll("ul[name='child']");
		for($index = 0; $index < $NodeList.length; $index += 1) {
			$item = $NodeList.item($index);
			$item.style.setProperty("display", "none");
		}
		
	};//}}}//

	this.expandAll = function($index) 
	{//{{{//
		
		var $node, $name, $NodeList, $item;
		
		if(this.nodes[$index] == undefined) {
			console.warn("node with passed index not exists");
			return(false);
		}
		
		$node = this.nodes[$index];
		$name = $node.getAttribute("name");
		if($name != "branch") {
			console.warn("Can't expand not 'branche' node");
			return(false);
		}
		
		$NodeList = $node.querySelectorAll("ul[name='child']");
		for($index = 0; $index < $NodeList.length; $index += 1) {
			$item = $NodeList.item($index);
			$item.style.setProperty("display", "block");
		}
		
	};//}}}//	

	this.insert = function($index, $json)
	{//{{{//
		
		try {
			var $o = JSON.parse($json);
		}
		catch {
			console.warn("Can't parse tree json");
			return(false);
		}
		
		if(!(
			typeof($o) == "object"
			&& $o instanceof Object 
		)) {
			console.warn("Passed tree is not object");
			return(false);
		}
		
		if(this.nodes[$index] == undefined) {
			console.warn("node with passed index not exists");
			return(false);
		}
		
		$node = this.nodes[$index];
		$name = $node.getAttribute("name");
		if($name != "branch") {
			console.warn("Can't insert into not 'branche' node");
			return(false);
		}
		
		this.addNodes($node, $o);
		
	}//}}}//

	this.delete = function($index)
	{//{{{//
		
		var $key, $children;
		
		if(this.nodes[$index] == undefined) {
			console.warn("node with passed index not exists");
			return(false);
		}
		
		$node = this.nodes[$index];
		$name = $node.getAttribute("name");
		
		if($name == "leaf") {
			for($key in this.nodes) {
				if(this.nodes[$key] == $node) {
					this.nodes[$key] = $node.parentNode.removeChild($node);
					this.nodes[$key] = undefined;
					return(true);
				}
			}
		}
	
		if($name == "branch") {
			$children = this.getChildren($index);
			for($key in $children) {
				this.delete($children[$key][0]);
			}
			
			$node = this.nodes[$index];
			for($key in this.nodes) {
				if(this.nodes[$key] == $node) {
					this.nodes[$key] = $node.parentNode.removeChild($node);
					this.nodes[$key] = undefined;
					return(true);
				}
			}
		}
		
		return(true);
		
	}//}}}//
	
	this.getChildren = function($index)
	{//{{{//
		
		var $result = [];
		var $NodeList, $item, $index, $key;
		
		if(this.nodes[$index] == undefined) {
			console.warn("node with passed index not exists");
			return(false);
		}
		
		$node = this.nodes[$index];
		$name = $node.getAttribute("name");
		if($name != "branch") {
			console.warn("Can't get children for not 'branche' node");
			return(false);
		}
		
		$node = $node.querySelector("ul[name='child']");
		
		$NodeList = $node.childNodes;
		for($index = 0; $index < $NodeList.length; $index += 1) {
			$item = $NodeList.item($index);
			if(
				$item.getAttribute("name") == "branch"
				|| $item.getAttribute("name") == "leaf"
			) {
				$result.push([0, $item]);
			}
		}
		
		for($index = 0; $index < $result.length; $index++) {
			for($key in this.nodes) {
				if(this.nodes[$key] == $result[$index][1]) {
					$result[$index][0] = $key;
				}
			}
		}
		
		return($result);
		
	};//}}}//

}

