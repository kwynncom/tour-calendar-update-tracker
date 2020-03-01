<?php

require_once('utils/kwutils.php');

$datck = 6;

class dci_dao extends dao_generic {
    const db = 'dci';
    const datv = 6;
    
    function __construct() {
	parent::__construct(self::db /*, 'mongodb://127.0.0.1:9998' */);
	$this->ccoll = $this->client->selectCollection(self::db, 'call');
	$this->rcoll = $this->client->selectCollection(self::db, 'running');
	$this->lcoll = $this->client->selectCollection(self::db, 'latest');
    }
    
    public function getShowsLatest($idsin = false, $lim = PHP_INT_MAX) {
	if ($idsin && !is_array($idsin)) $ids = [$idsin];
	else				 $ids = $idsin;
	
	if ($ids) $q = ['dci_id' => ['$in' => $ids]];
	else      $q = [];
	
	$res = $this->lcoll->find($q, ['sort' => ['datets' => 1]])->toArray();
	if ($res && ($lim === 1 || !is_array($idsin))) return $res[0];
	return $res;
	
    }
    
    public function getRunning($id, $tsl) { return $this->rcoll->find(['dci_id' => $id, 'ts' => ['$lt' => $tsl ]], ['sort' => ['ts' => -1]])->toArray();}
    
    public function putCall($dat, $sfx = false) { 
	
	if ($dat === 'pre') { 
	    $dat = [];
	    $dat['ts'] = time();
	    $dat['status'] = 'pre'; 
	    $dat['callSfx'] = $sfx;
	    $dat['res'] = 'n/a';
	}
	else $dat['status'] = 'post';
	
	$dat['r'] = date('r', $dat['ts']);
	$md5 = md5($dat['res']);
	$dat['md5'] = $md5;
	$dat['datv'] = self::datv;
	
	$this->ccoll->insertOne($dat); 
	
    }
    
    public function getCall($sfx, $since, $ip) { 
	$q = ['ts' => ['$gte' => time() - $since], 'callSfx' => $sfx];
	if (!$ip) $q['status'] = ['$ne' => 'pre'];
	return $this->ccoll->findOne($q, ['sort' => ['ts' => -1]]);     }
	
    public function putShows($ss, $sfx, $cres) { 

	$ca['ts'] = $cres['ts'];
	$ca['r'] =  date('r', $cres['ts']);
	$ca['sfx'] = $sfx;
	
	$sfxs = '';
	foreach($ss as $s) {
	    $s = array_merge($s, $ca);
	    $this->rcoll->insertOne($s);
	    $this->lcoll->upsert(['dci_id' => $s['dci_id']], $s);
	    $sfxs .= $s['dci_id'];
	}
    }
}

if (PHP_SAPI === 'cli' && $datck === dci_dao::datv - 1) {
    $o = dci_dao();
}
unset($datck);
