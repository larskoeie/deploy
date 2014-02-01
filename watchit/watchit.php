<?php

	class watchIt {
	
    private function __construct() {
    	
    }
    
    
    /*
     *
     *
     */
    static function watch ($var, $name=NULL) {
    	self::writeToFile($var);
    }
    
    /*
     * Watch debug backtrace
     *
     */
    static function watchDBT ($i=NULL) {
      self::watch(self::debug_backtrace($i));	
    }
    
    /*
     * Returns a smaller version of debug_backtrace().
     *
     */
		static function debug_backtrace ($i=NULL) {
		  $dbt = debug_backtrace();
			if (! empty($i))
				return $dbt[$i];
				
		  foreach ($dbt as $br)
		  	$db[] = array(
		  	  'file' => $br['file'],
		  	  'line' => $br['line'],
		  	  'function' => $br['function'],
		  	);
		  
		  return $db;
		}
	
	
		static function writeToFile ($var) {
			$fh = fopen(self::getDataFileName(), 'a');
    	fwrite($fh, print_r($var, 1));
    	fclose($fh);
    	
		}
		
		static function getDataFileName () {
		  return '/home/lars/watchit.log';
		}
	}
	
	