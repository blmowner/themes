<?php
session_start();

/**
 * @author MJMZ
 * @copyright 2011
 * A common file will be included in every page.
 */

include("mysql_base.class.php");
include("conf.php");

$sql = "SELECT * FROM base_config";
$db->query($sql);
$db->next_record();
$row = $db->rowdata();

/* global variable - will be used for each page */
$lang = $row['config_language']; /* load language */ 
$charset = $row['config_charset']; /* the character set */
$css = $row['config_theme']; /* load the theme file */
$langSelect = $row['config_lang_select'];
$app_stat = $row['config_status'];

$db->free();
$maintenance_page_name = "";
/* redirecting page if app_status = 0 (system under maintenance)) */
if($maintenance_page_name == load_constant('MAINTENANCE')) {
    $redirectURL = "";
} else {
    $redirectURL = load_constant("REDIRECT_MAINTAINANCE");
}

if($app_stat == load_constant('ZERO')) {
    header("Location:$redirectURL");
}
/* end redirecting page if app_status = 0 (system under maintenance)) */



/**
 * ====================================================================================
 * Function checkLogin() - to handle session, if user not properly login, system will 
 *                         automatically redirect back to login page.
 * author : MJMZ
 * ====================================================================================
 */ 
function checkLogin() {
    $logoutURL = load_constant('REDIRECT_FORCE'); //prepare redirect path - logout when user are not login
    if($_SESSION['user_log'] <> load_constant("LOGIN")) header("location:$logoutURL");
}
/**
 * =====================================================================================
 * Function load language - MJMZ
 * This function will load all error msgs, warning, labeling, word or any hardcoded text
 * author : MJMZ
 * =====================================================================================
 */ 
function load_lang($term) {
    global $dbf;
    global $lang;
    
    $sql = "SELECT text,term FROM base_language_text WHERE term='".$term."' AND language_code='".$lang."'";
    $dbf->query($sql);
    $dbf->next_record();
    
    $rows = $dbf->rowdata();
    if($term != $rows['term']) {
        exit("the language term not match in a database!");        
    } else {   
        $text = $rows['text'];
        return $text;
    }
}

/**
 * ==================================================================================
 * Function to remove dashed ic - MJMZ 
 * This function will remove all "-" (dashed) value  - e.g ic format: 888888-14-8888
 * author : MJMZ
 * ==================================================================================
*/
function remove_dash_ic($nric) {
    $ic = str_replace("-","",$nric);
    return $ic;
}


/**
 * ==================================================================================
 * Function getAge() 
 * This function will get the current age based on DOB => 1983-04-29
 * author : MJMZ
 * ==================================================================================
*/
function getAge($DOB) 
{
    $birth = explode("-", $DOB);
    $age = date("Y") - $birth[0];
    if(($birth[1] > date("m")) || ($birth[1] == date("m") && date("d") < $birth[2]))
    {
            $age -= 1;
    }
    return $age;
}

/**
 * ==================================================================================
 * Function running number e.g FORMAT: YYYYMMDD001 - MJMZ
 * This function will automatically generates running. The running number will
 * automatically reset on the next day.
 * author : MJMZ
 * ==================================================================================
 */
 function run_num($column_name, $tblname) { // column_name, table_name
    
    global $dbf;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX($column_name) AS run_id FROM $tblname";
    $sql_slct = $dbf;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

      
    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) {
        $run_id = date("Ymd").$run_start;
    } else {
        
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) {
            $run_id = $todate.$run_start;
        } else {
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }

    return $run_id;
}

/**
 * ====================================================================================
 * Function tracking user activity - MJMZ
 * author : MJMZ
 * ====================================================================================
 */
