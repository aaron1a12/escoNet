<?php

class customPage extends page {
    
    public $user;
    public $img;
    public $profileLink;
    
    function init()
    {
        $this->img = new stdClass;
        
        if(!isset($_GET['usr-id']))
            die();
        
        $userid = intval($_GET['usr-id']);
        
        
        $query = 'SELECT * FROM esco_users WHERE id="'.$userid.'"';
        $result = mysqli_query($this->link, $query);
        $num_rows = mysqli_num_rows($result);
        $this->user = mysqli_fetch_assoc($result);
        
        $this->profile =  mysqli_fetch_assoc(mysqli_query($this->link, "SELECT * FROM esco_user_profiles WHERE user='$userid';"));
        
        $this->user['photoBase'] = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].'_'.$this->user['lastname']).'/photos';

        $this->title = 'Photos by '.$this->user['name'].' '.$this->user['lastname'];    
        
        if(!isset($_GET['img-id']))
            $this->img->id = 0;
        else
            $this->img->id = intval($_GET['img-id']);
        
        $this->profileLink = '/user/'.$this->user['id'].'/'.urlify($this->user['name'].' '.$this->user['lastname']);
    }
    
    function head(){ ?>
<script>
    function deleteImage( imgCode ){
        var ok = confirm('Really delete?');
        
        if(ok)
        {
            $.ajax({
                type: "GET",
                url : "/user/photo-uploader/deleter.php",
                data: { confirm:1, image:imgCode }
            }).done(function(data){
                location.reload();
            }); 
        }
        
    }
</script>
<?php
    }
    
    function content()
    {
?>
<div class="widget nopadding">
    <?php
     include($this->siteDirectory.'/_inc/php/user-photo-header.php');
    ?>
    
    <div class="paddedContent">
        <h2>Favorites</h2>
        <?php {

            
            //show($this->user);


            $FOR_ROW = 'user';
            $FOR_VALUE = $this->user['id'];


            // How many items to show per page
            $RESULTS_PER_PAGE = 144;
            // How pages to show until the ellipsis (...)
            // Must be an odd number (not evenly divisable by 2. E.g., 1, 3, 5, 7, 9, 11, 13, etc)
            $MAX_PAGE_GROUP = 9;            


            $urlQuery = array();
            parse_str($_SERVER['QUERY_STRING'], $urlQuery);

            // Remove the first two ( internal page-loading stuff )
            //array_shift($urlQuery);
            //array_shift($urlQuery);
            if(isset($urlQuery['page'])) unset($urlQuery['page']);
            if(isset($urlQuery['id'])) unset($urlQuery['id']);
            if(isset($urlQuery['usr-id'])) unset($urlQuery['usr-id']);

            if(!isset($urlQuery['p']) || $urlQuery['p']=='' || intval($urlQuery['p'] < 1) )
                $urlQuery['p'] = 1;
            else
                $urlQuery['p'] = intval($urlQuery['p']);


            // Where Clause
            $WHERE = "WHERE $FOR_ROW='$FOR_VALUE'";

            // Let's first see how many rows come up with the already constructed WHERE clause.
            $countQuery   = "SELECT COUNT(id) FROM esco_photo_favs $WHERE";
            $resultsNum = mysqli_fetch_array(mysqli_query($this->link, $countQuery));
            $resultsNum = $resultsNum[0];

            // Divide the results per page
            $totalPages = ceil( $resultsNum / $RESULTS_PER_PAGE );

            // Offsets

            if($urlQuery['p']>$totalPages)
                $urlQuery['p'] = $totalPages;

            $startPageOffset = ($RESULTS_PER_PAGE * $urlQuery['p']) - $RESULTS_PER_PAGE;

            if($startPageOffset<0)
                $startPageOffset = 0;

            $LIMIT = 'LIMIT '.$startPageOffset.','.$RESULTS_PER_PAGE;


            $photoSelect = "SELECT * FROM esco_photo_favs $WHERE ORDER BY `id` DESC $LIMIT";

            $photoResult = mysqli_query($this->link, $photoSelect);


            //
            // While Loop and HTML output
            //


            //
            // HTML Pagination Buttons
            //

            $currentURL = $_SERVER['REDIRECT_URL']; // DOES NOT CONTAIN QUERY STRINGS

            // Page Groups

            $page_group_start = $urlQuery['p'] - floor( $MAX_PAGE_GROUP / 2 );
            $page_group_end = $urlQuery['p'] + floor( $MAX_PAGE_GROUP / 2 );

            //$diff = ;

            if($page_group_start<1){
                $page_group_end += 1-$page_group_start;
                $page_group_start = 1;
            }

            if($page_group_end>$totalPages){
                $page_group_start += $totalPages-$page_group_end;
                $page_group_end = $totalPages;    
            }

            // Fix for when the page number is less $MAX_PAGE_GROUP

            if($page_group_start<1)
                $page_group_start = 1;


            $html_pagination_number_buttons = '';

            $tmpQueryArray = $urlQuery;
            unset($tmpQueryArray['p']);

            if($page_group_start > 1)
            {
                $href = $currentURL . '?p=' . 1;

                // Reconstruct the query
                foreach ($tmpQueryArray as $key => $value)
                    $href .= "&amp;$key=$value";

                $html_pagination_number_buttons .= '<a href="'.$href.'" class="btn">...</a>';
            }

            for($i=$page_group_start; $i<=$page_group_end; $i++)
            {
                $href = $currentURL . '?p='.$i;

                // Reconstruct the query
                foreach ($tmpQueryArray as $key => $value)
                    $href .= "&amp;$key=$value";

                // HTML/CSS stuff

                $cssClass = 'btn';

                if($urlQuery['p']==$i){
                    $cssClass = 'btn selected';
                }

                // Remove the link if there's only one page
                if($totalPages==1) $href = '#';

                $html_pagination_number_buttons .= '<a href="'.$href.'" class="'.$cssClass.'">'.$i.'</a>';
            }


            if($page_group_end < $totalPages)
            {
                $href = $currentURL . '?p=' . $totalPages;

                // Reconstruct the query
                foreach ($tmpQueryArray as $key => $value)
                    $href .= "&amp;$key=$value";

                $html_pagination_number_buttons .= '<a href="'.$href.'" class="btn">...</a>';
            }


            $maxPossible = $totalPages;
            $minPossible = 1;

            $next = ($urlQuery['p']+1);
            $prev = ($urlQuery['p']-1);

            $prev_href = $currentURL . '?p='.$prev;
            $next_href = $currentURL . '?p='.$next;


            // Reconstruct the queries
            foreach ($tmpQueryArray as $key => $value)
                $prev_href .= "&amp;$key=$value";
            foreach ($tmpQueryArray as $key => $value)
                $next_href .= "&amp;$key=$value";

            $html_pagination_prev_button = '<a class="btn" href="'.$prev_href.'">&lt; Prev</a>';
            $html_pagination_next_button = '<a class="btn" href="'.$next_href.'">Next &gt;</a>';

            if($prev < $minPossible) $html_pagination_prev_button = '';
            if($next > $maxPossible) $html_pagination_next_button = '';

            $divider = '<span class="divider"></span>';

            if($resultsNum!=0)
                $pagination_html = '<div class="pagination">'.$html_pagination_prev_button . $divider . $html_pagination_number_buttons . $divider . $html_pagination_next_button.'</div>';
            else
                $pagination_html = '';

            unset($tmpQueryArray);       


            $document = new DOMDocument;    

            $table = $document->createElement('table');               
            $tr = $document->createElement('tr');

            $i = 0;
            while ($fav = mysqli_fetch_row($photoResult)[2]) {
                $i++;
                
                
                
                $photoRow = mysqli_fetch_assoc( mysqli_query($this->link, "SELECT * FROM esco_photos WHERE id='$fav';") );
                
                $photoTitle = $photoRow['title'];
                
                if($photoTitle!=''){ $photoTitle = $photoRow['title']; }else{
                    $photoTitle = $photoRow['name'];
                    $photoTitle = explode('_', $photoTitle);
                    
                    array_pop($photoTitle);
                    array_pop($photoTitle);
                    
                    $photoTitle = implode('_', $photoTitle);
                }
                
				$authorName = mysqli_fetch_row( mysqli_query($this->link, "SELECT name,lastname FROM esco_users WHERE id='".$photoRow['author']."' ") );
		
				
				
                $photoURL = '/user/'.$photoRow['author'].'/'.urlify($authorName[0].'_'.$authorName[1]) . '/photos/'. $photoRow['id'] . '/' . urlify($photoTitle);
                
                
                if( $i==13 ){
                    $i = 1;
                    // Add the row now and create another row
                    $table->appendChild($tr);
                    $tr = $document->createElement('tr');
                }
                 
                $td = $document->createElement('td');
                
                $a = $document->createElement('a');
                $a->setAttribute('href', $photoURL);
                $td->appendChild($a);
                
                $img = $document->createElement('img');
                $a->appendChild($img);
                

            

                
                
                $commentAuthorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$photoRow['author']."' ") );

                $imageName = $photoRow['name'];

                $format = $photoRow['format']; 
                switch($format){
                    case 0:
                        $format = '.jpg';
                        break;
                    case 1:
                        $format = '.png';
                        break;
                    case 2:
                        $format = '.gif';
                        break;
                }

                $imageFile = $imageName . '_s' . $format;

                $parts = explode('_', $imageName);
                //$serverID = $parts[ count($parts)-2 ];

                $photo = 'http://media.esco.net/img/social/photos/'.$photoRow['author'].'/'.$imageFile;
                
                $img->setAttribute('src', $photo);
                $img->setAttribute('alt', $photoTitle);
                $img->setAttribute('style', 'width:50px; height:50px;');
                
                // Add the cell
                $tr->appendChild( $td );
            }

            $table->appendChild($tr);
            $document->appendChild( $table );
            
            
            $table->setAttribute('class', 'image-grid-all');
            
            echo $document->saveHTML(); 

            echo '<br>';
            
            echo '<div style="width:645px">'.$pagination_html.'</div>';            

                //while ($commentRow = mysqli_fetch_assoc($searchResult)) {
                //}
        } ?>
    </div>
</div>
<?php
    }
}

new customPage();