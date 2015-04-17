<?php

// change the following paths if necessary
$yii = dirname(__FILE__) . '/yii/yii/yii.php';
$configDev = dirname(__FILE__) . '/protected/config/dev.php';
$config = dirname(__FILE__) . '/protected/config/prod.php';
if (file_exists($configDev)) {
    define('IS_DEV_SITE', true);
    define('YII_DEBUG', true);
    $config = include($configDev);
} else {
    define('IS_DEV_SITE', false);
    define('YII_DEBUG', false);
    $config = include($config);
}
require_once('functions.php');
// remove the following line when in production mode
// defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
Yii::createWebApplication($config)->run();
