<?php


/**
 * Image
 *
 * The image is displayed in the ImagePanel. This file
 * implements its displaying.
 */

class Image implements HTMLObject
{
	/// URLencoded version of the relative path to file
	static public $fileweb;
	
	/// URLencoded version of the relative path to directory containing file
	private $dir;
	
	/// Width of the image
	private $x;
	
	/// Height of the image
	private $y;

	/// Force big image or not
	private $t;
	
	
	/**
	 * Create image
	 */
	public function __construct($file=NULL,$forcebig = false){
		
		/// Check file type
		if(!isset($file) || !File::Type($file) || File::Type($file) != "Image")
			return;
		
		/// Set relative path (url encoded)
		$this->fileweb	=	urlencode(File::a2r($file));
		
		/// Set relative path to parent dir (url encoded)
		$this->dir	=	urlencode(dirname(File::a2r($file)));
		
		/// Get image dimensions
		list($this->x,$this->y)=getimagesize($file);

		/// Set big image
		if($forcebig){
			$this->t = "Big";
		}else{
			$this->t = "Img";

			if($this->x >= 1200 || $this->y >= 1200){
				if ($this->x > $this->y){
					$this->x = 1200;
				}else{
					$this->x = $this->x * 1200 / $this->y;
				}
			}
		}
	}
	
	
	/**
	 * Display the image on the website
	 *
	 * @return void
	 */
	public function toHTML(){
		echo 	"<div id='image_big' ";
		echo 	"style='";
		echo 		" background: black url(\"?t=".$this->t."&f=$this->fileweb\") no-repeat center center;";
		echo 		" background-size: contain;";
		echo 		" -moz-background-size: contain;";
		echo 		" height:100%;";
		echo 	"';>";

		echo "<input type='hidden' id='imageurl' value='?t=Big&f=$this->fileweb'>";
		echo 	"<a href='?f=$this->dir'>"; 
		echo 	"<img src='inc/img.png' height='100%' width='100%' style='opacity:0;'>";
		echo 	"</a>";
		echo	"</div>";
	}
}

?>
