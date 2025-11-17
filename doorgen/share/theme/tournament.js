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
	predictions: [],
	pages: {},
	
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

// predictions /////////////////////////////////////////////////////////////////

		elements = document.querySelectorAll('div[class="short_prediction"]');
		for(element of elements) {
			var opponent1 = element.querySelector('span[class="opponent1"]');
			var opponent2 = element.querySelector('span[class="opponent2"]');
			var prediction = element.querySelector('span[class="prediction"]');
			var probability_count = element.querySelector('span[class="probability_count"]');
			var total = element.querySelector('span[class="total"]');
			var date = element.querySelector('span[class="date"]');
			var details = element.querySelector('a[class="details"]');
			this.predictions.push({
				opponent1: opponent1.innerText,
				opponent2: opponent2.innerText,
				prediction: prediction.innerText,
				probability_count: probability_count.innerText,
				total: total.innerHTML,
				date: date.innerText,
				details: details.getAttribute("href")
			});
		}
		
		// console.log(this.predictions);
		
// pages ///////////////////////////////////////////////////////////////////////
		
		element = document.querySelector('a[class="previous"]');
		if(element !== null) {
			this.pages.previous = element.getAttribute("href");
		}
		else {
			this.pages.previous = false;
		}
		element = document.querySelector('a[class="next"]');
		if(element !== null) {
			this.pages.next = element.getAttribute("href");
		}
		else {
			this.pages.next = false;
		}
		element = document.getElementById("current");
		this.pages.current = element.innerText;
		
		elements = document.querySelectorAll('a[class="page_number"]');
		this.pages.numbers = [];
		for(element of elements) {
			this.pages.numbers.push({
				href: element.getAttribute("href"),
				innerText: element.innerText
			})
		}
		
		// console.log(this.pages);

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

// predictions /////////////////////////////////////////////////////////////////
		
		var prediction = document.getElementById('prediction');
		current = prediction;
		var date = prediction.querySelector('div[class="date"]');
		var h2 = prediction.querySelector('h2');
		var description = prediction.querySelector('span[class="description"]');
		var probability_count = prediction.querySelector('span[class="blue_round"]');
		var total = prediction.querySelector('span[class="yellow_round"]');		
		var a = prediction.querySelector('a');
		
		for($object of this.predictions) {
			date.innerText = "Начало в " + $object.date + " время московское";
			h2.innerText = $object.opponent1 + " против " + $object.opponent2;
			description.innerText = $object.prediction;
			probability_count.innerText = $object.probability_count;
			a.setAttribute("href", $object.details);
			total.innerHTML = $object.total;
			element = prediction.cloneNode(true);
			element.setAttribute("id", id());
			current.after(element);
			current = element;
		}
		
		prediction.parentElement.removeChild(prediction);

// pages ///////////////////////////////////////////////////////////////////////

		element = document.getElementById("previous");
		if(this.pages.previous !== false) {
			 element.setAttribute("href", this.pages.previous);
		}
		else {
			element.parentElement.removeChild(element);
		}

		element = document.getElementById("next");
		if(this.pages.next !== false) {
			 element.setAttribute("href", this.pages.next);
		}
		else {
			element.parentElement.removeChild(element);
		}
		
		element = document.getElementById("number");
		current = element;
		for($object of this.pages.numbers) {
			var a = element.cloneNode(true);
			a.setAttribute("id", id());
			a.setAttribute("href", $object.href);
			a.innerText = $object.innerText;
			if(a.innerText == this.pages.current) {
				a.setAttribute("class", "blue_round");
			}
			current.after(a);
			current = a;
		}
		element.parentElement.removeChild(element);

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
	iframe.setAttribute("src", "/share/theme/tournament.html");
	
}//}}}//
window.addEventListener("load", onWindowLoad);
