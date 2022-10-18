<?php

class customPage extends page {
    public $title = 'Fun Stuff';


    function content() {
?>
<div class="widget">
  <button onclick="document.getElementById('audible0').Play();">Play</button>
  <button onclick="document.getElementById('audible0').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible0).GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible0" width="200" height="200" src="http://media.esco.net/swf/audibles/goodbyes/base.us.goodbyes.split1.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible1').Play();">Play</button>
  <button onclick="document.getElementById('audible1').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible1').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible1" width="200" height="200" src="http://media.esco.net/swf/audibles/emoticats/base.us.emoticats.vacation2.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible2').Play();">Play</button>
  <button onclick="document.getElementById('audible2').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible2').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible2" width="200" height="200" src="http://media.esco.net/swf/audibles/international/philippines/base.aa.philippines2.ph_yourock.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible3').Play();">Play</button>
  <button onclick="document.getElementById('audible3').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible3').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible3" width="200" height="200" src="http://media.esco.net/swf/audibles/international/philippines/base.aa.philippines2.ph_hello.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible4').Play();">Play</button>
  <button onclick="document.getElementById('audible4').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible4').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible4" width="200" height="200" src="http://media.esco.net/swf/audibles/insults/base.us.insults.sock_09.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible5').Play();">Play</button>
  <button onclick="document.getElementById('audible5').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible5').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible5" width="200" height="200" src="http://media.esco.net/swf/audibles/international/taiwan/base.tw.smiley.smiley82.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible6').Play();">Play</button>
  <button onclick="document.getElementById('audible6').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible6').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible6" width="200" height="200" src="http://media.esco.net/swf/audibles/international/china/base.cn.emotion.1ganbade.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible7').Play();">Play</button>
  <button onclick="document.getElementById('audible7').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible7').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible7" width="200" height="200" src="http://media.esco.net/swf/audibles/international/china/base.cn.emotion.heartmelt.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible8').Play();">Play</button>
  <button onclick="document.getElementById('audible8').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible8').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible8" width="200" height="200" src="http://media.esco.net/swf/audibles/flirts/base.us.flirts.heartmelt1.swf" type="application/x-shockwave-flash"></embed>
</div>
<div class="widget">
  <button onclick="document.getElementById('audible9').Play();">Play</button>
  <button onclick="document.getElementById('audible9').StopPlay();">Pause</button>
  <button onclick="document.getElementById('audible9').GotoFrame(0);">Stop</button>
  <br>
  <embed id="audible9" width="200" height="200" src="http://media.esco.net/swf/audibles/international/german/base.de.siedler5.pilgrim3.swf" type="application/x-shockwave-flash"></embed>
</div>
<?php
    }
}

new customPage();
