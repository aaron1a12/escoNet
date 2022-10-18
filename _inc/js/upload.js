function $id(id) {
            return document.getElementById(id);
}

// output information
function Output(msg, color) {
    var m = $id("upload-state");
    m.innerHTML = msg;

    if(!color){
        m.style.color = '#fff';
        m.style.textShadow = '2px 2px 2px #000';
    }else{
        m.style.color = '#000';
        m.style.textShadow = '2px 2px 2px #fff';
    }
}


function OutputError(msg) {
    $id("progress-error").style.display = 'block';
    $id("progress-error").innerHTML = '<div>'+msg+'</div>';
}

function HideError() {
    $id("progress-error").style.display = 'none';
}

function FileSelectHandler(e)
{
    // fetch FileList object
    var files = e.target.files || e.dataTransfer.files;

    // process all File objects
    for (var i = 0, f; f = files[i]; i++) {
        UploadFile(f);
        // TODO: Must call UploadFile() one at a time!
    }
}


function UploadFile(file)
{
   //HideError();

    $id("upload-file").disabled = true;

    var xhr = new XMLHttpRequest();

        var progressBar = $id("upload-progress");
    
        // progress bar
        xhr.upload.addEventListener("progress", function(e) {
            var pc = parseInt(100 - (e.loaded / e.total * 100));
            progressBar.value = 100 - pc;
            Output(100 - pc + '%');
        }, false);

        // file received/failed
        xhr.onreadystatechange = function(e) {
            if (xhr.readyState == 4) {
                //progressBar.value = 0;
                if(xhr.status==200){
                    ////////////////////////////////////////////

                    // The script will generate a unique name for the image and return it in the response.
                    var imageName = xhr.responseText;

                    // Now we need to convert the images and install them in the site.
                    xhr2 = new XMLHttpRequest();

                    xhr2.onreadystatechange = function(e) {
                        if (xhr2.readyState == 1) {
                            Output('Making Thumbnails, please wait...');
                        }else if(xhr.readyState == 4) {
                            if(xhr.status==200){
                                Output( 'Done', 1 ); // Done
                                progressBar.value = 0;
                                
                                alert('Done');

                                //AddThumbnail( imageName, 1 );

                                $id("upload-file").disabled = false;

                                xhr2.onreadystatechange = function(e) {}; // prevents firing AddThumbnail multiple times
                            }
                        }
                    };

                    xhr2.open("GET", '/api/resize.php', true);
                    xhr2.setRequestHeader("X-IMAGE-ID", imageName);
                    xhr2.send();

                    ////////////////////////////////////////////

                    // if(xhr.status==200){
                }else{
                    OutputError('Error Uploading Images');
                }
            }
        };

        // start upload
        xhr.open("POST", $id("upload-form").action, true);
        xhr.setRequestHeader("X_FILENAME", file.name);
        xhr.send(file);
}

function init() {

    var upload_file = $id("upload-file");

    // file select
    upload_file.addEventListener("change", FileSelectHandler, false);

    // is XHR2 available?
    var xhr = new XMLHttpRequest();

}

    
$(document).ready(function(){
    init();
}); 