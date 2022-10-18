<?php

class customPage extends page {
    public $title = 'Edit Categories';	
    public $private = true;
    
    public $category;
    public $newCategory;
    
    public $errors = array();
    

    function init()
    {
        $this->newCategory = new stdClass;
        $this->newCategory->name = '';
        
        $this->category = new stdClass;
        $this->category->id = 0;
        $this->category->name = '';
        
        if(isset($_GET['new']))
            $this->category->id = intval($_GET['new']);
        
        if(isset($_GET['delete'])){
            $this->category->id = intval($_GET['delete']);
            
            //
            // Check for child categories and check if there are any pages in them
            //
            
            
            
            $query = <<<MySQL
SELECT * FROM  esco_blog_posts
LEFT JOIN (SELECT DISTINCT `post` FROM esco_blog_post_cat_assoc WHERE `category`=3 or `category`=2 ORDER BY `post` DESC) AS T1
ON esco_blog_posts.id = T1.post WHERE T1.post is not null
MySQL;

            $cats = array($this->category->id);

            getAllCategoryChildren( $this->link, $this->category->id, $cats );
            
            $bContinueSearching = true;

            foreach($cats as $cat){
                if($bContinueSearching){
                    $count = intval(mysqli_fetch_row( mysqli_query($this->link, "SELECT COUNT(id) FROM esco_blog_posts WHERE category=$cat;") )[0]);
                    if($count>0){
                        $bContinueSearching = false;
                    }
                }
            }
            
            if($bContinueSearching){

                if($this->category->id!=1){
                    // Delete all categories in the tree (parent and subs)
                    foreach($cats as $cat){
                        mysqli_query($this->link, "DELETE FROM esco_blog_categories WHERE `id`=$cat;");
                    }
                    header('Location: /user/blog/categories.php');
                    die();
                }
                
                echo 'Cannot delete default category.';
                die();
            }

            
        }
        
        if($this->category->id!=0){       
            $result = mysqli_query($this->link, "SELECT * FROM esco_blog_categories WHERE id='".$this->category->id."';");
            if(mysqli_num_rows($result)==0) die('Category non-existent');
            
            $row = mysqli_fetch_row($result);
            $this->category->name = $row[2];
        }
        else
        {
            $this->category->name = 'root';
        }
        
        if($_POST){
            if(isset($_POST['name']) && $_POST['name']!=''){
                $this->newCategory->name = filter_var( htmlentities($_POST['name']), FILTER_SANITIZE_MAGIC_QUOTES );
                
                $name = $this->newCategory->name;
                $id = $this->category->id;
                
                $query = "SELECT COUNT(id) FROM esco_blog_categories WHERE `name`='$name';";
                $count = mysqli_fetch_array(mysqli_query($this->link, $query))[0];
                
                if($count>0) array_push($this->errors, 'Category name already exists. ');
                
                
                if(count($this->errors)==0){
                    $query = "INSERT INTO esco_blog_categories (parent, name) VALUES($id,'$name')";
                    header('Location: /user/blog/categories.php');
                    
                    mysqli_query($this->link, $query);
                    
                    die();
                }
            }
        }
        //filter_var( htmlentities($title, FILTER_SANITIZE_MAGIC_QUOTES ));
    }
    
    function head(){
?>
<script>
</script>
<?php
    }

    function content() {
?>
<?php include($this->siteDirectory . '/_inc/php/user-header.php');?>


<?php if(isset($_GET['new'])) { ?>
<div class="widget">
    <?php if($this->category->id==0) {?>
    <h1>New Category</h1>
    <?php }else{ ?>
    <h1>New Category Under "<?php echo $this->category->name;?>"</h1>
    <?php } ?>
    <form action="" method="POST">
        <table class="table">
            <tr>
                <td>Title</td>
                <td style="width:500px;"><input style="width:100%;" type="text" name="name" value="<?php echo $this->newCategory->name;?>" maxlength="100"></td>
            </tr>
        </table>


        <br><br><br><br>


        <button type="submit">Save</button>
        <button type="button" onclick="location.href='/user/blog/categories.php';">Cancel</button>
    </form>   
    <?php
        if(count($this->errors)>0){
            echo '<div class="error" style="margin-top:20px;"><ul>';
            foreach($this->errors as &$error){
                echo '<li>'.$error.'</li>';
            }
            echo '</ul></div>';
        }
    ?>
</div>    
<?php }elseif(isset($_GET['delete'])) { ?>
<div class="widget">
    <h1>Cannot delete Category</h1>
    The selected category has pages in it. Try removing the pages first.
</div>    
<?php }else{ ?>

<div class="widget" style="position:relative;">
    <h1>Categories</h1>
    <button onclick="location.href='/user/blog/categories.php?new=0';">Add top-level category</button>
    
    <br>
    <br>
    
    <?php
	{
		$select = "SELECT * FROM esco_blog_categories ORDER BY `id` ASC";
		$result = mysqli_query($this->link, $select);
        
        $d = new DOMDocument;
        $d->loadHTML('<div id="categoriesDiv"></div>', LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        
        $categoriesDiv = $d->getElementById( 'categoriesDiv' );
        
        while ($row = mysqli_fetch_row($result)) {
            $id = $row[0];
            $parentId = $row[1];
            $name = $row[2];
            
            $parent = $d->getElementById( 'cat_'.$parentId );
            
            $div = $d->createElement('div');
            $div->setAttribute('id', 'cat_' . $id );
            $div->setAttribute('class', 'category');
            
            $divBoldName = $d->createElement('b');
            $divBoldName->nodeValue = $name;
            
            $small = $d->createElement('small');
            
            $divLinkNew = $d->createElement('a');
            $divLinkNew->nodeValue = 'New Sub Category';
            $divLinkNew->setAttribute('href', 'categories.php?new='.$id);
            
            $divLinkDelete = $d->createElement('a');
            $divLinkDelete->nodeValue = 'Delete';
            $divLinkDelete->setAttribute('href', 'categories.php?delete='.$id);
            
            
            
            $div->appendChild( $divBoldName );
            $div->appendChild( $d->createTextNode(' ― ') );
            $div->appendChild( $small );
            
            $small->appendChild($divLinkNew);
            $small->appendChild( $d->createTextNode(' | ') );
            $small->appendChild($divLinkDelete);
            
            
            
            if($parent){
                $div->setAttribute('style', 'margin-left:20px;');
                $parent->appendChild($div);
            }
            else{
                $categoriesDiv->appendChild($div);
            }
        }
        
        print( $d->saveHTML() );
	}
?>
</div>
<?php } ?>
<?php
    }
}

new customPage();