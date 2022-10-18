<?php
/**
 *
 * Paginator Class for displaying pages with a query
 *
 */

class paginator {
    
    public $paginationHtml;
    public $count;
    public $custom;
    
    /* Constructor */
    final public function __construct(
        $session, $query, $RESULTS_PER_PAGE=15, $MAX_PAGE_GROUP=9, $callback, $bAutoShowPages=true
    ) 
    {
        
        //
        // $RESULTS_PER_PAGE
        // How many items to show per page
        //
        // $MAX_PAGE_GROUP
        // How pages to show until the ellipsis (...)
        // Must be an odd number (not evenly divisable by 2. E.g., 1, 3, 5, 7, 9, 11, 13, etc)
        //
        
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

        
        $result = mysqli_query($session->link, $query);
        
        // Total results without pages
        $resultsNum = mysqli_fetch_row(mysqli_query($session->link, "SELECT FOUND_ROWS();"))[0];
        
        $this->count = $resultsNum;
        
        // Divide the results per page
        $totalPages = ceil( $resultsNum / $RESULTS_PER_PAGE );
        
        // Offsets

        if($urlQuery['p']>$totalPages)
            $urlQuery['p'] = $totalPages;

        $startPageOffset = ($RESULTS_PER_PAGE * $urlQuery['p']) - $RESULTS_PER_PAGE;

        if($startPageOffset<0)
            $startPageOffset = 0;

        $LIMIT = 'LIMIT '.$startPageOffset.','.$RESULTS_PER_PAGE;
        
        
        $FINAL_QUERY  = $query. ' ' .$LIMIT;
        
        $result = mysqli_query($session->link, $FINAL_QUERY);
        
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
            $this->paginationHtml = '<div class="pagination">'.$html_pagination_prev_button . $divider . $html_pagination_number_buttons . $divider . $html_pagination_next_button.'</div>';
        else
            $this->paginationHtml = '';

        unset($tmpQueryArray);               
        
        
        
        while ($row = mysqli_fetch_row($result)) {
            $callback($row);
        }
        
        if($bAutoShowPages){
            echo '<br>';
            echo $this->paginationHtml;
        }
    }    
    
    public function showPages() {
        print( $this->paginationHtml );
    }
    
    public function getCount() {
        return intval($this->count);
    }
    
} // END OF CLASS