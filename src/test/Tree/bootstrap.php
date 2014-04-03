<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../yii-1.1.14.f0fee9/framework/yiit.php';
$config=dirname(__FILE__).'/config.php';

require_once($yiit);

Yii::createWebApplication($config);
