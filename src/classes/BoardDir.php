<?php


/**
 * BoardDir
 *
 * Implements the displaying of directory on the grid of
 * the Website.
 */
class BoardDir implements HTMLObject
{
	/// URL-encoded relative path to dir
	public $url;
	
	/// Path to dir
	public $path;

	/// Images representing the dir
	public $images;

	/// URL for loading the image
	private $img;
	
	
	public function __construct($dir,$img){
		$this->path 	= 	$dir;
		$this->url		=	urlencode(File::a2r($dir));
		if($img == NULL){
			$this->img='inc/folder.png';
		}else{
					$this->img = "?t=Thb&f=".urlencode(File::a2r($img));

		}
	}

	public function toHTML(){
		echo "<div class=' pure-u-1-3 pure-u-sm-1-3 pure-u-lg-1-4 pure-u-xl-1-8'>";
		echo "<div class='directory'>";
		echo 	"<a href=\"?f=$this->url\">";
		echo 	"<img src=\"$this->img\"/ >";
		echo "<div class='dirname'>";
		(array)$name = explode('/', $this->path);
		echo 	htmlentities(end($name), ENT_QUOTES ,'UTF-8');
		echo "</div>";
		echo 	"</a>\n";
		echo "</div>\n";
		echo "</div>";

	}
}

?>
