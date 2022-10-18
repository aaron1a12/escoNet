<?php
error_reporting(0);
class customPage extends page {
    public $private = false;

    function init(){
        if($_POST)
        {
			$headlines = array();
			
			$feed = new DOMDocument;
			$feed->loadHTML( $_POST['xml'] );		

			$items = $feed->getElementsByTagName('item');
			
			foreach ($items as $item) {		
				$headline = $item->getElementsByTagName('title')->item(0)->nodeValue;
				array_push($headlines, $headline);
			}
			
			//var_dump($headlines);
			
			$newsCount = mysqli_fetch_array(mysqli_query($this->link, "SELECT COUNT(id) FROM esco_news"))[0];
			$maxCount = 15;
			
			// Watch for duplicates
			
			$select = "SELECT * FROM esco_news ORDER BY `id` DESC";
            $selectResult = mysqli_query($this->link, $select);
        
            while ($row = mysqli_fetch_row($selectResult)) {
				foreach ($headlines as $key=>$headline) {		
					if($row[1]==$headline) {
						unset($headlines[$key]);
					}
				}
            }
			
			// Delete old headlines if we reach the limit
			
			$feedCount = count($headlines);
			$numberToDelete = ($newsCount+$feedCount) - $maxCount;
		
			if($numberToDelete > 0) {
				$delete = "DELETE FROM `esco_news` ORDER BY `id` LIMIT $numberToDelete;";
				$result = mysqli_query($this->link, $delete);
			}
			
			// Add the new ones
			
			foreach ($headlines as $headline) {		
                $query = "INSERT INTO esco_news (headline) VALUES ('$headline')";
                mysqli_query($this->link, $query);
			}

			exit();
        }
        else
        {
            exit();
        }
       
    }
}

new customPage();