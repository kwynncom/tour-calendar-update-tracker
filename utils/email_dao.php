<?php

require_once(__DIR__ . '/../dao.php');

class dci_email_dao extends dao_generic {
    function __construct() {
	parent::__construct(dci_dao::db);
	$this->ecoll = $this->client->selectCollection(dci_dao::db, 'email');
    }
    
    public function put($s) { 
	$d['subject'] = $s;
	$d['ts'] = time();
	$d['r']  = date('r');
	$this->ecoll->insertOne($d);
    }
    
    public function getLatest() { 
	$res = $this->ecoll->findOne([], ['sort' => ['ts' => -1]]);
	$ret = 0;
	if (isset($res['ts'])) $ret = $res['ts'];
	return $ret;
    }
    
}