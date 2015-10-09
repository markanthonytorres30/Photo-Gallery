<?php


/**
 * BoardItem
 *
 * Implements the displaying of an item of the grid on
 * the Website.
 */
class BoardItem implements HTMLObject
{
	/// URL-encoded relative path to file
	public $file;
	
	/// Path to file
	public $path;

	/// Ratio of the file
	public $ratio;
	
	/// Item width
	public $width;
	
	/**
	 * Construct BoardItem
	 *
	 * @param string $file 
	 * @param string $ratio 
	 */
	public function __construct($file,$ratio=0){
		$this->path 	= 	$file;
		$this->file		=	urlencode(File::a2r($file));
		$this->ratio	=	$ratio;
	}
	
	/**
	 * Display BoardItem on Website
	 *
	 * @return void
	 */
	public function toHTML(){
		
		$getfile =	"t=Thb&f=$this->file";

		echo "<div class='item $lgcls pure-u-1-2 pure-u-sm-1-2 pure-u-md-1-3 pure-u-lg-1-4 pure-u-xl-1-8'>";
		echo 	"<a href='?f=$this->file'>";
		echo 	"<img src='?$getfile'>";
		echo 	"</a>\n";
		echo "</div>\n";

	}



	/**
	 * Calculate width (in percent) of the item : 
	 * 90 * item_ratio / line_ratio
	 *
	 * @param string $r 
	 * @return void
	 */
	public function set_width($line_ratio){
		$this->width = 90 * $this->ratio / $line_ratio;
	}
}

?>