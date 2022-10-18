<?php

class customPage extends page {
    public $title = 'Edit Home Sections';	
    public $private = true;
    
    function init()
    {
        
        //echo $_COOKIE["TestCookie"];
    }
    
    function head(){
?>
<script>
    function editNews(actionCode, input)
    {
        document.getElementById('newsLoader').style.visibility = 'visible';
        document.getElementById('newsError').style.visibility = 'hidden';
        
        if( typeof actionCode == 'undefined') var actionCode = 0;
        if( typeof input == 'undefined') var input = '';
        
        var responseData;
        $.ajax({
            type: "POST",
            url : "/api/edit-news.php",
            data: { action:actionCode, data:input },
            dataType : 'json'
        }).done(function(data){
            document.getElementById('newsLoader').style.visibility = 'hidden';
            
            if(actionCode!=2) document.getElementById('input_headline').value = '';
            
            var ul = document.getElementById('headlines');
            
            while(ul.firstChild)
            {
                ul.removeChild(ul.firstChild);
            }
            

            //for (var row in data)
            var datalength =  data.length;
            for(var i = 0; i < datalength; i++)
            {
                var li = document.createElement('LI');
                li.innerHTML = data[i][1] + ' - ';
                
                var anchor = document.createElement('A');
                anchor.innerHTML = 'Remove';
                anchor.setAttribute('data-id', data[i][0]);
                anchor.href = 'javascript:void(0);';
                anchor.onclick = function(){
                    editNews(2, this.getAttribute('data-id'));
                };
                
                li.appendChild( anchor );
                ul.appendChild( li );
            }
           
        }).fail(function(){
            document.getElementById('newsLoader').style.visibility = 'hidden';
            document.getElementById('newsError').style.visibility = 'visible';
            
        });
    }
    
    
    function editTrending(actionCode, input)
    {
        document.getElementById('trendingLoader').style.visibility = 'visible';
        document.getElementById('trendingError').style.visibility = 'hidden';
        
        if( typeof actionCode == 'undefined') var actionCode = 0;
        if( typeof input == 'undefined') var input = '';

        
        var responseData;
        $.ajax({
            type: "POST",
            url : "/api/edit-trending.php",
            data: { action:actionCode, data:input },
            dataType : 'json'
        }).done(function(data){
            document.getElementById('trendingLoader').style.visibility = 'hidden';
            if(actionCode!=2) document.getElementById('input_trend').value = '';

            var ul = document.getElementById('trending');

            while(ul.firstChild)
            {
                ul.removeChild(ul.firstChild);
            }


            //for (var row in data)
            var datalength =  data.length;
            for(var i = 0; i < datalength; i++)
            {
                var li = document.createElement('LI');
                li.innerHTML = data[i][1] + ' - ';

                var anchor = document.createElement('A');
                anchor.innerHTML = 'Remove';
                anchor.setAttribute('data-id', data[i][0]);
                anchor.href = 'javascript:void(0);';
                anchor.onclick = function(){
                    editTrending(2, this.getAttribute('data-id'));
                };

                li.appendChild( anchor );
                ul.appendChild( li );
            }
        }).fail(function(){
            document.getElementById('trendingLoader').style.visibility = 'hidden';
            document.getElementById('trendingError').style.visibility = 'visible';
            
        });
    }    
    
    
    $(document).ready(function(){
        editNews();
        editTrending();
        
            $( "#input_headline" ).keypress(function(event) {
                if ( event.which == 13 ) {
                    event.preventDefault();
                    editNews( 1, document.getElementById('input_headline').value );
                }
            });  

            $( "#input_trend" ).keypress(function(event) {
                if ( event.which == 13 ) {
                    event.preventDefault();
                    editTrending( 1, document.getElementById('input_trend').value );
                }
            });         
    }); 
    
    

</script>
<style>
    #trendingLoader, #newsLoader, #trendingError, #newsError{visibility:hidden;}
</style>
<?php
    }
    
	
    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>
<div class="widget" style="position:relative;">
    <h1>Trending Now</h1>
    Add new keyword: &nbsp;&nbsp; <input id="input_trend" style="width:400px;" maxlength="25"><button onclick="editTrending( 1, document.getElementById('input_trend').value );">Add</button>
    <img id="trendingLoader" src="/_inc/img/loading.gif">
    <div class="error" id="trendingError" style="border-radius:6px;font-weight:bold;margin-top:5px;"><div style="padding:5px;">Max limit (12) reached. Try deleting some entries.</div></div>
    
    <ol id="trending">
    </ol>
</div>

<div class="widget" style="position:relative;">
    <h1>News Headlines</h1>
    
    
    
    Add new headline: &nbsp;&nbsp; <input id="input_headline" style="width:400px;" maxlength="250"><button onclick="editNews( 1, document.getElementById('input_headline').value );">Add</button>
    <img id="newsLoader" src="/_inc/img/loading.gif">
    <div class="error" id="newsError" style="border-radius:6px;font-weight:bold;margin-top:5px;"><div style="padding:5px;">Max limit (15) reached. Try deleting some entries.</div></div>
    
    <ol id="headlines">
    </ol>
    
</div>
<?php
    }
}

new customPage();