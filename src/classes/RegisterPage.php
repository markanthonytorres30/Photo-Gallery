<?php


/**
 * RegisterPage
 *
 * This is the page that lets the user create an account.
 * If there is no account created yet, the acount created
 * here will be the admin.
 */


class RegisterPage extends Page
{
	
	private $admin_account;

	private $included;

	/**
	 * Create Register Page
	 */
	public function __construct($admin_account = false, $included = false){
		$this->admin_account = $admin_account;		
		$this->included 	 = $included;
	}
	
	/**
	 * Display Register Page on website
	 *
	 * @return void
	 */
    public function toHTML(){

        if (Settings::$forcehttps && !$_SERVER["HTTPS"]){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
            exit();
        }else{

            if(!$this->included){

                $this->header();

                echo "<form method='post' action='?t=Reg' class='pure-form pure-form-aligned'>\n";
            echo "<div class='header'>";

                if($this->admin_account){
                    echo "<h1>".Settings::_("register","mainacc")."</h1>";						
                }else{
                    echo "<h1>".Settings::_("register","register")."</h1>";
                }
            echo "</div>";
            echo "<div class='center'>\n";

            }else{
                echo "<form class='pure-form pure-form-aligned' method='post' action='?t=Adm&a=AAc'>\n";
            }

            echo "<fieldset>
            <h2>".Settings::_("account","createaccount")."</h2>
                <div class='pure-control-group'>
                    <label>".Settings::_("register","logintxt")."</label>
                    <input type='text' name='login' value=''>
                </div>
            ";


            /// Password
            echo "<div class='pure-control-group'>
                    <label>".Settings::_("register","passtxt")."</label>
                    <input type='password' name='password' value=''>
                </div>";

            /// Verif
            echo "<div class='pure-control-group'>
                    <label>".Settings::_("register","veriftxt")."</label>
                    <input type='password' name='verif' value=''>
                </div>";


            echo "<div class='pure-controls'><input class='pure-button button-success' type='submit' value='".Settings::_("register","submit")."'></div>";
            echo "</fieldset></form>\n";

            if(!$this->included){
                echo " <a class='pure-button button-warning' href='.'>".Settings::_("register","back")."</a>";
            }

        }
    }
}
?>
