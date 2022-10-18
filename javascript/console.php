<?php

class customPage extends page {
    public $title = 'Home';
    function head() {
?>
<style>
    #console {
        background-color:#000;
        height:400px; border-radius:12px;border:4px solid #ccc; padding:15px; 
        overflow-y:hidden;
        white-space:pre-line;
		word-wrap: break-word;
        word-break: break-all;
		font-family:Monospace;
    }
    
    #console a, #console span{
        vertical-align:middle;
    }
	
	#outputSpan, #stdLine, #stdLineAppend, #blinkingCursor {
		color:#fff;
		white-space:pre-line;
		word-wrap: break-word;
        word-break: break-all;
		font-family:Monospace;
	}
    
    #blinkingCursor{color:#000;}
	
	#stdLine {
        /*background-color:#333;*/
    }
	
    #outputSpan::selection {
        background-color:#fff; color: #000;
    }
    #outputSpan::-moz-selection {
        background-color:#fff; color: #000;
    }
	
	#stdLine::selection {
        background-color:#fff; color: #000;
    }
    #stdLine::-moz-selection {
        background-color:#fff; color: #000;
    }
    
    #stdLineAppend::selection {
        background-color:#fff; color: #000;
    }
    #stdLineAppend::-moz-selection {
        background-color:#fff; color: #000;
    }
