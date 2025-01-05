
async function HTTPLoader(
	$method, 
	$url,
	$data,
	$user, $password, // Authorization - Basic
	$timeout = 30, // in seconds
	$maxResponseLength = 16 // in megabytes
	
	// Return object with property `event`
	// load - http request complete
	// abort - when response length more then maxResponseLength
) {//{{{//
	
	var wrapper = function($method, $url, $data, $user, $password, $timeout, $maxResponseLength, resolve, reject) {
		//console.log($method, $url, $data, $user, $password, $timeout, $maxResponseLength, resolve, reject);
		
		$maxResponseLength = 0x100000 * $maxResponseLength;
		$timeout = 1000 * $timeout;
		
		var $XMLHttpRequest = new XMLHttpRequest();
		$XMLHttpRequest.open($method, $url, true);	
		$XMLHttpRequest.responseType = "text";
		$XMLHttpRequest.timeout = $timeout;
		if(typeof($user) == "string" && typeof($password) == "string") {
			$XMLHttpRequest.setRequestHeader("Authorization", " Basic "+btoa($user+":"+$password));
		}
	
		var onLoad = function($XMLHttpRequest, $maxResponseLength, resolve, onAbort) {
			if($XMLHttpRequest.response.length > $maxResponseLength) {
				$XMLHttpRequest.abort();
				onAbort(resolve);
			}
			$result = {
				event: 'load'
				,status: $XMLHttpRequest.status
				,headers: $XMLHttpRequest.getAllResponseHeaders()
				,body: $XMLHttpRequest.response
			};
			resolve($result);
		};
		
		var onProgress = function($XMLHttpRequest, $maxResponseLength, resolve, onAbort, event) {
			var $loaded = event.loaded;
			if($loaded > $maxResponseLength) {
				$XMLHttpRequest.abort()
				onAbort(resolve);
			}
		};
		
		var onAbort = function(resolve) {
			$result = {
				event: 'abort'
			};
			resolve($result);
		};
		
		var onTimeout = function(resolve) {
			$result = {
				event: 'timeout'
			};
			resolve($result);
		};
		
		var onError = function(resolve) {
			$result = {
				event: 'error'
			};
			resolve($result);
		};
		
		$XMLHttpRequest.addEventListener('load', onLoad.bind(null, $XMLHttpRequest, $maxResponseLength, resolve, onAbort));
		$XMLHttpRequest.addEventListener('progress', onProgress.bind(null, $XMLHttpRequest, $maxResponseLength, resolve, onAbort));
		$XMLHttpRequest.addEventListener('abort', onAbort.bind(null, resolve));
		$XMLHttpRequest.addEventListener('timeout', onTimeout.bind(null, resolve));
		$XMLHttpRequest.addEventListener('error', onError.bind(null, resolve));
		
		$XMLHttpRequest.send($data);
	};
	var $result = new Promise(wrapper.bind(null, $method, $url, $data, $user, $password, $timeout, $maxResponseLength));
	return($result);
	
}//}}}//

