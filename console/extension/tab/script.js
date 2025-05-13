/// basic mnemonics 
//{{{
var $b,$i,$f,$s,$a,$o,$e,$f,$t,$r,$R,$i,$j,$k // base registers
var $stack=[];
var ce = function($s){ console.error($s); }
var cw = function($s){ console.warn($s); return(false); }
var ci = function($s){ console.info($s); }
var cl = function($t){ console.log($t); }
if(!true){ ce('0'); cw('1'); ci('2'); }
//}}}
var tabs =
{//{{{//
	$NAME: ['io','fs','sh','sql','php'],
	$BUTTON: [],
	main: function()
	{//{{{//
		/// initialization tabs buttons ///
		var div = document.querySelector("div[name='tabs']"); //cl(div);
		for(var $i=0; $i<tabs.$NAME.length; $i++) {
			var $name = this.$NAME[$i];
			$button = div.querySelector("button[name='"+$name+"']");
			this.$BUTTON.push($button);
			$button.addEventListener("click", this.buttonOnClick)
		} // cl(this.$BUTTON);
		
	}//}}}//
};//}}}//
function windowOnLoad(event)
{//{{{//
	tabs.main();
}//}}}//
window.addEventListener("load",windowOnLoad);