function tracking($uid, $activity, $module) 
{
	
	global $dbf;

	$running_no = '00001';
		
	$sql_select_tracking = "SELECT MAX(track_id) AS track_id FROM base_tracking";
	$sql_slct = $dbf;
	$sql_slct->query($sql_select_tracking);
	$sql_slct->next_record();
		
	if($sql_slct->num_rows($sql_select_tracking)== 0 || $sql_slct->f("track_id")==NULL){
		$trackId = date("Ym").$running_no;
	} else {
	   
       $todate = date("Ym");
       
       if($todate > substr($sql_slct->f("track_id"),0,6)) {
        $trackId = $todate.$running_no;
       }
       else {
		  $trackId = $sql_slct->f("track_id") + 1;
        }
	}
			
	$sql_insert_track = "INSERT INTO base_tracking (track_id, track_ip, track_agent, track_uid, track_activity, track_module, track_url, track_time) VALUES('$trackId','".$_SERVER['REMOTE_ADDR']."','".$_SERVER['HTTP_USER_AGENT']."','$uid', '$activity', '$module', '".$_SERVER['SCRIPT_NAME']."', now())"; 
		
	$sql_ins = $dbf;
	$sql_ins->query($sql_insert_track);
				
}

/**
 * ======================================================================================
 * Function to load constant value - MJMZ
 * author : MJMZ
 * ======================================================================================
 */
function load_constant($term) 
{
	global $dbf;

	$sql_slct_const = "SELECT * FROM base_constant WHERE const_term='$term'";
	$sql_cons = $dbf;
	$sql_cons->query($sql_slct_const);
	$sql_cons->next_record();
		
	$rows = $sql_cons->rowdata();
	if($term != $rows['const_term']) 
	{
		exit("Constant not exist! Please check load_constant() spelling!");
	} else {
	   $value = $rows['const_value'];       
	   return $value;
	}
}

/**
 * =========================================================================================
 * Function get single value from db - MJMZ
 * This function will load the single value based on passed paramater (id)
 * author : MJMZ
 * =========================================================================================
 */
function getValue( $colValueName, $tblName, $whereColumn, $passingId) { // selected column name, table name, for where condition, passing id for where
	
	global $dbf;
    $sql_get = "SELECT $colValueName FROM $tblName WHERE $whereColumn='".$passingId."'";
	
	$getValue = $dbf;
	$getValue->query($sql_get);
	$getValue->next_record();

	$rowValue = $getValue->rowdata();
	$theValue = $rowValue[$colValueName];

	return $theValue;

}

/**
 * =========================================================================================
 * Function get single value from db - MJMZ
 * This function will load the single value based on passed paramater (id)
 * author : MJMZ
 * =========================================================================================
 */
function getValueSiso( $colValueName, $tblName, $whereColumn, $passingId) { // selected column name, table name, for where condition, passing id for where
	
	global $dbs;
    $sql_get = "SELECT $colValueName FROM $tblName WHERE $whereColumn='".$passingId."'";
	
	$getValue = $dbs;
	$getValue->query($sql_get);
	$getValue->next_record();

	$rowValue = $getValue->rowdata();
	$theValue = $rowValue[$colValueName];

	return $theValue;

}

/**
 * =========================================================================================
 * Function get single value from db - MJMZ
 * This function will load the single value based on passed paramater (id)
 * author : MJMZ
 * =========================================================================================
 */
function getMaxValue( $colValueName, $tblName) { // selected column name, table name
	
	global $dbf;
    $sql_get = "SELECT MAX($colValueName) as $colValueName FROM $tblName";
	
	$getValue = $dbf;
	$getValue->query($sql_get);
	$getValue->next_record();

	$rowValue = $getValue->rowdata();
	$theValue = $rowValue[$colValueName];

	return $theValue;

}

