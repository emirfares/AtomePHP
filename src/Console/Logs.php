<?php
	namespace Console;

	/**	@class		Logs
	 *	@author		Jean Walrave 
	 *	@abstract 	Handling log files & console printing.
	 */
	class Logs
	{
		public static function init_logs()
		{
			$date = date("d-m-Y");
			$time = date("H:i:s");

			# Checking logs dir
			if (!is_dir('logs')) 
			{
				self::print_log('debug','"Logs" dir doesn\'t exist, creating ...');
   				mkdir('logs');         
			}
			if (!file_exists('logs/'.$date.'.txt'))
			{
				$log_file = fopen('logs/'.$date.'.txt', "wb");
				fputs($log_file, "-- Log file created on $date at $time --\n");
				fclose($log_file);
			}
		}

		public static function print_log($log_type, $text, $save = true, $die = false)
		{
			
			$text = "[$log_type] $text \n";

			if (DEBUG AND ($log_type === 'debug' OR $log_type == 'launch'))
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