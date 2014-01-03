<?php
function convert($size)
 {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
 }
 function print_debug ($text){
 	global $Config;
	if ($Config->debug) {
		print "[Debug] : ".$text;
	}
}

function print_info ($text){
	global $Config;
	if ($Config->debug) {
		print "[Info] : ".$text;
	}
}

function print_warning ($text){
	global $Config;
	if ($Config->debug) {
		print "[Warning] : ".$text;
	}
}

function print_error ($text){
	global $Config;
	if ($Config->debug) {
		print "[Error] : ".$text;
	}
}