/**
 * ===========================================================================================
 * Function load_button() 
 * This function will be used to show/hide button based on permission
 * author : MJMZ
 * ===========================================================================================
 */
 function load_button($fileid, $permType, $btnCaption, $cssStyle) { // 1, "DELETE", load_lang("DELETE"), "fancy-button-blue"
    global $dbf;
    global $lang;
    
    if(empty($fileid)) { exit("Please insert the file id @ button."); }
    if(empty($permType)){ exit("Please insert the permission type value for the button."); }
    if(empty($btnCaption)) { $btnCaption="DEFAULT_BUTTON"; }
    if(empty($cssStyle)) { $cssStyle=""; }
    
    $sqlPerm = "SELECT * FROM base_user_permission WHERE user_id='".$_SESSION['user_id']."' 
                AND file_id ='".$fileid."' AND permission='".$permType."'";
    $dbf->query($sqlPerm);
    $dbf->next_record();
    
    $rows = $dbf->rowdata();
    if($permType != $rows['permission']) {
        //exit("Invalid permission type!");
        return;
    } else {
    
        $theButton = "<input type=\"submit\" name=".$permType." value=\"$btnCaption\" class=\"$cssStyle\" />";
        
        return $theButton;
    }
 }
 
/**
 * ===========================================================================================
 * Function load_hyperlink() 
 * This function will be used to show/hide hyperlink based on permission
 * author : MJMZ
 * ===========================================================================================
 */
function load_hyperlink($moduleid, $permType, $linkCaption, $linkURL, $linkTarget) {
    global $dbf;
    global $lang;
    
    if(empty($moduleid)) { exit("Please insert the module id."); }
    if(empty($permType)){ exit("Please insert the permission type value for the hyperlink."); }
    if(empty($linkCaption)) { $linkCaption="DEFAULT_HYPERLINK"; }
    if(empty($linkURL)) { exit("Passing parameter URL not found!"); }
    if(empty($linkTarget)) { $linkTarget=""; }
    
    
    $sqlLinkPerm = "SELECT b.permission_term, b.module_id, a.user_id FROM base_permission_link a
                         LEFT JOIN base_user_permission b ON (b.permission_id = a.permission_id) 
                         WHERE b.permission_term = '$permType' AND user_id='".$_SESSION['user_id']."' AND b.module_id='$moduleid'";
    
    $dbf->query($sqlLinkPerm);
    $dbf->next_record();
    
    $rows = $dbf->rowdata();
    if($permType != $rows['permission_term']) {
        return;
    } else {
    
        $theHyperlink = "<a href=\"$linkURL\" target=\"$linkTarget\" class=\"$permType\">$linkCaption</a>";
        return $theHyperlink;
    }
}


/**
 * =============================================================================================
 * Function dd_menu()
 * This function will automatically load data from reference table into dropdown menu
 * author : MJMZ
 * =============================================================================================
 */
function dd_menu($rowName = array(),$tblName, $selected, $orderBy)
{

    global $dbf;
    
    if(empty($rowName)) {
        $rowName = array('id','title');
    }
    $implodeValue = implode(", ", $rowName); // get the array value
	
	if (!empty($orderBy)) {
        $orderBy = " ORDER BY $orderBy";
    } else {
        $orderBy = " ORDER BY 1";
    }
        
    $sqldd_menu = "SELECT ".$implodeValue." FROM ".$tblName.$orderBy; 
	
    $result = $dbf->query($sqldd_menu);
	//$result=$dbf->next_record();
	
	echo "<option value=\"\">-- Please Select --</option>";
	if($dbf->num_rows($result) > 0)
	{
      while($rows = mysql_fetch_array($result)) 
      {
        $strselect="";
        if($rows[0] == $selected) {
            $strselect="selected";
            echo "<option ".$strselect." value='".$rows[0]."'>".$rows[1]."</option>";
        } else {
            echo "<option value='".$rows[0]."'>".$rows[1]."</option>";
        }
      }
	} 
}  

/**
 * =================================================================================================
 * Function show_permission()
 * This function will enable user to tick permission
 * author : MJMZ
 * =================================================================================================  
 */
 function show_permission($value) 
 {
    global $dbf;

    $sqlPerm = "SELECT b.permission_id, b.permission_term FROM base_module a
                LEFT JOIN base_user_permission b ON (a.module_id = b.module_id) WHERE a.module_id='$value' ORDER BY b.permission_id";
    $result = $dbf->query($sqlPerm);
    if($dbf->num_rows($result) > 0) 
    {
        while($rows = mysql_fetch_assoc($result))
        {
           echo "<input type=\"checkbox\" name=\"select[]\" value='".$rows['permission_id']."' id='".$rows['permission_id']."' /><label for='".$rows['permission_id']."'>".$rows['permission_term']."</label><br />";
        }
    } else{
        echo "";
    }
    echo "<br />";
} 

