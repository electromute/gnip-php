<?php
$dirName = dirname(__FILE__);

require_once 'PHPUnit/Framework.php';
require_once $dirName . '/../src/Gnip/Activity.php';
require_once $dirName . '/../src/Gnip/Collection.php';
require_once $dirName . '/../src/Gnip/Publisher.php';
function __autoload($class_name) {
	$dir = dirname(__FILE__);
    $path = $dir . '/../src/' . $class_name . '.php';
    require_once $path;
}
date_default_timezone_set("America/Los_Angeles");
?>