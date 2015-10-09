<?php


/**
 * ImagePanel
 *
 * The ImagePanel contains one image, and the infos
 * about that image (such as EXIF, Comments).
 * If the user is logged, it contains even more stuff.
 */

class ImagePanel implements HTMLObject
{

    /// Header of the html page
    public $page_header;
	
	/// Image object
	private $image;
	
	/// Video object
	private $video;
	
	/// Exif object
	private $exif;
	
	/// Comments object
	private $comments;
	
	/// Judge object
	private $judge;

	/**
	 * Create ImagePanel
	 *
	 * @param string $file 
	 */
	public function __construct($file=NULL){
		
		if(!isset($file)){
			return;
		}

        $file_type = File::Type($file);

        if($file_type == "Image"){
            /// Create Image object
            $this->image	=	new Image($file);
        }
        elseif($file_type == "Video"){
            /// Create Video object
            $this->video	=	new Video($file);		
        }		
		
		/// Create Image object
		$this->imagebar	=	new ImageBar($file);

		/// Create EXIF object
		$this->exif		=	new Exif($file);
		
		if(!Settings::$nocomments){
			/// Create Comments object
			$this->comments	=	new Comments($file);
		}

        $pageURL = Settings::$site_address."/?f=".urlencode(File::a2r($file));
        
        // generate the header - opengraph metatags for facebook
        $this->page_header = "<meta property=\"og:url\" content=\"".$pageURL."\"/>\n"
            ."<meta property=\"og:site_name\" content=\"".Settings::$name."\"/>\n"
            ."<meta property=\"og:type\" content=\"website\"/>\n"
            ."<meta property=\"og:title\" content=\"".Settings::$name.": ".File::a2r($file)."\"/>\n"
            ."<meta property=\"og:image\" content=\"".Settings::$site_address."/?t=Thb&f=".urlencode(File::a2r($file))."\"/>\n";
        if (Settings::$fbappid){
            $this->page_header .= "<meta property=\"fb:app_id\" content=\"".Settings::$fbappid."\"/>\n";
        }

		/// Set the Judge
		$this->judge 	=	new Judge($file);
	}

	/**
	 * Display ImagePanel on website
	 *
	 * @return void
	 */
	public function toHTML(){
        if (!isset($this->image) && !isset($this->video)){
            return;
        }
        /*
		echo "<div id='exif' class='box'>\n";
		$this->exif->toHTML();
         */
		
		if(isset($this->image)){
			echo "<div id='bigimage'>\n";

			$this->image->toHTML();
			echo "</div>\n";
		}
        elseif(isset($this->video)){

			echo "<div id='bigvideo'>\n";
			$this->video->toHTML();
			echo "</div>\n";
		}		

		echo "<div id='image_bar'>\n";
		$this->imagebar->toHTML();
		echo "</div>\n";
	}
	
}
?>
