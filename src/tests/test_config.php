<?php

// Folder where your pictures are stored.
// Must be at least readable by web server process
#$config->photos_dir   = "path_to_your_photos_dir_goes_here";
$config->photos_dir   = realpath(dirname(__FILE__))."/tmp/photos/";

// Folder where Galleria parameters and thumbnails are stored.
// Must be writable by web server process
#$config->ps_generated   = "path_where_photoshow_generates_files_goes_here";
$config->ps_generated   = realpath(dirname(__FILE__))."/tmp/generated/";

// Local timezone. Default one is "Europe/Paris".
#$config->timezone = "Europe/Paris";

?>
