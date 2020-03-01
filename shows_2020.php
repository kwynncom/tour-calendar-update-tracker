<?php

function getShowID($name) {
    
    switch($name) { 
	case 'Mboro' : return 'a0r0a00000CSAWUAA5';
	case 'Col'   : return 'a0r0a00000CSAVGAA5';
	case 'Sta'   : return 'a0r0a00000CSAW7AAP';
	case 'ATL'   : return 'a0r0a00000CSAWWAA5';
	default      : kwas(0, 'unknown show code');
    }
}