<?php 
include_once   './Framework/NWF.php';
include_once   'config.php';

define("ROOT_PATH", dirname(__FILE__));

$app = new NWF(MOD_MVC, MOD_SQL, MOD_GET, MOD_FORM);
$app->mvc->render();