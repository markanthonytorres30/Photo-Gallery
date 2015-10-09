<?php

class LoginPage extends Page
{
	
	/**
	 * Create Login Page
	 */
	public function __construct(){
			
	}
	
	/**
	 * Display Login Page on website
	 *
	 * @return void
	 */
	public function toHTML(){

        if (Settings::$forcehttps && !$_SERVER["HTTPS"]){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
            exit();
        }else{
            $this->header();
            echo "<div class='header'>";
            echo "<h1>".Settings::_("login","logintitle")."</h1>";
                        echo "</div>";

            echo "<div class='center'>\n";
            echo "<form method='post' action='?t=Login' class='pure-form pure-form-aligned niceform'>\n";
            echo "<fieldset>
                 <div class='pure-control-group'>
                 <label>".Settings::_("login","login")."</label>
                    <input type='text' name='login' value='' placeholder='".Settings::_("login","login")."'>
                </div>
                 <div class='pure-control-group'>
                 <label>".Settings::_("login","pass")."</label>
                    <input type='password' name='password' value='' placeholder='".Settings::_("login","pass")."'>
                    </div>
                 <div class='pure-control-group'>
                    <input type='submit' class='pure-button pure-button-primary' value='".Settings::_("login","submit")."'>
                </div>
            </fieldset>
            </form>\n";

	   
            if (!Settings::$noregister){
               echo " <a class='pure-button button-success' href='?t=Reg'>".Settings::_("login","register")."</a> ".Settings::_("login","or");
            }
            echo " <a class='pure-button button-warning' href='.'>".Settings::_("login","back")."</a>"; echo "</fieldset></form>\n";
            echo "</div>\n";
        }
    }
}
?>
