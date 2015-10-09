<?php


/**
 * RSS
 *
 * Implements functions to work with a Guest Token (or key)
 * Read the account from the Guest Token XML file,
 * edit it, and save it.
 */
class RSS
{

	private $feed = "";

    public function __construct($feed){
    	$this->feed = $feed;
    }


	public function item($n,$p,$d){
		return "<item><title>$n</title><link>$p</link><description><![CDATA[ $d ]]></description></item>\n"; 
	}

	public function add($name,$path,$desc){
		$item = $this->item($name,$path,$desc);
		$current = file_get_contents($this->feed);
		file_put_contents($this->feed,$item.$current, LOCK_EX);
	}

	public function clean(){
		// read the file in an array.
		$file = file($this->feed);

		// slice first 20 elements.
		$file = array_slice($file,0,20);

		// write back to file after joining.
		file_put_contents($this->feed,implode("",$file));
	}

	public function toXML(){
		$this->clean();
		if(Settings::$rss);
		header("Content-type: text/xml"); 
		echo "<?xml version='1.0' encoding='UTF-8'?> 
<rss version='2.0'>
<channel>
<title>".Settings::$name."</title>
<link>".Settings::$site_address."</link>
<description>Photos Feed</description>
<language>en-us</language>"; 
		readfile($this->feed);
		echo "</channel></rss>";
	}
}