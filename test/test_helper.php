<?php
require_once 'PHPUnit/Framework.php';
function __autoload($class_name) {
    $dir = dirname(__FILE__);
    $path = $dir.'/../src/' . $class_name . '.php';
    require_once $path;
}
?>