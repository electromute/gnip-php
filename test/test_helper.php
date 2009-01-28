<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../src/Services/Gnip.php';

date_default_timezone_set("America/Los_Angeles");

function assertContains($expected, $collection) 
{
    foreach ($collection as $actual) {
        if ($expected == $actual) { return; }
    }

    PHPUnit_Framework_Assert::assertContains($expected, $collection);
}
?>