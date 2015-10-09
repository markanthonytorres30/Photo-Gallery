<?php



class Index
{
	function __construct(){
		/// Initialize variables
		Settings::init();


		/// Initialize CurrentUser
		try{
			CurrentUser::init();
		}catch(Exception $e){
			$page = new RegisterPage(true);
			$page->toHTML();
			return;
		}

		/// Check what to do
		switch (CurrentUser::$action){

			case "Rss":		$r = new RSS(Settings::$conf_dir."/photos_feed.txt");
							$r->toXML();
							break;

			case "Judge":	// Same as page
			case "Page":	$page = new MainPage();
							$page->toHTML();
							break;
			
			case "Logout":
			case "Login":			
			case "Log":		$page = new LoginPage();
							$page->toHTML();
							break;
							
			case "Reg":		$page = new RegisterPage();
							$page->toHTML();
							break;

			case "JS":		$page = new JS();
							break;

			case "Img":		Provider::Image(CurrentUser::$path);
							break;
			
			case "BDl":		Provider::Image(CurrentUser::$path,false,true,true,true);
							break;

			case "Big":		Provider::Image(CurrentUser::$path,false,true);
							break;

			case "Thb":		Provider::Image(CurrentUser::$path,true);
							break;

			case "Vid":		Provider::Video(CurrentUser::$path);
							break;
						
			case "Zip":		Provider::Zip(CurrentUser::$path);
							break;
			
			case "Acc":		if(CurrentUser::$admin && isset($_POST['login'])){
								$acc = new Account($_POST['login']);
							}else{
								$acc = CurrentUser::$account;
							}
							$acc->toHTML();
							break;
			
			case "Adm":		$page = new Admin();
							$page->toHTML();
							break;
		}
	}
}

?>
