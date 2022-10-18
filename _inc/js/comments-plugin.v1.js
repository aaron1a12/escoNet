var url = window.location.href;
url = url.split('?')[0];
url = url.split('#')[0];
url = url.replace(/\/$/, "");
url = url.toLowerCase();
url = encodeURIComponent(url);


var escoNetScript = document.getElementById('escoNet_Comments');

var customSettings = "";

if(typeof escoNet_Comments_Title != 'undefined'){
	escoNet_Comments_Title = encodeURIComponent(escoNet_Comments_Title);
	customSettings = "&title="+escoNet_Comments_Title;
}

var iframe = document.createElement("iframe");
iframe.setAttribute("scrolling", "no");
iframe.setAttribute("src", "http://www.esco.net/api/comments-plugin/v1/?url="+url+"&no-cache="+Math.random()+customSettings);
iframe.setAttribute("style", "border: none; overflow: hidden; width: 100%;");
iframe.setAttribute("onload", "escoNet_resize(this)");

escoNetScript.parentNode.replaceChild(iframe, escoNetScript);


// Create IE + others compatible event handler
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

// Listen to message from child window
eventer(messageEvent,function(e) {
  iframe.style.height = e.data + "px";
},false);