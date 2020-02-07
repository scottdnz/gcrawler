<?php
namespace Lib;
require "../vendor/autoload.php";

use \DateTime;

/**
 * Class QuickSchedule
 * 
 * Provides a way of calling the crawler repeatedly
 *
 * @author scott
 */
class QuickSchedule {
	const SECONDS=1;
	const MINUTES=60;
	const HOURS=60*60;
	const DAILY=60*60*24;
    
    /**
     *
     * @var array
     */
    private $params;
    
    private $callback;

    /**
     * QuickSchedule constructor
     */
    public function __construct($params) {
        $this->params = $params;
    }
    
    public function run($callback) {
        $lastDatetimeRan = $this->params["datetime_start"];
        $i = 0;
        $recurringSecondsNum = $this->params["frequency"] * $this->params["number"];
        
        if (array_key_exists("datetime_end", $this->params) && ! is_null($this->params["datetime_end"]) ) {
            while ((new DateTime()) < $this->params["datetime_end"]) {
                echo "Occurred: #" . ++$i . PHP_EOL;
                
                $callback();
                
                sleep($recurringSecondsNum);
            }
        }
        
    }

}

