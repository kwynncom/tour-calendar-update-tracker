<?php

require_once('email.php');

if (PHP_SAPI !== 'cli') die('cli only');

$eo = new kwynn_email();
$eo->smail('test 2020/02/28 9:25pm', 'email test', false);
