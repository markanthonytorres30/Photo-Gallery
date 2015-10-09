<?php



class MainPage extends Page
{
	
	/// True if the image div should be visible
	private $image_div = false;
	
	/// Boardpanel object
	private $panel;

    /// Specific header content
    private $header_content;
	
	/// Boards class;
	private $panel_class;

	/// Menubar object
	private $menubar;
	
	/// Imagepanel object
	private $image_panel;

	/// Image_panel class
	private $image_panel_class;

	/// Imagepanel object
	private $menu;

	/// Infos
	private $infos;

	/**
	 * Creates the page
	 */
	public function __construct(){	
			
		try{
			$settings=new Settings();
		}catch(FileException $e){
			// If Accounts File missing... Register !
			$this->header();
			new RegisterPage();
			exit;
		}
		
		/// Check how to display current file
		if(is_file(CurrentUser::$path)){
			$this->image_panel			=	new ImagePanel(CurrentUser::$path);
			$this->image_panel_class 	=	"image_panel";
			$this->panel				=	new Board(dirname(CurrentUser::$path));
			$this->panel_class			=	"linear_panel";
            $this->header_content       =   $this->image_panel->page_header;
		}else{
            $this->image_div            =   false;
			$this->image_panel			=	new ImagePanel();
			$this->image_panel_class	=	"image_panel hidden";
			$this->panel				=	new Board(CurrentUser::$path);
			$this->panel_class			=	"panel";
            $this->header_content       =   $this->panel->page_header;
		}

		/// Create MenuBar
		$this->menubar 		= 	new MenuBar();

		/// Menu
		$this->menu			=	new Menu();

		$this->infos 		= 	new Infos();
	
	}
	
	/**
	 * Display page on the website
	 *
	 * @return void
	 */
	public function toHTML(){

		$this->header($this->header_content);
		echo "<body>";

		echo "<div id='layout'>\n";		



		// Menu left
		echo "<div id='menu'>\n";
		echo "<span id='rss'><a href='?t=Rss'><i class='fa fa-rss fa-lg'></i></a></span>";
		echo "<span id='logo'>".Settings::$name."</span>\n";
		echo "<div class='pure-menu menu pure-menu-open'>\n";
		$this->menu->toHTML();
		echo "</div></div>\n";

		echo "<a href='#menuright' class='menuright-link'><span></span></a>";
		// Menu right
		echo "<div id='menuright'>";
		$this->menubar->toHTML();
		echo "<div class='infos'>";
		$this->infos->toHTML();
		echo "</div>";
		echo "</div>";


		// Main page
		echo "<div id='page'>\n";

		if(CurrentUser::$admin || CurrentUser::$uploader){
	//		echo "<div class='bin'><img src='inc/bin.png'>".Settings::_("bin","delete")."</div>";
		}

		// ImagePanel
		echo "<div id='image_panel' class='$this->image_panel_class'>\n";
		$this->image_panel->toHTML();
		echo "</div>\n";

		// Panel
		echo "<div class='$this->panel_class'>\n";
		$this->panel->toHTML();
		echo "</div>\n";


		echo "</div>\n";
		echo "</div>\n";

		echo "<script src='inc/ui.js'></script>\n";

		echo "</body>";
	}
}

?>
