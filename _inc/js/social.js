function post()
{
}


/*
    function editNews(actionCode, input)
    {
        if( typeof actionCode == 'undefined') var actionCode = 0;
        if( typeof input == 'undefined') var input = '';
        
        var responseData;
        $.ajax({
            type: "POST",
            url : "/api/edit-news.php",
            data: { action:actionCode, data:input },
            dataType : 'json'
        }).done(function(data){
            
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
                anchor.href = '#';
                anchor.onclick = function(){
                    editNews(2, this.getAttribute('data-id'));
                };
                
                li.appendChild( anchor );
                ul.appendChild( li );
            }
           
        });

    
    }
    
    
    $(document).ready(function(){
        editNews();
    }); */