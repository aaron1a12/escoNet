<?php

session_start();

define( 'SITE_BASE' , dirname(dirname(dirname(__FILE__))) );

class page {
    
    
    /* Public MySQL Connection */
    public $link;
    
    public $siteDirectory;
    
    /* User Sessions */
    public $loggedIn;
    
    public $escoID;
    public $escoName;
    public $escoLastName;
    public $escoProfileURL;
    
    //private $sessionExpired;


    /* Custom Custructor For CustomPage */
    public function init() {
    }
    
    /* Constructor */
    final public function __construct() {

        $this->siteDirectory = dirname(SITE_BASE);

        
        //die($_COOKIE['esco_user']);
        
        // Load Settings
        require_once( SITE_BASE . '/php/sys/settings.php');
        require_once( SITE_BASE . '/php/sys/functions.php');
        require_once( SITE_BASE . '/php/sys/paginator.php');
        require_once( SITE_BASE . '/php/sys/encryption.php');
        
        // Connect the database
        $this->link = mysqli_connect($_ENV['db_server'], $_ENV['db_user'], $_ENV['db_pass']);
        
        mysqli_set_charset($this->link, "utf8");
        
        if(!$this->link) die('Failed to connect to the database.');
        
        if(!mysqli_select_db( $this->link, $_ENV['db_name'] )) die('Database does not exist.');
        
        // Default Session Settings
        $this->loggedIn = false;

        
        if( isset($_COOKIE['esco_user'])&&isset($_COOKIE['esco_pass']) )
        {
            $esco_user = filter_var( $_COOKIE['esco_user'], FILTER_SANITIZE_MAGIC_QUOTES);
            
            $keyCheckquery = 'SELECT * FROM esco_users WHERE username=\'' . $esco_user . '\' AND password=\'' . filter_var( decrypt($_COOKIE['esco_pass']), FILTER_SANITIZE_STRING) . '\'';
            $keyCheckresult = mysqli_query( $this->link, $keyCheckquery );
            $numRows = mysqli_num_rows( $keyCheckresult );
            
            $userData = mysqli_fetch_row($keyCheckresult);
            
            if($numRows!=0)
            {
                $this->loggedIn = true;
               //$this->escoName = strip_tags(filter_var( $_COOKIE['esco_name'], FILTER_SANITIZE_MAGIC_QUOTES));
                $this->escoName = $userData[3];
                $this->escoLastName = $userData[4];
                $this->escoID = intval($userData[0]);
                $this->escoProfileURL = '/user/' . $userData[0] . '/' . urlify( $userData[3].'_'.$userData[4] );
                
                //die($this->escoProfileURL);
            }

        }
        
        if(!($this->loggedIn==false && $this->private==true)){
            // Execute the page's constructor
            $this->init();
        }
        
        
        
        
        // Load the theme
        require(SITE_BASE . '/php/theme.php');     
    }
    
    /* Default Empty Content */
    public $title = 'Default Title';
    public $private = false;
    public function head() {
        echo '';
    }
    public function content() {
        echo 'Default Content';
    }
    
    public $pageIsFullscreen = false;

    final public function output($outputType) {   
        if( $this->loggedIn==false && $this->private==true )
        {
			$this->pageIsFullscreen = false;
            require(SITE_BASE . '/php/login.php');
        }
        else
        {
            switch($outputType)
            {
                case 'title':
                echo $this->title;
                break;
                case 'head':
                $this->head();
                break;
                case 'content':
                $this->content();
                break;
            }
        }
    }
    

    
} // END OF CLASS