<?php

/**
 * Exif
 *
 * Reads the exif of an image and outputs it in a
 * nice, readable, html format.
 *
 */

class Exif implements HTMLObject
{
	/// Conversion array for exif values
	private $wanted=array();
	
	/// Exif values, nice and clean
	private $exif=array();
	
	/// Name of the file
	private $filename;

	/**
	 * Create Exif class
	 *
	 * @param string $file 
	 */
	public function __construct($file=null){
		/// No file given
		if(!isset($file)) return;
		
		/// File isn't an image
		if(is_array($file) || !File::Type($file) || File::Type($file) != "Image"){
			return;
		}
		
		/// No right to view
		if(!Judge::view($file))
			return;


		/// No exif extension installed
		if (!in_array("exif", get_loaded_extensions())) {
			$infos['']="Exif extension is not installed on the server";
			return;
		}

		/// Create wanted table
		$this->init_wanted();
		
		/// Read exif
		$raw_exif	=	@exif_read_data($file);

		/// Parse exif
		foreach($this->wanted as $name => $data){
			foreach($data as $d){
				if(isset($raw_exif[$d])){
					$this->exif[$name]	=	$this->parse_exif($d,$raw_exif);
				}
			}
		}	
		$this->filename = basename($file);
	}
	
	/**
	 * Create wanted array
	 *
	 * @return void
	 */
	private function init_wanted(){
		$this->wanted['Name'][]			=	'FileName';		
		$this->wanted['Model'][]		=	'Model';
		$this->wanted['Make'][]			=	'Make';
		$this->wanted['Expo'][]			=	'ExposureTime';
		$this->wanted['Focal Length'][]	=	'FocalLength';
		$this->wanted['Aperture'][]		=	'FNumber';
		$this->wanted['ISO'][]			=	'ISOSpeedRatings';
		$this->wanted['Original Date'][]=	'DateTimeOriginal';
	}
	
	/**
	 * Display Exif on website
	 *
	 * @return void
	 */
	public function toHTML(){
		if($this->exif){
			echo '<h3>Exif</h3>';
			echo "<table>";		
			foreach($this->exif as $name=>$value){
				echo "<tr><td class='td_data'>".htmlentities($name, ENT_QUOTES ,'UTF-8')."</td>";
				echo "<td class='td_value'>".htmlentities($value, ENT_QUOTES ,'UTF-8')."</td></tr>\n";
			}
			echo "</table>\n";
			echo "</div>";
		}
	}
	
	/**
	 * Parse a string referencing a fraction,
	 * and returns the value of the function
	 *
	 * @param string $f 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	function frac2float($f){
		$frac	=	explode('/',$f);
		$float	=	$frac[0]/$frac[1];
		return $float;
	}
	
	/**
	 * Create a beautiful fraction
	 *
	 * @param string $f 
	 * @return void
	 * @author Thibaud Rohmer
	 */
	function nicefrac($a,$b){
		return "1/".number_format($b/$a,"1");
	}

	/**
	 * Parse exif data
	 *
	 * @param string $d 
	 * @param string $raw_exif 
	 * @return void
	 */
	private function parse_exif($d,$raw_exif){
		
		/// Values that don't need to be processed
        $untouched=array('FileName','Model','Make','ISOSpeedRatings',
            'DateTimeOriginal', 'DateTimeDigitized', 'DateTime');
		
		/// If value doesn't need to be processed, return it
		if(in_array($d,$untouched)) 
			return $raw_exif[$d];
		
		/// Return value
		$v=0;
		
		switch ($d){
			case 'ExposureTime': 	$v	=	$raw_exif[$d]." s";
									break;
			case 'FocalLength':		$v		=	$this->frac2float($raw_exif[$d])." mm";
									break;
			case 'FNumber':	        if(($a = number_format($this->frac2float($raw_exif[$d]),"1")) > 0){
										$v = "f".$a;
									}else{
										$v='Unknown';
									}
									break;
		}
		return $v;
	}
}
?>