/**
 * =================================================================================================
 * Function show_permission_value()
 * This function will display all the permission
 * author : MJMZ
 * =================================================================================================  
 */
 function show_permission_value($value) 
 {
    global $dbf;

    $sqlPerm = "SELECT b.permission_id, b.permission_term FROM base_module a
                LEFT JOIN base_user_permission b ON (a.module_id = b.module_id) WHERE a.module_id='$value' ORDER BY b.permission_id";
    $result = $dbf->query($sqlPerm);
    if($dbf->num_rows($result) > 0) 
    {
        while($rows = mysql_fetch_assoc($result))
        {
           echo $rows['permission_term'];
           echo "<br />";
        }   
    
    } else {
        echo "";
    }
}

/**
 * =================================================================================================
 * Function show_tick_permission()
 * This function will show all the ticked permission (by user)
 * author : MJMZ
 * =================================================================================================  
 */
 function show_tick_permission($value,$userId) 
 {
    global $dbf;
    global $dba;

    $sqlPerm = "SELECT b.permission_id, b.permission_term FROM base_module a
                LEFT JOIN base_user_permission b ON (a.module_id = b.module_id) WHERE a.module_id='$value' ORDER BY b.permission_id";
    $result = $dbf->query($sqlPerm);
    if($dbf->num_rows($result) > 0) 
    {
        while($rows = mysql_fetch_array($result))
        {
           //get the data from base_permission_link befor tick on the checkbox
           $sqlTick = "SELECT * FROM base_permission_link WHERE permission_id='".$rows['permission_id']."' AND user_id='$userId'";
           $tick = $dba;
           $tick->query($sqlTick);
           $tick->next_record();
           $therow = $tick->rowdata(); // $therow to be compare with $sqlPerm result
           
           if($rows['permission_id'] == $therow['permission_id']) {
                echo "<input type=\"checkbox\" name=\"select[]\" value='".$rows['permission_id']."' id='".$rows['permission_id']."' checked /><label for='".$rows['permission_id']."'>".$rows['permission_term']."</label><br />";
           } else {
                echo "<input type=\"checkbox\" name=\"select[]\" value='".$rows['permission_id']."' id='".$rows['permission_id']."' /><label for='".$rows['permission_id']."'>".$rows['permission_term']."</label><br />";
           }
        }
    } else{
        echo "";
    }
    echo "<br />";
} 

/**
 * ====================================================================================================
 * Function array_trim() remove all empty/blank array value
 * author : MJMZ
 * ====================================================================================================
 */ 
function array_trim($sv) 
{
	$s = 0;
	$svn = null;
	$c = count($sv);
	for($i = 0; $i < $c; $i++)
	{
		if($sv[$i] != ""){
		$svn[$s++] = trim($sv[$i]);
	}
}
	return $svn;
}


/**
 * =================================================================================================
 * Function get_count_data()
 * Count number or returned result based on certain conditions
 * author : MJMZ
 * =================================================================================================
 */ 
function get_count_data($columnName, $tblname, $whereColumn, $passingParam) {
    global $dbf;
    $sql = "SELECT COUNT($columnName) as count FROM $tblname WHERE $whereColumn='$passingParam'";
    $countVal = $dbf;
    $countVal->query($sql);
    $countVal->next_record();
    
    $rowValue = $countVal->rowdata();
	$countValue = $rowValue['count'];
    
    return $countValue;
}

