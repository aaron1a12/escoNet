<?php

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
    $WHERE = "WHERE $COMMENTS_FOR_ROW='$COMMENTS_FOR_VALUE'";
	
	// Comments Plugin Api (uses binary ids)
	if(isset($COMMENTS_FOR_BINARY))
		$WHERE = "WHERE $COMMENTS_FOR_ROW=$COMMENTS_FOR_VALUE";
	

    // Let's first see how many rows come up with the already constructed WHERE clause.
    $countQuery   = "SELECT COUNT(id) FROM $COMMENTS_TABLE $WHERE";
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


    $commentSelect = "SELECT * FROM $COMMENTS_TABLE $WHERE ORDER BY `id` DESC $LIMIT";


    $commentResult = mysqli_query($this->link, $commentSelect);


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




    echo '<div class="comments" style="background-color:#f2f2f7; margin-bottom:20px;">';
    while ($commentRow = mysqli_fetch_assoc($commentResult)) {
    ?>
        <?php
		$commentAuthorInfo = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_users WHERE id='".$commentRow['author']."' ") );
		$commentAuthorProfile = mysqli_fetch_row( mysqli_query($this->link, "SELECT * FROM esco_user_profiles WHERE user='".$commentRow['author']."' ") );
		?>
        <!-- Comment ID: <?php echo $commentRow['id'];?> -->
        <div style="padding:20px; border-bottom:2px solid #eee;">
            <img src="http://media.esco.net/img/social/<?php echo $commentRow['author'];?>/profile_small.jpg" style="width:50px; float:left;">
            <div style="float:left; margin-left:20px; width:450px;">
                <b><a href="<?php echo '/user/'.$commentRow['author'].'/'.urlify( $commentAuthorInfo[3].'_'.$commentAuthorInfo[4] );?>"><?php echo $commentAuthorInfo[3].' '.$commentAuthorInfo[4];?></b></a>

                <span style="color:#bbb;">
                |
                <?php if(isset($ENABLE_VIEW_PHOTOS) && $ENABLE_VIEW_PHOTOS!=false){ echo '<a href="'.'/user/'.$commentRow['author'].'/'.urlify( $commentAuthorInfo[3].'_'.$commentAuthorInfo[4] ).'/photos/">View Photos</a> |'; } ?>
                <small><?php echo escoDate($commentRow['time']);?></small>
                <?php
                {
                    if($commentRow['author']==$this->escoID && time()-$commentRow['time'] < 604800){ // a week is the max
                        echo '| <small><a href="/api/remove-comment.php?from='.base64_encode($COMMENTS_TABLE).'&id='.$commentRow['id'].'&return='.base64_encode($_SERVER['REQUEST_URI']).'" style="color:#ffaaaa;">Remove</a></small>';
                    }
                }
                ?>
                </span>

                <br>
                <?php {
                    $zComment = $commentRow['comment'];
                    /*
                    $httpPos = stripos($zComment, 'http://');

                    if($httpPos !== false) {

                        $everythingAfter = substr($zComment, $httpPos);
                        $everythingAfter = explode(' ', $everythingAfter);

                        $httpLink = $everythingAfter[0];

                        $zComment = str_replace($httpLink, "<a href=\"$httpLink\">$httpLink</a>", $zComment);
                    }
                    */
                    $zComment = addLinks( nl2br(trim($zComment)), true );

                    echo $zComment;
                }?>
            </div>
            <div class="cf"></div>
        </div>
    <?php
    }
    echo '</div>';

    echo $pagination_html;
