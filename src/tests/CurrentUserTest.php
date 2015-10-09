<?php


/**
 * Unit test Guest Toke
 *
 * I used that for some debug. It's incomplete and I guess
 * It would be better to have a proper framework for unit 
 * test on PHP website. Anyway, it does not harm anyone for now
 */

require_once(realpath(dirname(__FILE__)."/TestUnit.php"));
class CurrentUserTest extends TestUnit
{

    /**
     * Test login & logout
     * @test
     */
    public function test_login_logout(){

        CurrentUser::login("testuser", "testpassword");

        $this->assertEquals("testuser", $_SESSION['login']);
        $this->assertNull($_SESSION['token']);
        $this->assertNotNull(CurrentUser::$account);
        $this->assertEquals("testuser", CurrentUser::$account->login);
        $this->assertFalse(CurrentUser::$admin);

        CurrentUser::logout();
        
        //TODO: Failure because I do require_once. Autoload may solve the issue
        $this->assertNull($_SESSION['login']);
        $this->assertNull(CurrentUser::$account);
        $this->assertNull($_SESSION['token']);
        $this->assertFalse(CurrentUser::$admin);
    }


}
?>
