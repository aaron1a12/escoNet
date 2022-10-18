var MAX_FILE_SIZE = 5242880;

function $id(id) {
            return document.getElementById(id);
}

// output information
function Output(msg, color) {
    var m = $id("upload-status");
    m.innerHTML = msg;
}


function OutputError(msg) {
    $id("progress-error").style.display = 'block';
    $id("progress-error").innerHTML = '<div>&mdash '+msg+'</div>';
}

function HideError() {
    $id("progress-error").style.display = 'none';
}

function clientError(msg) {
    var parent = document.getElementById('clientErrorMessages');
    var div = document.createElement('div');
    div.style.border = "5px solid transparent";
    div.innerHTML = msg;
    parent.appendChild( div );
}

function hideClientErrors()
{
    document.getElementById('clientErrorMessages').innerHTML = '';
}

function showSuccess()
{
    photoUploaderWizard.show( 'success' );
}

var filesToUpload;

function FileSelectHandler(e)
{  
    hideClientErrors();
    
    var errors = [];
    
    var fileListDiv = document.getElementById('filesList');
    fileListDiv.innerHTML = '';
    
    // fetch FileList object
    var files = e.target.files || e.dataTransfer.files;

    filesToUpload = [].slice.call( files );
    
    var filesAddedToDiv = 0;
    
    // process all File objects
    for (var i = 0, f; f = files[i]; i++) {        
        var extension = f.name.split('.');
        extension = extension[extension.length-1];
        extension = extension.toLowerCase();

        try{
            if(f.size > MAX_FILE_SIZE)
                errors.push('Some images are too large!');      
        }catch(err){
            errors.push('File does not exist.');
        }
        
        
        // if( extension!='jpg' && extension!=='jpeg' && extension!='png' && extension!='gif')
        if( extension!='jpg' && extension!=='jpeg' && extension!='gif'  )
            errors.push('Bad format. Only JPEG and GIF are supported.');
        
        var fileDiv = document.createElement('DIV');
        fileDiv.innerHTML = f.name;
        fileListDiv.appendChild(fileDiv);
        filesAddedToDiv++;
    }

    
    if(errors.length==0)
    {
        photoUploaderWizard.show( 'page2' );
    }
    else
    {
        var errorCount = errors.length;
        for (var i=0; i<errorCount; i++) {  
            clientError( errors[i] );
        }
    }
    /*
    else
    {
        document.getElementById('serverErrorMessages').innerHTML = '<div style="padding:15px;">Invalid file selected</div>';
        photoUploaderWizard.show( 'error' );
    }*/
    
}


function UploadAllFiles()
{/*
    for (var i = 0, f; f = filesToUpload[i]; i++) {
    
        
        var extension = f.name.split('.');
        extension = extension[extension.length-1];
        extension = extension.toLowerCase();
        
        
        if(extension=='jpg'||extension=='jpeg'||extension=='png'||extension=='gif')
        {
            UploadFile(f);
        }
        // TODO: Must call UploadFile() one at a time!
    }*/
}


