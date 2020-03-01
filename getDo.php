<?php

if (PHP_SAPI !== 'cli') die('cli only');

require_once('get.php');
require_once('dci.php');
require_once('dao.php');
require_once('emailDCI.php');

get_outer();

function get_outer() {

    $dao = new dci_dao();

    $sfxs = ['TN&startDate=2020-07-24&toDate=2020-07-24',  
	     'SC&startDate=2020-07-03&toDate=2020-07-03', 
	     'GA&startDate=2020-07-25&toDate=2020-07-25' ];

    foreach($sfxs as $sfx) { 
	if (PHP_SAPI !== 'cli') die('cli only');
	$o = new dci_get($sfx, $dao);
	$res = $o->get();
	$s = dci::parse($res['res']);
	dci_email($s);
	$dao->putShows($s, $sfx, $res);
    }
    
    dci_email($s, 'done');
}