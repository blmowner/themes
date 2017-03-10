<?php

    /**
     * GENERAL CONNECTION
     * @author MFZ
     */
	$db = new mysql_base();
	$db->Database = "postgrad";
	$db->User     = "root";
	$db->Password = "ptplsvc@1122mvcsql";
	//$db->Password = "klas2@1122sql";
    $db->Host     = "localhost";

    
    /**
     * DO NOT DELETE THIS CONNECTION
     * This connection will be used to cater multiple connection at a same time for e.g: loop in loop
     */    
   	$dba = new mysql_base();
	$dba->Database = "postgrad";
	$dba->User     = "root";
	$dba->Password = "ptplsvc@1122mvcsql";
	//$dba->Password = "klas2@1122sql";
 	$dba->Host     = "localhost";

    
    /**
     * DO NOT DELETE THIS CONNECTION
     * DB Connection for all functions
     * db = database connection, f = function 
     */    
   	$dbf = new mysql_base();
	$dbf->Database = "postgrad";
    $dbf->User     = "root";
	$dbf->Password = "ptplsvc@1122mvcsql";
	//$dba->Password = "klas2@1122sql";
    $dbf->Host     = "localhost"; 


	/**
     * DO NOT DELETE THIS CONNECTION
     * DB Connection for all functions
     * db = database connection, f = function 
     */    
   	$db_klas2 = new mysql_base();
	$db_klas2->Database = "postgrad";
    $db_klas2->User     = "root";
	$db_klas2->Password = "ptplsvc@1122mvcsql";
	//$db_klas2->Password = "klas2@1122sql";
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

       $dbb = new mysql_base();
	$dbb->Database = "postgrad";
	$dbb->User     = "root";
	//$dbb->Password = "ptplsvc@1122";
	$dbb->Password = "";
 	$dbb->Host     = "localhost";
	
	$dbe = new mysql_base();
	$dbe->Database = "postgrad";
	$dbe->User     = "root";
	//$dbc->Password = "ptplsvc@1122";
	$dbe->Password = "";
 	$dbe->Host     = "localhost";
	
	$dbg = new mysql_base();
	$dbg->Database = "postgrad";
	$dbg->User     = "root";
	//$dbc->Password = "ptplsvc@1122";
	$dbg->Password = "";
 	$dbg->Host     = "localhost";
		
	$dbd = new mysql_base();
	$dbd->Database = "postgrad";
	$dbd->User     = "root";
	//$dbd->Password = "ptplsvc@1122";
	$dbd->Password = "";
 	$dbd->Host     = "localhost";
	
?>
