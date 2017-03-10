<?php

    /**
     * GENERAL CONNECTION
     * @author MFZ
     */
	$db = new mysql_base();
	$db->Database = "demopostgrad";
	$db->User     = "root";
	$db->Password = "";
	$db->Host     = "localhost";
	
	$dbLive = new mysql_base();
	$dbLive->Database = "postgrad";
	$dbLive->User     = "root";
	$dbLive->Password = "ptplsvc@1122mvcsql";
	$dbLive->Host     = "localhost";

    
    /**
     * DO NOT DELETE THIS CONNECTION
     * This connection will be used to cater multiple connection at a same time for e.g: loop in loop
     */    
   	$dba = new mysql_base();
	$dba->Database = "demopostgrad";
	$dba->User     = "root";
	$dba->Password = "";
	$dba->Host     = "localhost";

    	$dbb = new mysql_base();
	$dbb->Database = "demopostgrad";
	$dbb->User     = "root";
    $dba->Password = "";
 	$dbb->Host     = "localhost";
	
	$dbe = new mysql_base();
	$dbe->Database = "demopostgrad";
	$dbe->User     = "root";
    $dba->Password = "";
 	$dbe->Host     = "localhost";
	
	$dbg = new mysql_base();
	$dbg->Database = "demopostgrad";
	$dbg->User     = "root";
    $dba->Password = "";
 	$dbg->Host     = "localhost";
		
	$dbd = new mysql_base();
	$dbd->Database = "demopostgrad";
	$dbd->User     = "root";
    $dba->Password = "";
 	$dbd->Host     = "localhost";

	$dbj = new mysql_base();
	$dbj->Database = "demopostgrad";
	$dbj->User     = "root";
    $dba->Password = "";
 	$dbj->Host     = "localhost";

	$dbu = new mysql_base();
	$dbu->Database = "demopostgrad";
	$dbu->User     = "root";
    $dba->Password = "";
 	$dbu->Host     = "localhost";

    /**
     * DO NOT DELETE THIS CONNECTION
     * DB Connection for all functions
     * db = database connection, f = function 
     */    
   	$dbf = new mysql_base();
	$dbf->Database = "demopostgrad";
    	$dbf->User     = "root";
        $dba->Password = "";
	$dbf->Host     = "localhost"; 


	/**
     * DO NOT DELETE THIS CONNECTION
     * DB Connection for all functions
     * db = database connection, f = function 
     */    
   	$db_klas2 = new mysql_base();
	$db_klas2->Database = "demopostgrad";
    	$db_klas2->User     = "root";
        $dba->Password = "";
	$db_klas2->Host     = "localhost"; 

    /**
     * SISO
     * Connection for Single Sign-On
     * db = database, s = siso
     */ 
    $dbs = new mysql_base();
    $dbs->Database = "siso";
    $dbs->User     = "postgrad";
    $dbs->Password = "1234";
    $dbs->Host     = "10.0.11.240";
    
    /**
     * KLAS2
     * Connection for KLAS2
     * db = database, c = klas2
     */ 
    $dbc = new mysql_base();
    $dbc->Database = "cms_ora";
    $dbc->User     = "postgrad";
    $dbc->Password = "1234";
    $dbc->Host     = "10.0.11.231";

    $dbk = new mysql_base();
    $dbk->Database = "cms_ora";
    $dbk->User     = "postgrad";
    $dbk->Password = "1234";
    $dbk->Host     = "10.0.11.231";

    $dbl = new mysql_base();
    $dbl->Database = "cms_ora";
    $dbl->User     = "postgrad";
    $dbl->Password = "1234";
    $dbl->Host     = "10.0.11.231";

    $dbn = new mysql_base();
    $dbn->Database = "cms_ora";
    $dbn->User     = "postgrad";
    $dbn->Password = "1234";
    $dbn->Host     = "10.0.11.231";

    /**
     * KLAS2LK - Colombo
     * Connection for KLAS2LK
     * db = database, c = klas2lk
     */ 
    $dbc1 = new mysql_base();
    $dbc1->Database = "cms_ora";
    $dbc1->User     = "postgrad";
    $dbc1->Password = "1234";
    $dbc1->Host     = "10.0.11.239";

	
?>
