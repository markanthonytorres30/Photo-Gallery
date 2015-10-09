<?php

class JS extends Page
{
	private $toPrint;

	private $j;

	public function __construct(){

		/// Execute stuff automagically
		new Admin();

		if(isset($_GET['j'])){
			switch($_GET['j']){

				case "Pag":		$m = new Menu();
								$p = new Board();
								$ap = new AdminPanel();

								echo "<div id='menu' class='menu'>\n";
								
								$m->toHTML();

								echo "</div>\n";
								echo "<div class='panel'>\n";
								$p->toHTML();
								echo "</div>\n";

								echo "<div class='image_panel hidden'>\n";
								echo "</div>\n";

								if(CurrentUser::$admin){
									echo "<div class='infos'>\n";
									$ap->toHTML();
									echo "</div>\n";
								}
								break;

				case "Log":		$p = new LoginPage();
								$p->toHTML();
								break;
				
				case "Reg":		$p = new RegisterPage();
								$p->toHTML();
								break;

				case "Pan":		if(is_file(CurrentUser::$path)){
									$b = new ImagePanel(CurrentUser::$path);
									$b->toHTML();
								}else{
									$b = new Board(CurrentUser::$path);
									$b->toHTML();
								}
								break;

				case "Men":		$m = new Menu();
								$m->toHTML();
								break;


				case "Pan":		$f = new AdminPanel();
								$f->toHTML();
								break;

				case "Inf":		$f = new Infos();
								$f->toHTML();
								break;

				case "Jud":		$j = new Judge(CurrentUser::$path);
								$j->toHTML();
								break;
				
				case "Acc": 	$f = new Group();
								$f->toHTML();
								break;
				
				case "Comm":	$f = new Comments(CurrentUser::$path);
								$f->toHTML();
								break;

				default:		break;
			}
		}
	}

	public function toHTML(){
		
	}
}


?>