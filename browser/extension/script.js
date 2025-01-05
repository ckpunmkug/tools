var backend = {
	initialization: function()
	{//{{{//
		
		var $backend = localStorage.getItem("backend");
		if($backend === null) {
			$backend = {
				url: 'http://127.0.0.1:8080/index.php'
				,user: 'user'
				,'password': ''
				,'token': ''
			};
		}
		else {
			$backend = JSON.parse($backend);
		}
		
		this.container = document.querySelector("fieldset[name='backend']");
		this.url = this.container.querySelector("input[name='url']");
		this.user = this.container.querySelector("input[name='user']");
		this.password = this.container.querySelector("input[name='password']");
		this.token = this.container.querySelector("input[name='token']");
		this.set = this.container.querySelector("button[name='set']");
		
		this.url.value = $backend.url;
		this.user.value = $backend.user;
		this.password.value = $backend.password;
		this.token.value = $backend.token;
		
		this.set.addEventListener("click", this.setItem.bind(this));
		
	}//}}}//
	
	,setItem: async function()
	{//{{{//
		var $return = await HTTPLoader('GET', this.url.value, undefined, this.user.value, this.password.value);
		if(!($return.event == 'load' && $return.status == 200)) {
			alert("HTTPLoader error");
			console.log($return);
			return(false);
		}
		
		this.token.value = $return.body;
		
		var $backend = {
			url: this.url.value
			,user: this.user.value
			,password: this.password.value
			,token: this.token.value
		};
		localStorage.setItem("backend", JSON.stringify($backend));
		
	}//}}}//
	
	,container: null
	,url: null
	,user: null
	,password: null
	,token: null
	,set: null
};

async function windowOnLoad(event)
{//{{{//

	backend.initialization();
	
}//}}}//

window.addEventListener("load", windowOnLoad);

