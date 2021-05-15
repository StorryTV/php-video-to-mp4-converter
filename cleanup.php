<?php

/** define the directory **/
$dir = "./original/";
$dir2 = "./converted/";

/*** cycle through all files in the directory ***/
foreach (glob($dir."*.*") as $file) {

	/*** if file is 24 hours (86400 seconds) old then delete it ***/
	if(time() - filemtime($file) > 86400){
		if ($file != '.*') {
			echo 'deleting: ' . $file . "\n";
			unlink($file);
		}
	}
}

foreach (glob($dir2."*.*") as $file) {

	/*** if file is 24 hours (86400 seconds) old then delete it ***/
	if(time() - filemtime($file) > 86400){
		if ($file != '.*') {
			echo 'deleting: ' . $file . "\n";
			unlink($file);
		}
	}
}

?>
