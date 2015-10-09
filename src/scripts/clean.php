<?php

/**
 * Clean
 *
 * Your config.php file is read and thumbnails and job get cleaned
 * call the script on the command line or in cron job:
 * > php <path_to_clean.php>
 * The folder from which you call the script does not matter
 */

// Include class files
$toinclude = array( realpath(dirname(__FILE__)."/../classes/HTMLObject.php"),
    realpath(dirname(__FILE__)."/../classes/Page.php"),
    realpath(dirname(__FILE__)."/../classes/Video.php"),
    realpath(dirname(__FILE__)."/../classes/File.php"),
    realpath(dirname(__FILE__)."/../classes/Cleaning.php"),
    realpath(dirname(__FILE__)."/../classes/Settings.php")
);

foreach ( $toinclude as $class_file ){
    if(!include($class_file)){
        throw new Exception("Cannot find ".$class_file." file");
    }
}


// Perform the cleaning
Cleaning::PerformClean();
?>
