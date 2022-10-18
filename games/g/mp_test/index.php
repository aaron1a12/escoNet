<?php

class customPage extends page {
    public $title = 'Websocket Test';
	
	function head(){?>
	<style>
	#console {
		color:#fff;
		text-align:left;
		background-color: #000;
		height: 200px;
		border-radius: 12px;
		border: 4px solid #CCC;
		padding: 15px;
		overflow-y: scroll;
		white-space: pre-line;
		word-wrap: break-word;
		word-break: break-all;
		font-family: monospace;
	}
	
	#console span {
		color: #aaa;
	}
	</style>
	<script>
		function getTime() {
			var date = new Date();
			var hours = date.getHours();
			var minutes = date.getMinutes();
			var seconds = date.getSeconds();
			var ampm = hours >= 12 ? 'PM' : 'AM';
			hours = hours % 12;
			hours = hours ? hours : 12; // the hour '0' should be '12'
			minutes = minutes < 10 ? '0'+minutes : minutes;

			seconds = seconds < 10 ? '0'+seconds : seconds;

			var strTime = hours + ':' + minutes + ':' + seconds + ampm;
			return strTime;
		}	
	</script>
	<?php
	}
	
    function content() {
?>
<div class="widget" style="position:relative;">
    
	
	<div id="scene" style="background-color:skyblue;height:300px;"></div>
	
	<br>
	
	<div id="console"></div>

	<br>
	
	<button onclick="socket.send(0);">Ping</button>
	
	<br><br>
	<b>Connected users:</b><br>
	<div id="playersDiv"></div>
	
	<script>	
	//
	// Console
	//
	
	var terminalWindow = document.getElementById('console');
	
	function print(msg)
	{
		var outputDiv = document.createElement("div");
		outputDiv.innerHTML = '<span>['+getTime()+'] </span>'+msg;

		terminalWindow.appendChild(outputDiv); 
		terminalWindow.scrollTop = terminalWindow.scrollHeight;
	}
	
	//
	// Game
	//
	
	var scene = document.getElementById('scene');
	scene.style.cursor = 'none';
	
	window.MouseX = 0;
	window.MouseY = 0;
	
	sceneOnMouseMove = function(evt) {
		if(typeof playerMe !='undefined'){
			if(playerMe!=''){
				var docScrollY = document.documentElement.scrollTop;
				var docScrollX = document.documentElement.scrollLeft;
				
				var X = (evt.clientX+docScrollX);
				var Y = (evt.clientY+docScrollY);
				
				window.MouseX = X;
				window.MouseY = Y;
				
				playerMeCursor = document.getElementById(playerMe);
				playerMeCursor.style.top = Y+'px';
				playerMeCursor.style.left = X+'px';
				
				socket.send(JSON.stringify([X, Y]));
			}
		}
	};
	
	// WTF IS THIS?
	setInterval(function(){
		socket.send(JSON.stringify([window.MouseX, window.MouseY]));
	},100);
	
	scene.addEventListener("mousemove", sceneOnMouseMove);
	
	//
	// Server settings
	//
	
	var connectToIP = "multiplayer.esco.net";
	var connectToPort = "6432";
	
	var bConnected = false;
	
	print("Attempting to connect to "+connectToIP+" using port "+connectToPort+"...");
	
	var socket;
	
	function socket_onOpen()
	{
		bConnected = true;
		print("Connection established.");
		
		// Web Socket is connected, send data using send()
		//socket.send("Message to send");
		//alert("Message is sent...");
	}

	function socket_onClose(evt)
	{
		if(bConnected) {
				print("The connection has been closed.");
		}else {
				print("Server appears offline. Trying to start the remote host..."); //Unable to connect to server
			
			$.ajax({
				type: "GET",
				url : "start.php",
			}).done(function() {
				print("Attempting to connect again...");
				
				setTimeout(connectToServer, 5000);
			}).fail(function() {
				print("Unable to start server.");
			});
			
		}
    }
	
	var players = [];
	var playerMe = '';
	var bFirstNewConn = true;

	function createPlayerCursor(id) {
		var playerDiv = document.createElement('DIV');
		playerDiv.style.width = '16px';
		playerDiv.style.height = '16px';
		playerDiv.style.backgroundColor = '#000000';
		playerDiv.style.position = 'absolute';
		playerDiv.style.zIndex = '9999';
		playerDiv.id = id;
		playerDiv.style.pointerEvents = 'none';
		document.body.appendChild(playerDiv);	
	}
	
	
	function socket_onMessage(evt) 
	{ 
	  var received_msg = evt.data;
	  
	  if(received_msg.substr(0,1)=='{'){
		data = JSON.parse( received_msg );
		
		if(typeof data.id!='undefined'){
		
			if(data.action==0)
			{			
				//var data
				print('Player '+data.id+' (' + data.ip +') has joined the server.');
				
				if(bFirstNewConn) {
					playerMe = data.id;
				}
				
				createPlayerCursor(data.id);
				//scene.appendChild(playerDiv);
				
				bFirstNewConn = false;
			}else if(data.action==1)
			{
				print('Player '+data.id+' (' + data.ip +') has left the server.');
				var cursorToDelete = document.getElementById(data.id);
				cursorToDelete.parentNode.removeChild(cursorToDelete);
			}
		}
	  }else if(received_msg.substr(0,1)=='['){
		//var received_msg = '[["6caw1qkwh",0,0]]';
		//print(received_msg);

		players = JSON.parse( received_msg );


		var html = "";

		for(var i=0, len=players.length; i < len; i++) {
			if(players[i][0]!=playerMe){
				playerCursor = document.getElementById(players[i][0]);
				
				
				if(playerCursor==null){
					createPlayerCursor(players[i][0]);
					
					playerCursor = document.getElementById(players[i][0]);
				}
				
				playerCursor.style.left = (players[i][1])+'px';		
				playerCursor.style.top = (players[i][2])+'px';
			}
		
			html += "Player: " + players[i][0] +"<br>";
		}

		playersDiv = document.getElementById('playersDiv');
		playersDiv.innerHTML = html;
	  }else{
		print(received_msg);
	  }
	}
	
	function connectToServer() {
		socket = new WebSocket('ws://'+connectToIP+':'+connectToPort);
		socket.onopen = socket_onOpen;
		socket.onclose = socket_onClose;
		socket.onmessage = socket_onMessage;	
	}
	
	connectToServer();
	
	/*
	var players = [];
	
	players.push(['foo', 0, 0]);
	players.push(['bar', 0, 0]);
	
	
	print('Players count: ' + players.length);
	
	players.splice(1, 1);
	
	print('Players count: ' + players.length);
	*/
	</script>
	
    <div class="cf"></div>
</div>
<?php
    }
}

new customPage();
