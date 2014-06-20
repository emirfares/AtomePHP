<?php
	function convert($size){
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	function print_debug ($text){
	 	global $config;
		if ($config->debug) {
			print "[DEBUG] : ".$text."\n";
		}
	}

	function print_info ($text){
			print "[INFO] : ".$text."\n";
		
	}

	function print_warning ($text){
			print "[WARNING] : ".$text."\n";
	}

	function print_error ($text,$die=false){
			print "[ERROR] : ".$text."\n";
			if ($die){die();}
	}
?>