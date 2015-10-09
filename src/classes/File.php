<?php

/**
 * File
 *
 * All functions regarding files.
 */

class File
{
	/// Path to the file
	public $path;
	
	/// File extension
	public $extension;
	
	/// File name
	public $name;
	
	/// File type
	public $type;
	
	/**
	 * Check that file exists, and parse its infos (extension,name,type)
	 *
	 * @param string $path 
	 */
	public function __construct($path){
		
		/// Check that file exists
		if(!file_exists($path))
			throw new Exception("The file doesn't exist !");
		
		/// Set variables
		$this->path			=	$path;
		$this->extension	=	self::Extension($path);
		$this->name			=	self::Name($path);	
		$this->type			=	self::Type($path);
		$this->root			=	self::Root();		
	}
	
	/**
	 * Return the root directory
	 *
	 * @return void
	 */
	public static function Root(){
		return realpath(dirname(__FILE__)."/../../");
	}	
	
	/**
	 * Return the extension of $file
	 *
	 * @param string $file 
	 * @return void
	 */
	public static function Extension($file){
		return pathinfo($file,PATHINFO_EXTENSION);
	}
	
	/**
	 * Return the name of $file, without the extension
	 *
	 * @param string $file 
	 * @return void
	 */
	public static function Name($file){
		$info	=	pathinfo($file);
		if(isset($info['extension'])){
			return	basename($file,'.'.$info['extension']);
		}else{
			return basename($file);
		}
	}
	
	/**
	 * Return the type of $file
	 *
	 * @param string $file 
	 * @return void
	 */
	public static function Type($file){
		$file=strtolower($file);
		if(self::Name($file) == "."){
			return "folder";
		}

		$ext	=	self::Extension($file);
		if(!isset($ext)){
			return "folder";
		}

		$types	=	array();
		
		$types['Image'][]	=	"png";
		$types['Image'][]	=	"jpg";
		$types['Image'][]	=	"jpeg";
		$types['Image'][]	=	"tiff";
		$types['Image'][]	=	"gif";
		
		$types['Video'][]	=	"flv";
		$types['Video'][]	=	"mov";
		$types['Video'][]	=	"mpg";
		$types['Video'][]	=	"mp4";		
		$types['Video'][]	=	"ogv";		
		$types['Video'][]	=	"mts";		
		$types['Video'][]	=	"3gp";		
		$types['Video'][]	=	"webm";			
		
		
		$types['File'][]	=	"xml";
		
		/// Find file type
		foreach($types as $type=>$typetab){
			if(in_array($ext,$typetab)){
				return $type;
			}
		}
		return 0;

	}
	
	/**
	 * Absolute path comes in, relative path goes out !
	 *
	 * @param string $file 
	 * @param string $dir Directory from where the relative path will be (if NULL : photos_dir)
	 * @return void
	 */
	public static function a2r($file,$dir=NULL){
		if(!isset($dir)){
			$dir		=	Settings::$photos_dir;
		}
		
		$rf	=	realpath($file);
		$rd =	realpath($dir);
		
		if($rf==$rd) return "";

		if( substr($rf,0,strlen($rd)) != $rd ){
			throw new Exception("This file is not inside the photos folder !<br/>");
		}

		return ( substr($rf,strlen($rd) + 1 ) );
	}

	/**
	 * Relative path comes in, absolute path goes out !
	 *
	 * @param string $file 
	 * @param string $dir 
	 * @return void
	 */
	public static function r2a($file,$dir=NULL){
		if(!isset($dir)){
			$dir		=	Settings::$photos_dir;
		}
		
		return $dir."/".$file;
	}
	
	/**
	 * Path comes in, relative and absolute path come out
	 *
	 * @param string $path 
	 * @return void
	 */
	public static function paths($path,$dir=NULL){
		if(!isset($dir)){
			$dir		=	Settings::$photos_dir;
		}
		try{
			$rel		=	File::a2r($path,$dir);
			$abs		=	$path;
		}catch(Exception $e){

		// This path is already relative
			$rel		=	$path;
			$abs		=	File::r2a($path,$dir);
		}
		
		return array($rel,$abs);
	}

	/**
	 * Returns absolute path to next item
	 * 
	 * @param string $path
	 * @return $next
	 */
	 public static function next($path){
	 	$files 	= 	Menu::list_files(dirname($path));
	 	$pos 	=	array_search($path,$files);

	 	if( isset($pos) && $pos < sizeof($files) - 1 ){
	 		/// Found $path
 			return $files[$pos+1];
	 	}
	 	return $path;
	 }



	/**
	 * Returns absolute path to previous item
	 * 
	 * @param string $path
	 * @return $prev
	 */
	 public static function prev($path){
	 	$files 	=	Menu::list_files(dirname($path));
	 	$pos 	=	array_search($path,$files);

	 	if( isset($pos) && $pos > 0 ){
	 		/// Found $path
 			return $files[$pos-1];
	 	}
	 	return $path;
	 }

}
?>