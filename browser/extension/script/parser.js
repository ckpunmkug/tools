// parse query
//{{{

var $query = '';
var input = document.getElementById("search_form_input");
if(input !== null) {
  $query = input.value;
}

//console.log($query);

//}}}

// parse queries
//{{{

var $RegExpArray = [
  	/^related\-searches$/,
  	/.+\srelated\-searches\s.+/,
  	/.+\srelated\-searches$/,
  	/^related\-searches\s.+/,
];

var $NodeList = document.querySelectorAll("div");
var div = null;

for(let $index = 0; $index < $NodeList.length; $index += 1) {
  let $item = $NodeList.item($index);
  let $class = $item.getAttribute("class");
  
  let $flag = false;
  for(let $key in $RegExpArray) {
    let $RegExp = $RegExpArray[$key];

    if($RegExp.test($class)) {
      $flag = true;
      div = $item;
      break;
    }
  }
  
  if($flag) break;
}

var $queries = [];
if(div !== null) {
  $NodeList = div.querySelectorAll("a");
  for(let $index = 0; $index < $NodeList.length; $index += 1) {
    let $item = $NodeList.item($index);
    let $text = $item.innerText;
    $queries.push($text);
  }
}

//console.log($queries);

//}}}

/// parse results
//{{{

var $results = [];
var LI = document.querySelectorAll("li[data-layout='organic']");
for(let $i = 0; $i < LI.length; $i += 1) {
  let li = LI.item($i);
  let article = li.querySelector("article");
  let DIV = article.childNodes;
  
  let $url = '';
  let $title = '';
  let $flag = -1;
  for(let $j = 0; $j < DIV.length; $j += 1) {
    let div = DIV.item($j);
    let a = div.querySelector("a[data-testid='result-title-a']");
    if(a === null) continue;
    $url = a.getAttribute("href");
   	$title = a.innerText;
    //console.log($title);
    $flag = $j;
    break;
  }
  
  let $description = '';
  if($flag > 0) {
    let div = DIV.item($flag+1);
    $description = div.innerText;
    //console.log($description);
  }
  
  if($url != '' && $title != '' && $description != '') {
    $results.push({
      "url": $url,
      "title": $title,
      "description": $description
    });
  }
}

//console.log($results);

//}}}

var result = {
	"queries": $queries,
	"query": {
		"text": $query,
		"results": $results
	}
}

result;

