<?php
namespace Tests;

require "../vendor/autoload.php";

use Lib\QuickSchedule;
use \DateTime;
use \DateInterval;

/**
 * Description of TestQuickSchedule
 *
 * @author scott
 */
class TestQuickSchedule {
    
    public function test1() {
        $params = [
            "datetime_start" => (new DateTime()),
            "datetime_end" => (new DateTime())->add(new DateInterval('PT30S')),
            "frequency" => QuickSchedule::SECONDS,
            "number" => 5,
        ];
        
        $qs = new QuickSchedule($params);
        $qs->run(function() {
            echo "\tHi!" . PHP_EOL;
        });
    }
}

$ts = new TestQuickSchedule();
$ts->test1();
