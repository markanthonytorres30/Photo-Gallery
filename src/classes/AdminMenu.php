<?php



 class AdminMenu
 {
 	/// Menu options
 	public $options=array();

 	/**
 	 * Build AdminMenu
 	 */
 	public function __construct(){
 		$this->options['Abo']	= Settings::_("adminmenu","about");
 		$this->options['Sta']	= Settings::_("adminmenu","stats");
 		$this->options['VTk']	= Settings::_("adminmenu","tokens");
 	 	$this->options['Set']	= Settings::_("adminmenu","settings");
 	 	$this->options['Acc']	= Settings::_("adminmenu","account");
 	 	$this->options['EdA']	= Settings::_("adminmenu","groups");
 	}
 
 	/**
 	 * Display AdminMenu on website
 	 */
 	public function toHTML(){
		echo 	"<ul class='menu_item $this->class'>\n";
		
		foreach($this->options as $op=>$val){
			if( isset($_GET['a']) && $_GET['a'] == $op){
				$class = "menu_item currentSelected";
			}else{
				$class = "menu_item";
			}
 			echo "<li class='menu_title $class'>\n";
			echo "<a href='?t=Adm&a=$op'>$val</a>";
			echo "\n</li>\n";
 		}
		echo "<li class='menu_item'>\n";
		echo "<a href='.'>".Settings::_("adminmenu","back")."</a>";
		echo "</li>\n";
		echo "</ul>\n";
 	}

 }
 ?>
