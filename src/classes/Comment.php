<?php



class Comment implements HTMLObject
{
	/// Login of the poster
	public $login;
	
	/// Date when the comment was posted
	public $date;
	
	/// Content of the comment
	public $content;

	/// File
	public $file;

	/**
	 * Create comment
	 *
	 * @param string $login 
	 * @param string $content 
	 * @param string $date 
	 */
	public function __construct($login,$content,$date,$file=null){
		$this->login	=	$login;
		$this->content	=	$content;
		$this->date		=	$date;
		$this->file 	=	$file;
	}
	
	/**
	 * Display comment on website
	 *
	 * @return void
	 */
	public function toHTML($id=0){
		$login		=	stripslashes(htmlentities( $this->login , ENT_QUOTES ,'UTF-8'));
		$content	=	stripslashes(htmlentities( $this->content , ENT_QUOTES ,'UTF-8'));
		$date		=	$this->date;

		echo "<div class='pure-g'>\n";

		echo "<div class='pure-u-1-2 commentauthor'><div>$login</div></div>\n";
		echo "<div class='pure-u-1-2 commentcontent'>$content\n";
		if(CurrentUser::$admin){
			echo "<div class='commentdelete'><form action='?t=Adm&a=CDe' method='post'>
								<input type='hidden' name='image' value='".htmlentities(File::a2r($this->file), ENT_QUOTES ,'UTF-8')."'>
								<input type='hidden' name='id' value='$id'>
								<input type='submit' class='pure-button button-xsmall button-warning' value='x'>
							</form></div>";
		}
		echo "</div>\n";

		echo "</div>\n";
	}
}
?>
