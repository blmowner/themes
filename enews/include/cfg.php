<?php

/**
 * @author MSUITICWS06
 * @copyright 2011
 */

/*$HOST = "klas.msu.edu.my";
$USER = "root";
$PASS = "msusvc@1122klas1";
$DB   = "eadvertorial";*/
$HOST = "10.0.11.72";
$USER = "webcms";
$PASS = "1234";
$DB   = "eadvertorial";




$CONN = mysql_connect($HOST, $USER, $PASS) or die(mysql_error());
mysql_select_db($DB, $CONN) or die(mysql_error());


$HOST2 = "localhost";
$USER2 = "root";
$PASS2 = "ptplsvc@1122webcms";
$DB2   = "msucolombo";


$CONN2 = mysql_connect($HOST2, $USER2, $PASS2) or die(mysql_error());
mysql_select_db($DB2, $CONN2) or die(mysql_error());


?>