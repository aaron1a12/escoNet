<?php

class customPage extends page {
    public $title = 'Chat';
	
    function head() {
?>
<style>
    
    #chatPage { display:none; }
    
    #chatwindow, #chatinput, #chatinputBtn {
        border-radius:6px;
    }
    
    #chatwindow {
        background-color:#eee;
        height:200px; border:2px solid #ccc; padding:15px; 
        overflow-y:scroll;
        white-space:pre-line;
		word-wrap: break-word;
        word-break: break-all;
        margin-bottom:10px;
    }
	
	#chatinput {
        background-color:#fff;
        border:2px solid #ccc;
        height:40px;
        width:80%;
        float:left;
        font-size:15px;
        padding-left:10px;
        padding-right:10px;
	}
    #chatinputBtn {
        cursor:pointer;
        color:#fff;
        float:right;
        background-color:rgb(0, 144, 214);
        border:2px solid rgb(0, 120, 180);
        height:32px;
        width:160px;
        text-align:center;
        padding-top:8px;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    #chatinputBtn:hover {
        text-decoration:none;
        background-color:rgb(50, 180, 234);
        text-shadow: rgba(0,0,0,.2) 0 2px 0;
    }
    #chatinputBtn:active {
        text-decoration:none;
        background-color:rgb(0, 144, 214);
        text-shadow: rgba(0,0,0,.2) 0 2px 0;
        box-shadow: 0px 2px 20px rgba(0, 0, 0, 0.5) inset;
        padding-top:10px;
        height:30px;
        border-color: rgb(0, 80, 130);
    }

    #errordiv {
        background:pink;
        padding:10px;
        margin-top:20px;
        display:none;
    }
    
    .chatMsgUser {
        color: green;
    }

    .chatMsgLine {
        padding-bottom:20px;
    }
</style>
<?php }	
	
    function content() {
?>
<h1>Web Socket Chat</h1>


<div id="setupPage">
    <p>Please enter your name to use in the chat:</p>
    
    <br>
    <input id="userName">
    <button id="enterBtn">Enter Chat Room</button>
    
    <div id="errordiv">
        Error: Please enter a name that other users will identify you with.
    </div>
    
</div>

<div id="chatPage">
    <div id="chatwindow"></div>
    <input id="chatinput">
    <a id="chatinputBtn">Send Message</a>
    <div class="cf"></div>
</div>


<script>

    var chatwindow = document.getElementById('chatwindow');
    var chatinput = document.getElementById('chatinput');

    var username = '';



    function chatWrite( msg, author )
    {
        if(typeof author!='undefined'){
            var authorClass = '';

            if(author==username){
                authorClass = 'chatMsgUser';
            }

            author = '<span class="'+authorClass+'">' + author + ':</span> ';
        }else{
            author = '';
        }

        if(msg!='')
            chatwindow.innerHTML = chatwindow.innerHTML + '<div class="chatMsgLine">' + author + msg + '</div>';
    }

    //
    // The actual chat
    //

    var chatCon = '';

    function initChat()
    {
        chatinput.value = '';
        document.getElementById('setupPage').style.display = 'none';
        document.getElementById('chatPage').style.display = 'block';

        chatWrite( 'Opening connection to chat server...' );
        
        chatCon = new WebSocket("ws://www.rantra.com/_inc/php/chatserver.php");
        
        chatCon.onopen = function(evt) {
            chatWrite( 'Connected...' );
        }; 
    }  

    //
    // Event Listeners
    //

    document.getElementById('enterBtn').addEventListener("click", function(){
        username = document.getElementById('userName').value;
        if(username==''){
            document.getElementById('errordiv').style.display = 'block';
        }else{
            initChat();
        }

    });

    chatinput.addEventListener("keypress", function(e) {
        var key = e.keyCode ? e.keyCode : e.which;
        if(key == 13){
            chatWrite( chatinput.value, username ); 
            chatinput.value = '';
        }
    });


    document.getElementById('chatinputBtn').addEventListener("click", function(){
        chatWrite( chatinput.value, username );
        chatinput.value = '';
    });
    
</script>

<?php
    }
}

new customPage();