</style>
<?php }
    function content() {
?>
<h1>Console Simulator</h1>

<div id="console"></div>


<script>
// Settings
var BUFFER_LENGTH = 1500;
    
var BLINKING_CURSOR_INTERVAL = 300;    
var BLINKING_CURSOR_COLOR = "white";    
    
//
// Don't edit bellow
//
    
var UP = 0;
var DOWN = 1;

// We start outputting, not inputting
var bInput = false;

// Recent commands array for quick typing
var recentCommands = [];
var currentRecentCmd = 0;

var terminalWindow = document.getElementById('console');

// Console outputs to this span
var outputSpan = document.createElement("span");
outputSpan.id = 'outputSpan';

// User types into this span
var stdLine = document.createElement("span");
stdLine.id = 'stdLine';

var blinkingCursor = document.createElement("span");
blinkingCursor.style.width = "7px";
blinkingCursor.style.height = "15px";
blinkingCursor.style.display = "inline-block";
blinkingCursor.style.backgroundColor = "transparent";
blinkingCursor.setAttribute("data-blinked", "1");
blinkingCursor.innerHTML = '&nbsp;';
blinkingCursor.id = 'blinkingCursor';

// This span gets filled up when the user moves the blinking cursor up
// the string. Example:
// [stdLine][blinkingCursor][stdLineAppend]
// Hello [] world
var stdLineAppend = document.createElement("span");
stdLineAppend.id = 'stdLineAppend';
    
terminalWindow.appendChild(outputSpan); 
terminalWindow.appendChild(stdLine); 
terminalWindow.appendChild(blinkingCursor); 
terminalWindow.appendChild(stdLineAppend); 
    
var getConsole = function (){ return document.getElementById('console');};


setInterval(function(){
    var blinkingCursor = document.getElementById('blinkingCursor');
    if(blinkingCursor.getAttribute('data-blinked')=='0'){
        blinkingCursor.setAttribute('data-blinked', '1')
        blinkingCursor.style.backgroundColor = "transparent";
    }
    else{
        blinkingCursor.setAttribute('data-blinked', '0')
        blinkingCursor.style.backgroundColor = BLINKING_CURSOR_COLOR;
    }    
}, BLINKING_CURSOR_INTERVAL);
    
    
function echo(msg)
{
    //var terminalWindow = getConsole();

    var outputSpan = document.getElementById('outputSpan');
    
    outputSpan.innerHTML += msg;

    
    if( outputSpan.innerHTML.length > BUFFER_LENGTH )
    {
        
        outputSpan.innerHTML = outputSpan.innerHTML.substr( outputSpan.innerHTML.length - BUFFER_LENGTH );
        
    }
    
	

    /*
    DEPRECATED
    
	var stdLine = document.getElementById('stdLine');
	var blinkingCursor = document.getElementById('blinkingCursor'); 
	
    // Move to end
	stdLine.parentNode.appendChild(stdLine);
	// Move to end
	blinkingCursor.parentNode.appendChild(blinkingCursor); 
    */
    terminalWindow.scrollTop = terminalWindow.scrollHeight;
}

function printHelp()
{
	echo("Help For Javascript Console \n Functions \n ========= \n echo( msg ); \t\t- Prints a message to the console.\ncount \t\t- Counts indefinitely.");
}

// TODO: FIX ME!
    
function stdIn(msg)
{
	window.getSelection().removeAllRanges();
	
	var stdLine = document.getElementById('stdLine');  
	stdLine.innerHTML += msg;

    terminalWindow.scrollTop = terminalWindow.scrollHeight;
}

function stdBack()
{
	var stdLine = document.getElementById('stdLine');  

	stdLine.innerHTML = stdLine.innerHTML.substr(0, stdLine.innerHTML.length-1);
}
    
function stdDelete()
{
    var blinkingCursor = document.getElementById('blinkingCursor'); 
    var stdLineAppend = document.getElementById('stdLineAppend'); 
    
    blinkingCursor.innerHTML = stdLineAppend.innerHTML.substr(0, 1);
    stdLineAppend.innerHTML = stdLineAppend.innerHTML.substr(1);
}
    
function stdMoveInput( direction )
{
    var stdLine = document.getElementById('stdLine');  
    var blinkingCursor = document.getElementById('blinkingCursor'); 
    var stdLineAppend = document.getElementById('stdLineAppend'); 
    
    if(direction==UP)
    {
        // Last character of the std line plus contents
        blinkingCursor.innerHTML = stdLine.innerHTML.substr(stdLine.innerHTML.length-1) + blinkingCursor.innerHTML;
        
        if(blinkingCursor.innerHTML.length>1)
        {
            // Move rest of the characters to stdLineAppend
            stdLineAppend.innerHTML = blinkingCursor.innerHTML.substr(1) + stdLineAppend.innerHTML;
            
            // Get only the first character
            blinkingCursor.innerHTML = blinkingCursor.innerHTML.substr(0, 1);
        }
        
        
        stdLine.innerHTML = stdLine.innerHTML.substr(0, stdLine.innerHTML.length-1);
    }
    else // IF DIRECTION == DOWN
    {
        //
        // When the we haven't reached the end
        //
        
        if(stdLineAppend.innerHTML.substr(0, 1)!='&'&&stdLineAppend.innerHTML!=''&&stdLineAppend.innerHTML!=' ')
        {
            stdLine.innerHTML = stdLine.innerHTML + blinkingCursor.innerHTML;
            blinkingCursor.innerHTML = stdLineAppend.innerHTML.substr(0, 1);
            stdLineAppend.innerHTML = stdLineAppend.innerHTML.substr(1);
        }
        else // End Of Line
        {            
            stdLine.innerHTML = stdLine.innerHTML + blinkingCursor.innerHTML;
            blinkingCursor.innerHTML = '';
            stdLineAppend.innerHTML = '';
        }
    }
}

function stdEval()
{
    // NOTE: This won't work!
	var stdLine = document.getElementById('stdLine');
    var blinkingCursor = document.getElementById('blinkingCursor'); 
    var stdLineAppend = document.getElementById('stdLineAppend'); 
    
    var blinkingCursorContent = blinkingCursor.innerHTML;
    var stdLineAppendContent = stdLineAppend.innerHTML;
    
    blinkingCursor.innerHTML = '';
    stdLineAppend.innerHTML = '';
    
    if(blinkingCursorContent=='&nbsp;')
        blinkingCursorContent = ' ';
    
    if(stdLineAppendContent=='&nbsp;')
        stdLineAppendContent = '';
    
    stdLine.innerHTML = stdLine.innerHTML + blinkingCursorContent + stdLineAppendContent;
    
   

	
	if(stdLine.innerHTML!='')
	{
		var userCommand = stdLine.innerHTML;
            
        
        // Empty the current input line
		stdLine.innerHTML = '';
        
        // Save the command
        recentCommands.push( userCommand );
        currentRecentCmd = recentCommands.length;


        
		echo( userCommand + '\n' );

		// Parse User Command Here
		/*
		if(userCommand=='hi'){
			echo("Uh? Hello to you too.");
			stdIn_Enable();
		}else{
			echo("Unknown Command");
			stdIn_Enable();
		}*/
		
		switch(userCommand)
		{
			default:
				try {
					eval(userCommand);
				}
				catch(err) {
					echo(err.message);
				}
                
                stdIn_Enable();	
			break;

			case "help":
				printHelp();
                stdIn_Enable();	
			break;
            
            case "count":
                    window.tmpi = 0;
                    setInterval(function(){ window.tmpi++; echo(window.tmpi+'\n'); }, 0);
                break;
		}

		

		
	}
	else
	{
		stdIn_Enable();
	}
	
}

function stdIn_Enable()
{
    // CHANGES: New format.
	echo("\n>");
	// Accept typing
	bInput = true;
}

function stdIn_Disable()
{
	// Accept typing
	bInput = false;
}
    
function scrollConsole(bScrollDown)
{
    var terminalWindow = getConsole();
    if(bScrollDown)
    {
        terminalWindow.scrollTop = terminalWindow.scrollTop + (terminalWindow.offsetHeight / 2);
    }
    else
    {
        terminalWindow.scrollTop = terminalWindow.scrollTop - (terminalWindow.offsetHeight / 2);
    }
}

function scrollCommands( iDirection )
{
    if(recentCommands.length!=0)
    {
        if(iDirection==0) // 0 = up, 1 = down
            currentRecentCmd--;
        else
            currentRecentCmd++;

        // validate range
        if(currentRecentCmd>=recentCommands.length){
            currentRecentCmd = recentCommands.length - 1;
        }else if(currentRecentCmd<1){
            currentRecentCmd=0;
        }
        document.getElementById('stdLine').innerHTML = recentCommands[currentRecentCmd];
    }
}
    
function initConsole()
{    
    window.onkeydown = function(e) {
        var key = e.keyCode ? e.keyCode : e.which;
        
        var blinkingCursor = document.getElementById('blinkingCursor');  
        blinkingCursor.style.backgroundColor = BLINKING_CURSOR_COLOR;
        //echo( 'Key pressed '+key+' (' + String.fromCharCode(key) +')\n' );
        
        switch(key)
        {
                case 33:
                    scrollConsole(false);
                break;
                case 34:
                    scrollConsole(true);
                break;
            
                case 37: // LEFT ARROW
                    stdMoveInput( UP );
                break;
                
                case 38: // UP ARROW
                    e.preventDefault();
                    
                    scrollCommands( UP );
                break;
                
                case 39: // RIGHT ARROW
                    stdMoveInput( DOWN );
                break;

                case 40: // DOWN ARROW
                    e.preventDefault();
                    
                    scrollCommands( DOWN );
                case 46: //DELETE
                    stdDelete();
                break;
        }

    };
    
    window.addEventListener("keydown", function(e) {
        // 33 = PAGE UP, 34 = PAGE DOWN
        if([33, 34].indexOf(e.keyCode) > -1) {
            e.preventDefault();
        }
    }, false);
    
    window.addEventListener("keypress", function(e) {
        var key = e.keyCode ? e.keyCode : e.which;
       
		
	    if(bInput){
	
			
			var terminalWindow = getConsole();
			
			switch(key)
			{
				case 13: // ENTER
					//echo("\n");
                    
					stdIn_Disable();
					stdEval();
				break;
				case 8: // BACKSPACE
					e.preventDefault();
					
					stdBack();
					
					var blinkingCursor = document.getElementById('blinkingCursor');
					terminalWindow.insertBefore(blinkingCursor, blinkingCursor);

					//alert(terminalWindow.textContent)
					//terminalWindow.textContent=terminalWindow.textContent///substr(0,terminalWindow.textContent.length-1);
				break;
                   
                case 37: // LEFT ARROW
                break;
                case 39: // RIGHT ARROW
                break;
                    
                case 46: // DELETE
                break;
				/*
				case 116: // F5
				break;
				*/
				
				default:
					stdIn( String.fromCharCode(key) );
                    
					e.preventDefault();
				break;
			}
		}
    }, false);
    
    
    
    
    echo( '// Javascript Console. Type "help" for more info.' );
	
	stdIn_Enable();
	
    /*for(i=0; i<=1024; i++)
    {
        echo(i + '\n');
    }*/
    
}
    
initConsole();    
    

    
    

</script>

<?php
    }
}

new customPage();