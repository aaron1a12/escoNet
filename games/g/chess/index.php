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
	
	
	#scene {
		font-size:0;
	}
	.ui {
		float:right;
		width:40%;
	}
	.chessboard {
		font-size:0;float:left;
	}
	.rank {
		height:12.5%;
	}
	.square {
		width:12.5%;height:100%;border:0px solid blue; display:inline-block;
	}
	</style>
	<script>
	
	var escoChess = function(containerId){
		this.container = document.getElementById(containerId);			
		
		if(typeof window.escoChessInstances =='undefined')
			window.escoChessInstances = [];
		
		window.escoChessInstances.push(this);
		this.instanceId = window.escoChessInstances.length-1;
		
		this.setup();	
	}
	
	escoChess.prototype.place = function() {
		square = document.getElementById('_'+this.instanceId+'_D4');
		//square.style.outlineOffset =  "-3px";
		//square.style.outline = '3px solid red';
		
		
		var pawn = document.createElement("IMG");
		pawn.src = 'http://media.esco.net/img/chess/pawn.svg';
		pawn.style.width = '100%';
		square.appendChild(pawn);
	};
	
	escoChess.prototype.setup = function(){
		
		this.chessArray = new Array(64)
		
		this.chessboard = document.createElement("DIV");
		this.chessboard.className = "chessboard";
		this.chessboard.style.border = "1px outset";
		this.chessboard.style.width = "500px";
		this.chessboard.style.height = "500px";
		this.container.appendChild(this.chessboard);
		
		// Create the rows
		
		var files = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
		
		for (iRow = 0; iRow < 8; iRow++) {
			
			// Ranks = row
			var rankNumber = (iRow-8)*-1;
			
			var rank = document.createElement('div');
			rank.className = "rank";
			//rank.innerHTML = 'Rank #'+rankNumber;
			
			for (iCol = 0; iCol < 8; iCol++) {
				
				var fileLetter = files[iCol];
				var square = document.createElement('DIV');
				square.className = "square";
				
				// Sum the row and column integers and check if they are odd or even.
				if(iRow+iCol & 1)	
					square.style.backgroundColor = "dimgrey"; // ODD
				else
					square.style.backgroundColor = "ivory"; // EVEN
			
				square.id = '_' + this.instanceId + '_' + fileLetter + rankNumber;
				square.title = fileLetter + rankNumber;
				
				rank.appendChild(square);
			}
			
			this.chessboard.appendChild(rank);
		}
		
		// Right side
		
		this.ui = document.createElement("DIV");
		this.ui.className = "ui";
		this.ui.innerHTML = '<button onclick="window.escoChessInstances['+this.instanceId+'].place()">Place pawn</button>';
		this.container.appendChild(this.ui);
	};
	
	

	window.onload = function(){
		var chess = new escoChess('chessDiv');
	};
	</script>
	<?php
	}
	
    function content() {
?>
<div class="widget" style="position:relative;">
    
	<div id="chessDiv"></div>
	
    <div class="cf"></div>
</div>
<?php
    }
}

new customPage();
