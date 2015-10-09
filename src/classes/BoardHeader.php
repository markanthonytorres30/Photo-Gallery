<?php
class BoardHeader{

	/// Name of the directory listed in parent Board
	public $title;
	
	/// Path of the directory listed in parent Board
	public $path;

	/// Current Working Directory
	private $w;

	
	public function __construct(){
		$this->path 	=	urlencode(File::a2r(CurrentUser::$path));
		$this->title 	=	is_dir(CurrentUser::$path)?end(explode('/', CurrentUser::$path)):end(explode('/', dirname(CurrentUser::$path)));
		$this->w 		= 	File::a2r(CurrentUser::$path);
	}
	
	/**
	 * Display BoardHeader on Website
	 *
	 * @return void
	 */
	public function toHTML(){
		echo 	"<div class='header'>";

		// Menu left
		echo "<a href='#menu' id='menuLink' class='menu-link'><span></span></a>";


		echo 	"<h1>".htmlentities($this->title, ENT_QUOTES ,'UTF-8')."</h1>";

		echo 	"</div>\n";
	}
}

?>
