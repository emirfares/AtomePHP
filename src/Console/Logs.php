<?php
namespace Console;

class Logs
{
	public static function init_logs()
	{
		$date = date("d-m-Y");
		$time = date("H:i:s");

		if (!file_exists('logs/'.$date.'.txt'))
		{
			$log_file = fopen('logs/'.$date.'.txt', "wb");
			fputs($log_file, "-- Log file created on $date at $time --\n");
			fclose($log_file);
		}
	}

	public static function print_log($log_type, $text, $save = true)
	{
		global $config;

		$text = "[$log_type] $text \n";

		if ($config->debug AND ($log_type === 'debug' OR $log_type == 'launch'))
			print( "(".date("H:i:s").") ".$text);
		else
			print($text);

		if ($save)
		{
			$log_file = fopen('logs/'.date("d-m-Y").'.txt', 'a');
			fputs($log_file, "(".date("H:i:s").") ".$text);
			fclose($log_file);
		}
	}
}
?>