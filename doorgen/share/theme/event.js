function id()
{//{{{//

    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';

    for (let i = 0; i < 4; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters[randomIndex];
    }

    return result;
	
}//}}}//

var Data = {
	tournaments: [],
	h1: '',
	prediction: {},
	
	get: function()
	{//{{{//
		
		var container = null, elements = null, element = null;
		
// tournaments /////////////////////////////////////////////////////////////////

		container = document.querySelector("div[class='tournaments']");
		elements = container.querySelectorAll("a[class='tournament']");
		for(element of elements) {
			this.tournaments.push({
				innerText: element.innerText,
				href: element.getAttribute("href")
			})
		}
		// console.log(this.tournaments);
		
// h1 //////////////////////////////////////////////////////////////////////////
		
		element = document.querySelector("h1");
		this.h1 = element.innerText;

// h2 //////////////////////////////////////////////////////////////////////////
		
		element = document.querySelector("h2");
		this.h2 = element.innerText;

// predictions /////////////////////////////////////////////////////////////////

		element = document.querySelector('div[class="prediction"]');
		var prediction = element.querySelector('span[class="prediction"]');
		var probability_count = element.querySelector('span[class="probability_count"]');
		var total = element.querySelector('span[class="total"]');
		var date = element.querySelector('span[class="date"]');
		var teams_analysis = element.querySelector('span[class="teams_analysis"]');
		var key_factors = element.querySelector('span[class="key_factors"]');
		this.prediction = {
			prediction: prediction.innerText,
			probability_count: probability_count.innerText,
			total: total.innerHTML,
			date: date.innerText,
			teams_analysis: teams_analysis.innerText,
			key_factors: key_factors.innerText,
		};
		
		//console.log(this.prediction);

	},//}}}//
	
	set: function()
	{//{{{//
	
		var container = null, elements = null, element = null, parent = null, current = null;
		
// tournaments /////////////////////////////////////////////////////////////////
		
		var tournament = document.getElementById("tournament");
		parent = tournament.parentElement;
		element = tournament;
		
		for(var $object of this.tournaments) {
			var a = tournament.cloneNode(true);
			a.setAttribute("id", id());
			var button = a.querySelector("button");
			button.innerText = $object.innerText;
			a.setAttribute("href", $object.href);
			element.after(a);
			element = a;
		}
		
		tournament = parent.removeChild(tournament);

// h1 //////////////////////////////////////////////////////////////////////////

		element = document.querySelector("h1");
		element.innerText = this.h1;

// h2 //////////////////////////////////////////////////////////////////////////

		element = document.querySelector("h2");
		element.innerText = this.h2;

// predictions /////////////////////////////////////////////////////////////////
		
		var element = document.getElementById('prediction');
		element.innerText = this.prediction.prediction;
		
		var element = document.getElementById('probability_count');
		element.innerText = this.prediction.probability_count;
		
		var element = document.getElementById('total');
		element.innerHTML = this.prediction.total;
		
		var element = document.getElementById('teams_analysis');
		element.innerText = this.prediction.teams_analysis;
		
		var element = document.getElementById('key_factors');
		element.innerText = this.prediction.key_factors;

	},//}}}//
};

function onIframeLoad(iframe, event)
{//{{{//
	
	var old_body = document.querySelector("body");
	var new_body = iframe.contentDocument.querySelector("body");
	new_body = new_body.parentElement.removeChild(new_body);
	
	old_body = old_body.parentElement.replaceChild(new_body, old_body);
	Data.set();
	
}//}}}//

function onWindowLoad(event)
{//{{{//
	
	Data.get();
	var new_body = document.createElement("body");
	var old_body = document.querySelector("body");
	old_body = old_body.parentElement.replaceChild(new_body, old_body);
	
	var link = document.createElement("link");
	link.rel = 'stylesheet';
	link.type = 'text/css';
	link.href =  "/share/theme/style.css";
	document.head.appendChild(link);
	
	var body = document.querySelector("body");
	var iframe = document.createElement("iframe");
	iframe.setAttribute("width", "0");
	iframe.setAttribute("height", "0");
	iframe.style.setProperty("visibility", "hidden");
	iframe = body.appendChild(iframe);
	iframe.addEventListener("load", onIframeLoad.bind(null, iframe));
	iframe.setAttribute("src", "/share/theme/event.html");
	
}//}}}//
window.addEventListener("load", onWindowLoad);