/**
 * ==================================================================================================
 * Pagination functions
 * Below is a set of functionns used for pagination
 * author : MJMZ
 * ==================================================================================================
 */
     function check_integer($which) {
        if(isset($_REQUEST[$which])){
            if (intval($_REQUEST[$which])>0) {
                //check the paging variable was set or not, 
                //if yes then return its number:
                //for example: ?page=5, then it will return 5 (integer)
                return intval($_REQUEST[$which]);
            } else {
                return false;
            }
        }
        return false;
    }//end of check_integer()

    function get_current_page() {
        if(($var=check_integer('page'))) {
            //return value of 'page', in support to above method
            return $var;
        } else {
            //return 1, if it wasnt set before, page=1
            return 1;
        }
    }//end of method get_current_page()

    function doPages($page_size, $thepage, $query_string, $total=0) {
        
        //per page count
        $index_limit = 10;

        //set the query string to blank, then later attach it with $query_string
        $query='';
        
        if(strlen($query_string)>0){
            $query = "&amp;".$query_string;
        }
        
        //get the current page number example: 3, 4 etc: see above method description
        $current = get_current_page();
        
        $total_pages=ceil($total/$page_size);
        $start=max($current-intval($index_limit/2), 1);
        $end=$start+$index_limit-1;

        echo '<br /><br /><div class="paging">';

        if($current==1) {
            echo '<span class="prn">&lt; Previous</span>&nbsp;';
        } else {
            $i = $current-1;
            echo '<a href="'.$thepage.'?page='.$i.$query.'" class="prn" rel="nofollow" title="go to page '.$i.'">&lt; Previous</a>&nbsp;';
            echo '<span class="prn">...</span>&nbsp;';
        }

        if($start > 1) {
            $i = 1;
            echo '<a href="'.$thepage.'?page='.$i.$query.'" title="go to page '.$i.'">'.$i.'</a>&nbsp;';
        }

        for ($i = $start; $i <= $end && $i <= $total_pages; $i++){
            if($i==$current) {
                echo '<span>'.$i.'</span>&nbsp;';
            } else {
                echo '<a href="'.$thepage.'?page='.$i.$query.'" title="go to page '.$i.'">'.$i.'</a>&nbsp;';
            }
        }

        if($total_pages > $end){
            $i = $total_pages;
            echo '<a href="'.$thepage.'?page='.$i.$query.'" title="go to page '.$i.'">'.$i.'</a>&nbsp;';
        }

        if($current < $total_pages) {
            $i = $current+1;
            echo '<span class="prn">...</span>&nbsp;';
            echo '<a href="'.$thepage.'?page='.$i.$query.'" class="prn" rel="nofollow" title="go to page '.$i.'">Next &gt;</a>&nbsp;';
        } else {
            echo '<span class="prn">Next &gt;</span>&nbsp;';
        }
        
        //if nothing passed to method or zero, then dont print result, else print the total count below:
        if ($total != 0){
            //prints the total result count just below the paging
            echo '<p id="total_count">(total '.$total.' results)</p></div>';
        }
        
    }//end of method doPages() 
    
/**
 * =========================================================================
 * Function download()
 * Common function for download file
 * Author: MJMZ
 * =========================================================================
 */  
 
 function download($column = array(), $tblName, $whereID, $fileID) {
    
    global $dbf;
    
    $implodeCol = implode(", ", $column);

	$sql = "SELECT ".$implodeCol." FROM $tblName WHERE $whereID = $fileID";
	$sql_slct = $dbf;
	$sql_slct->query($sql);
	$sql_slct->next_record();
					
	if(!empty($column[0])) $fileName = $sql_slct->f($column[0]); else exit("ERROR: The files contains no name!");
    if(!empty($column[1])) $fileType = $sql_slct->f($column[1]); else exit("ERROR: Unknown file type!");
    $fileSize = $sql_slct->f($column[2]);
	if(!empty($column[3])) $fileData = $sql_slct->f($column[3]); else exit("ERROR: The file contains Zero (0) byte");
	
	header('Content-length:' . $fileSize);
	header('Content-type:' . $fileType);
	header('Content-Disposition: attachment; filename=' . $fileName); 
	echo $fileData;
    
 }
 
?>