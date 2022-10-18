function searchToggle(e) {
    "use strict";
    if (e.value === '') {
        e.className = 'searchBar-off';
    } else {
        e.className = 'searchBar-on';
    }
}

function showNotifications()
{
    var menu = document.getElementById('notifications-menu');
    var blockLayer = document.getElementById('block-layer');
    var notifyBtn = document.getElementById('notifyBtn');

    menu.style.display = 'block';
    blockLayer.style.display = 'block';

    var offset = $( notifyBtn ).offset();
    var height = $( notifyBtn ).height();

    menu.style.left=(offset.left-$( menu ).outerWidth())+$( notifyBtn ).outerWidth()+'px';
    menu.style.top=offset.top+height+20+'px';
}

function hidePopups()
{
    document.getElementById('notifications-menu').style.display = 'none';
    document.getElementById('block-layer').style.display = 'none';
}

function heartbeat()
{
        $.ajax({
            type: "POST",
            url : "/api/notifications.php",
            data: { shoutbox_state:esco.shoutbox.active, shoutbox_lastid:esco.shoutbox.lastid },
            dataType : 'json'
        }).done(function(data){
            //
            // Shoutbox
            //
            if(esco.shoutbox.active==1){
              esco.shoutbox.lastid = Number(data['last_shout']);
              addShouts(data['shouts'].reverse());
            }


            // Compare to our previous value
            if(Number(data['updated'])==1 && esco.updated==0 )
            {
                // Switch to alert mode
                $( "#notifyBtn" ).css("backgroundColor","#7722bb");
                $( "#notifyBtnLayer" ).css("opacity","0.3");
                document.getElementById('notifyBtnIcon').src="/_inc/img/icons/exclamation.png";

                // Play Sound
                document.getElementById('message_sound').play();

                esco.updatedBlinkerHandle = setInterval(function(){

                    //$( "#spinner" ).css("backgrounColor","#5c1993");
                    $( "#notifyBtnLayer" ).animate({
                        opacity: 0.6
                    }, 100, function(){
                        $( "#notifyBtnLayer" ).animate({
                            opacity: 0.3
                        }, 100);
                    });

                }, 1000);

                document.getElementById('profile-notifications').innerHTML = '<div onclick="location.href=\''+esco.escoProfileURL+'\';">You have new comment(s) on your profile</div>';

                // Update our value
                esco.updated = 1;
            }
            else if(Number(data['updated'])==0 && esco.updated==1 )
            {
                // Switch to normal mode
                document.getElementById('notifyBtnIcon').src="/_inc/img/icons/at-sign-balloon.png";
                $( "#notifyBtn" ).css("backgroundColor","#5c1993");
                $( "#notifyBtnLayer" ).css("opacity","0");
                clearInterval(esco.updatedBlinkerHandle);

                document.getElementById('profile-notifications').innerHTML = '<div>No new comments on your profile</div>';

                // Update our value
                esco.updated = 0;
            }

            esco.notifications = data;

            var datalength =  data.length;
            //alert(datalength);
            for(var i = 0; i < datalength; i++)
            {
            }

        });
}


$(document).ready(function(){
    document.getElementById('profile-notifications').innerHTML = '<div>No new comments on your profile</div>';

    if(esco.loggedIn)
    {
        //
        // Social Notifications
        //

        if(esco.shoutbox.active==0)
          var heartbeatInterval = 5000;
        else
          var heartbeatInterval = 2500;

        heartbeat();

        setInterval(function(){
            heartbeat();
        }, heartbeatInterval);

    }
});
