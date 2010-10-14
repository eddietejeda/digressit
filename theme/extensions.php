<?php


/* AUTO LOAD EXTENSIONS */


/* HANDLE CORE EXTENSIONS */
if ($handle = opendir(TEMPLATEPATH)) {

    while (false !== ($file = readdir($handle))) {
        if (!is_dir($file) && strstr($file, '-functions.php')) {
			require_once(TEMPLATEPATH . '/' . $file);

        }
    }
    closedir($handle);
}


/* HANDLE SECONDARY EXTENSIONS */
/*
$secondary_extensions = TEMPLATEPATH . '/extensions';

if(is_dir($secondary_extensions)){
	if ($handle = opendir($secondary_extensions)) {

	    while (false !== ($file = readdir($handle))) {
	        if (!is_dir($file) && strstr($file, '-functions.php')) {
				require_once($secondary_extensions . '/' . $file);
	        }
	    }
	    closedir($handle);
	}
}
*/




?>