<?php

class customPage extends page {
    public $title = 'Games';

    public $bHasAccount;
    public $funds;


    function init() {
        // Check if the user has an account

        $result = mysqli_query($this->link, "SELECT funds FROM esco_bank_accounts WHERE owner=" . $this->escoID);
        $rows = mysqli_num_rows($result);

        if($rows==0) $this->bHasAccount = false; else $this->bHasAccount = true;

        if($this->bHasAccount)
        $this->funds = mysqli_fetch_row($result)[0];
    }

    function content() {
?>
<?php if($this->bHasAccount) {?>

<div class="widget" style="position:relative;">
    <h1>Slot machine</h1>
    <div>Your current balance: <b id="funds-ui"><?php echo '&euro;'. money($this->funds);?></b></div>

    <div style="position:absolute; right:20px; top:30px;text-align:right;">
        <img id="loadingIcon" style="visibility:hidden;" src="/_inc/img/loading.gif">
        <button id="btnPlay" onclick="alert('The slot machine has been disabled.');">Try your luck for &euro;7</button>
		<!-- <button id="btnPlay" onclick="play();">Try your luck for &euro;7</button> -->
    </div>


    <div class="center" style="margin-top:40px; width:542px; border: 1px solid #FEE5C9; background-color: #FFF0DF;">
        <div class="slot" style="display:inline-block;position:relative; width:170px; height:170px; border:2px inset; margin:2px;background-color:#fff;">
            <div style="position:absolute;width:100%;height:100%;z-index:1;background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0), rgba(0,0,0,0.5));">
            </div>
            <div id="slot1" style="position:absolute;width:100%;height:100%;overflow:hidden;z-index:0;text-align:center;font-size:0;line-height:0;">
                <div style="height:200px"></div>
                <img src="http://media.esco.net/swf/slots/0.png">
                <img src="http://media.esco.net/swf/slots/1.png">
                <img src="http://media.esco.net/swf/slots/2.png">
                <img src="http://media.esco.net/swf/slots/3.png">
                <img src="http://media.esco.net/swf/slots/4.png">
                <img src="http://media.esco.net/swf/slots/5.png">
                <img src="http://media.esco.net/swf/slots/6.png">
                <img src="http://media.esco.net/swf/slots/7.png">
                <img src="http://media.esco.net/swf/slots/8.png">
                <img src="http://media.esco.net/swf/slots/9.png">
                <img src="http://media.esco.net/swf/slots/10.png">
                <img src="http://media.esco.net/swf/slots/11.png">
                <img src="http://media.esco.net/swf/slots/12.png">
                <img src="http://media.esco.net/swf/slots/13.png">
                <img src="http://media.esco.net/swf/slots/14.png">
                <img src="http://media.esco.net/swf/slots/15.png">
                <img src="http://media.esco.net/swf/slots/16.png">
                <!--<img src="http://media.esco.net/swf/slots/17.png">-->
                <!--<img src="http://media.esco.net/swf/slots/18.png">-->
                <!--<img src="http://media.esco.net/swf/slots/19.png">-->
                <!--<img src="http://media.esco.net/swf/slots/20.png">-->
                <div style="height:200px"></div>
            </div>
        </div>

        <div class="slot" style="display:inline-block;position:relative; width:170px; height:170px; border:2px inset; margin:2px;background-color:#fff;">
            <div style="position:absolute;width:100%;height:100%;z-index:1;background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0), rgba(0,0,0,0.5));">
            </div>
            <div id="slot2" style="position:absolute;width:100%;height:100%;overflow:hidden;z-index:0;text-align:center;font-size:0;line-height:0;">
                <div style="height:200px"></div>
                <img src="http://media.esco.net/swf/slots/0.png">
                <img src="http://media.esco.net/swf/slots/1.png">
                <img src="http://media.esco.net/swf/slots/2.png">
                <img src="http://media.esco.net/swf/slots/3.png">
                <img src="http://media.esco.net/swf/slots/4.png">
                <img src="http://media.esco.net/swf/slots/5.png">
                <img src="http://media.esco.net/swf/slots/6.png">
                <img src="http://media.esco.net/swf/slots/7.png">
                <img src="http://media.esco.net/swf/slots/8.png">
                <img src="http://media.esco.net/swf/slots/9.png">
                <img src="http://media.esco.net/swf/slots/10.png">
                <img src="http://media.esco.net/swf/slots/11.png">
                <img src="http://media.esco.net/swf/slots/12.png">
                <img src="http://media.esco.net/swf/slots/13.png">
                <img src="http://media.esco.net/swf/slots/14.png">
                <img src="http://media.esco.net/swf/slots/15.png">
                <img src="http://media.esco.net/swf/slots/16.png">
                <!--<img src="http://media.esco.net/swf/slots/17.png">-->
                <!--<img src="http://media.esco.net/swf/slots/18.png">-->
                <!--<img src="http://media.esco.net/swf/slots/19.png">-->
                <!--<img src="http://media.esco.net/swf/slots/20.png">-->
                <div style="height:200px"></div>
            </div>
        </div>

        <div class="slot" style="display:inline-block;position:relative; width:170px; height:170px; border:2px inset; margin:2px;background-color:#fff;">
            <div style="position:absolute;width:100%;height:100%;z-index:1;background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0), rgba(0,0,0,0.5));">
            </div>
            <div id="slot3" style="position:absolute;width:100%;height:100%;overflow:hidden;z-index:0;text-align:center;font-size:0;line-height:0;">
                <div style="height:200px"></div>
                <img src="http://media.esco.net/swf/slots/0.png">
                <img src="http://media.esco.net/swf/slots/1.png">
                <img src="http://media.esco.net/swf/slots/2.png">
                <img src="http://media.esco.net/swf/slots/3.png">
                <img src="http://media.esco.net/swf/slots/4.png">
                <img src="http://media.esco.net/swf/slots/5.png">
                <img src="http://media.esco.net/swf/slots/6.png">
                <img src="http://media.esco.net/swf/slots/7.png">
                <img src="http://media.esco.net/swf/slots/8.png">
                <img src="http://media.esco.net/swf/slots/9.png">
                <img src="http://media.esco.net/swf/slots/10.png">
                <img src="http://media.esco.net/swf/slots/11.png">
                <img src="http://media.esco.net/swf/slots/12.png">
                <img src="http://media.esco.net/swf/slots/13.png">
                <img src="http://media.esco.net/swf/slots/14.png">
                <img src="http://media.esco.net/swf/slots/15.png">
                <img src="http://media.esco.net/swf/slots/16.png">
                <!--<img src="http://media.esco.net/swf/slots/17.png">-->
                <!--<img src="http://media.esco.net/swf/slots/18.png">-->
                <!--<img src="http://media.esco.net/swf/slots/19.png">-->
                <!--<img src="http://media.esco.net/swf/slots/20.png">-->
                <div style="height:200px"></div>
            </div>
        </div>
    </div>
    <br>
    Outcome: &nbsp;<b id="outcome-ui" style="font-size:20px;"></b>
</div>

<div class="widget" style="position:relative;">
    <h2>How to play</h2>
    <p>The objective of the game is to get as many slots as possible with the same image. Two will get you &euro;50 and three will earn you the jackpot (&euro;1000).</p>
    <p>You can play up to <b>10</b> times a day.</p>
    <small>Odds of finding two matches: 1 in 256. Odds of winning the jackpot: 1 in 4,096.</small>
</div>
<script>
    var slotImageHeight = 128;

    var slot1 = document.getElementById('slot1');
    var slot2 = document.getElementById('slot2');
    var slot3 = document.getElementById('slot3');

    outcomeUi = document.getElementById('outcome-ui');

    var btnPlay = document.getElementById('btnPlay');
    var loadingIcon = document.getElementById('loadingIcon');

    var dataLoaded=0;

    slot1.scrollTop = 0;
    slot2.scrollTop = 0;
    slot3.scrollTop = 0;

    function play()
    {
        slot1.scrollTop = 0;
        slot2.scrollTop = 0;
        slot3.scrollTop = 0;

        outcomeUi.style.fontSize = '20px';
        outcomeUi.innerHTML = '';

        btnPlay.disabled=true;
        btnPlay.style.opacity = '0.5';
        loadingIcon.style.visibility = 'visible';

        $.ajax({
            type: "POST",
            url : "/api/slot-play.php",
            data: { frommachine:1 },
            dataType : 'json'
        }).done(function(data){
            if(data.ok){
                //
                // Animate slots
                //

                //data.matches = 3;

                dataLoaded = data;

                setTimeout(function(){
                    $(slot1).animate({
					   scrollTop: (slotImageHeight*dataLoaded.slots[0]) + 180 +"px"
                    }, 1700, 'easeOutBounce');
                }, 0);

                setTimeout(function(){
                    $(slot2).animate({
					   scrollTop: (slotImageHeight*dataLoaded.slots[1]) + 180 +"px"
                    }, 1700, 'easeOutBounce');
                }, 500);

                setTimeout(function(){
                    $(slot3).animate({
					   scrollTop: (slotImageHeight*dataLoaded.slots[2]) + 180 +"px"
                    }, 1700, 'easeOutBounce', function(){

                            document.getElementById('funds-ui').innerHTML = '&euro;'+dataLoaded.funds;

							btnPlay.disabled=false;
							btnPlay.style.opacity = '1';
							loadingIcon.style.visibility = 'hidden';

                            if(dataLoaded.matches==1){
                                outcomeUi.style.color = 'red';
                                outcomeUi.innerHTML = 'You lost &euro;7!';
                            }
                            else if (dataLoaded.matches==2){
                                outcomeUi.style.color = 'green';
                                outcomeUi.innerHTML = 'You won &euro;50!';
                            }
                            else if(dataLoaded.matches==3){
                                outcomeUi.style.color = 'green';
                                outcomeUi.style.fontSize = '80px';
                                outcomeUi.innerHTML = '<br>You WON &euro;1000!';
                            }
                    });
                }, 1000);


            }
            else
            {
                btnPlay.disabled=false;
                btnPlay.style.opacity = '1';
                loadingIcon.style.visibility = 'hidden';

                outcomeUi.style.color = 'red';
                outcomeUi.innerHTML = 'Cannot play! Not enough funds or max plays for today reached.';
            }
        });
    }

</script>
<?php }else{ ?>
<div class="widget" style="position:relative;">
    <h1>No account</h1>
    <div>You must set up a bank account first to play!</div>
</div>
<?php } ?>
<?php
    }
}

new customPage();
