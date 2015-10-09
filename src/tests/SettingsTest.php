<?php


/**
 * Unit test Guest Toke
 *
 * I used that for some debug. It's incomplete and I guess
 * It would be better to have a proper framework for unit 
 * test on PHP website. Anyway, it does not harm anyone for now
 */

require_once(realpath(dirname(__FILE__)."/TestUnit.php"));
class SettingsTest extends TestUnit
{
    /**
     * test set_lang
     * @test
     */
    public function test_set_lang(){
        Settings::set_lang("francais");
        $this->assertEquals(Settings::_("settings", "noregister"), "Bloquer les inscriptions");
    }


}
?>
