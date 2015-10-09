<?php

/**
 * Menu
 *
 * Creates a menu, by creating Menu instances for 
 * each directory in $dir.
 */
class Menu implements HTMLObject
{
	/// Name of current directory
	public $title;
	
	/// HTML Class of the div : "selected" or empty
	public $class;
		
	/// HTML-formatted relative path to file
	private $webdir;
	
	/// Array of Menu instances, one per directory inside $dir
	private $items=array();

	/// Relative path to file
	private $path = "";
	
	/**
	 * Create Menu
	 *
	 * @param string $dir 
	 * @param int $level
	 */
	public function __construct($dir=null,$level=0){
		
		/// Init Menu 
		if($dir == null)
			$dir = Settings::$photos_dir;
			
		/// Check rights
		if(!Judge::view($dir) || Judge::searchDir($dir) == NULL ){
			return;
		}	

		if(!CurrentUser::$admin && !CurrentUser::$uploader && sizeof($this->list_files($dir,true,false,true)) == 0){
			return;
		}

		/// Set variables
		$this->title = end(explode('/', $dir));
		$this->webdir= urlencode(File::a2r($dir));
		$this->path  = File::a2r($dir);

		try{

			/// Check if selected dir is in $dir
			File::a2r(CurrentUser::$path,$dir);

			$this->selected			=	true;
			$this->class 			=	"level-$level selected";

		}catch(Exception $e){

			/// Selected dir not in $dir, or nothing is selected			
			$this->selected			=	false;
			$this->class 			=	"level-$level";
			
		}

		/// Create Menu for each directory
		$subdirs = $this->list_dirs($dir);

		if(Settings::$reverse_menu){
			$subdirs = array_reverse($subdirs);
		}

		foreach($subdirs as $d){
			$this->items[]	=	new Menu($d,$level+1);
		}
	}
	
	/**
	 * Display Menu in website
	 *
	 * @return void
	 */
	public function toHTML(){
		if(isset($this->webdir) && isset($this->title)){
			echo 	"<ul class='menu_item $this->class'>\n";
			
			if($this->selected){
			$currentSelected = "currentSelected";
				foreach($this->items as $item){
					if($item->selected){
						$currentSelected="";
					}
				}
				echo 	"<li class='menu_title selected $currentSelected'>\n";
			}else{
				echo 	"<li class='menu_title'>\n";
			}

			echo 	"<span class='name hidden'>".htmlentities($this->title, ENT_QUOTES ,'UTF-8')."</span>";
			echo 	"<span class='path hidden'>".htmlentities($this->path, ENT_QUOTES ,'UTF-8')."</span>";
			echo 	"<a href='?f=$this->webdir'>".htmlentities($this->title, ENT_QUOTES ,'UTF-8')."</a>";
			echo 	"</li>\n";

			foreach($this->items as $item)
				$item->toHTML();

			echo 	"</ul>\n";
		}	
	}
	
	/**
	 * List directories in $dir, omit hidden directories
	 *
	 * @param string $dir 
	 * @return void
	 */
	public static function list_dirs($dir,$rec=false, $hidden=false){
		
		/// Directories list
		$list=array();

		/// Check that $dir is a directory, or throw exception
		if(!is_dir($dir)) 
			throw new Exception("'".$dir."' is not a directory");
			
		/// Directory content
		$dir_content = scandir($dir);

        if (empty($dir_content)){
            // Directory is empty or no right to read
            return $list;
        }
		
		/// Check each content
		foreach ($dir_content as $content){
			
			/// Content isn't hidden and is a directory
			if(	($content[0] != '.' || $hidden) && is_dir($path=$dir."/".$content)){
				
				/// Add content to list
				$list[]=$path;

				if($rec){
					$list = array_merge($list,Menu::list_dirs($dir."/".$content,true));
				}

			}
			
		}

		/// Return directories list
		return $list;
	}
	
	/**
	 * List files in $dir, omit hidden files
	 *
	 * @param string $dir 
	 * @return void
	 */
	public static function list_files($dir,$rec = false, $hidden = false, $stopatfirst = false){
		/// Directories list
		$list=array();
		
		/// Check that $dir is a directory, or throw exception
		if(!is_dir($dir)) 
			throw new Exception("'".$dir."' is not a directory");

		/// Directory content
		$dir_content = scandir($dir);

        if (empty($dir_content)){
            // Directory is empty or no right to read
            return $list;
        }
		
		/// Check each content
		foreach ($dir_content as $content){
			
			/// Content isn't hidden and is a file
			if($content[0] != '.' || $hidden){
				if(is_file($path=$dir."/".$content)){
					if(File::Type($path) && (File::Type($path) == "Image" || File::Type($path)=="Video")){
						/// Add content to list
						$list[]=$path;

						/// We found the first one
						if($stopatfirst){
							return $list;
						}
					}
				}else{

					if($rec){
						$list = array_merge($list,Menu::list_files($dir."/".$content,true));
					}

				}
			}
		
		}

		/// Return files list
		return $list;
	}
	
}
?>