function UploadFiles()
{    
    var progressBar = $id("progress-bar-div");
    
    if( typeof filesToUpload[0] !='undefined' )
    {    
        progressBar.style.width = "0%";
        Output('Uploading "'+filesToUpload[0].name+'"...');

        var xhr = new XMLHttpRequest();

        

        // progress bar
        xhr.upload.addEventListener("progress", function(e) {
            var pc = parseInt(100 - (e.loaded / e.total * 100));


            //progressBar.value = 100 - pc;
            progressBar.style.width = 100 - pc + '%';
            //$( progressBar ).animate({ width: 100 - pc +'%'}, 100, 'swing', function() {
              
            //});

            //Output(100 - pc + '%');
        }, false);

        // file received/failed
        xhr.onreadystatechange = function(e) {
            if (xhr.readyState == 4) {
                //progressBar.value = 0;
                if(xhr.status==200){
                    
                    //
                    // Main Upload Complete
                    //
                    
                    var imageName = xhr.responseText;
                    
                    progressBar.style.width = "0%";
                    
                    // Resize the images

                    var ORIGINAL_SIZE = 0;  
                    var LARGE_SIZE = 1;   
                    var COVER_SIZE = 2;       
                    var THUMB_SIZE = 3;      
                    var SMALL_SIZE = 4;       
                    
                    var prog = 0;
                    var progIncrease = 20;
                    
                    ResizeFile(imageName, ORIGINAL_SIZE, function(){
                        prog=prog+progIncrease; $( progressBar ).animate({ width: prog +'%'}, 400, 'swing', function() {});
                        
                        ResizeFile(imageName, LARGE_SIZE, function(){
                            prog=prog+progIncrease; $( progressBar ).animate({ width: prog +'%'}, 400, 'swing', function() {});

                            ResizeFile(imageName, COVER_SIZE, function(){
                                prog=prog+progIncrease; $( progressBar ).animate({ width: prog +'%'}, 400, 'swing', function() {});

                                ResizeFile(imageName, THUMB_SIZE, function(){
                                    prog=prog+progIncrease; $( progressBar ).animate({ width: prog +'%'}, 400, 'swing', function() {});

                                    ResizeFile(imageName, SMALL_SIZE, function(){
                                        $( progressBar ).animate({ width: '100%'}, 100, 'swing', function() {
                                            progressBar.style.width = "0%";
                                        });

                                        // Upload the next file!
                                        if(filesToUpload.length>0){filesToUpload.shift();UploadFiles();}else{
                                            progressBar.style.width = "0%";
                                            Output('Success!');

                                            showSuccess();
                                            //location.href = esco.escoProfileURL+'/photos';
                                        }

                                    });                                
                                });
                            });
                        });
                    });
                    
                    
                    /*

                    // The script will generate a unique name for the image and return it in the response.
                    var imageName = xhr.responseText;

                    // Now we need to convert the images and install them in the site.
                    xhr2 = new XMLHttpRequest();

                    xhr2.onreadystatechange = function(e) {
                        if (xhr2.readyState == 1) {
                            Output('Resizing, please wait...');
                            $( progressBar ).animate({ width: 20 +'%'}, 200, 'swing', function() {});
                        }else if(xhr.readyState == 4) {
                            if(xhr.status==200){
                                //Output( 'Done', 1 ); // Done
                                progressBar.style.width = "0%";

                                //alert('Done');

                                //AddThumbnail( imageName, 1 );

                                $id("upload-file").disabled = false;

                                xhr2.onreadystatechange = function(e) {}; // prevents firing AddThumbnail multiple times
                            }
                        }
                    };

                    xhr2.upload.addEventListener("progress", function(e) {
                        var pc = parseInt(100 - (e.loaded / e.total * 100));

                        progressBar.style.width = 100 - pc + '%';
                        $( progressBar ).animate({ width: 100 - pc +'%'}, 200, 'swing', function() {});

                        //Output(100 - pc + '%');
                    }, false);

                    xhr2.open("GET", '/api/resize-photo.php', true);
                    xhr2.setRequestHeader("X-IMAGE-ID", imageName);
                    xhr2.setRequestHeader("X-RESIZE", 0);
                    xhr2.send();
                    */
                }else{
                    Output('Error!');
                    document.getElementById('serverErrorMessages').innerHTML = '<div style="padding:15px;">'+xhr.responseText+'</div>';
                    photoUploaderWizard.show( 'error' );
                }
            }
        };

        // start upload
        xhr.open("POST", '/api/upload-photo.php', true);
        xhr.setRequestHeader("X-FILENAME", filesToUpload[0].name);
        xhr.send( filesToUpload[0] );        
    }
    else
    {
        progressBar.style.width = "0%";
        Output('Done!');

        showSuccess();
        //location.href = esco.escoProfileURL+'/photos';
    }
    
    
    


   //HideError();

    
    //$id("upload-file").disabled = true;


    
}


function ResizeFile(imageName, sizeCode, callback)
{
    var progressBar = $id("progress-bar-div");
    
    var xhr = new XMLHttpRequest();
    
    
    xhr.onreadystatechange = function(e) {
        if (xhr.readyState == 1) {
            Output('Resizing "'+imageName+'", please wait...');
        }else if(xhr.readyState == 4) {
            if(xhr.status==200){
                //Output( 'Done', 1 ); // Done
                //progressBar.style.width = "0%";

                
                callback();
                
                $id("upload-file").disabled = false;

                xhr.onreadystatechange = function(e) {}; // prevents firing AddThumbnail multiple times
            }
        }
    };    
    
    xhr.open("GET", '/api/resize-photo.php', true);
    xhr.setRequestHeader("X-IMAGE-ID", imageName);
    xhr.setRequestHeader("X-SIZE", sizeCode);
    xhr.setRequestHeader("X-RESIZE", 0);
    xhr.send();    
    
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