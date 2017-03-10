<?php

include("../../../lib/common.php");
checkLogin();
ob_start();
session_start();
$userid=$_REQUEST['uid'];
$pgProposalId=$_REQUEST['pid'];

if(isset($_POST['btnDelete']) && ($_POST['btnDelete'] <> ""))
{
	 $msg = array();
	if (isset($_REQUEST['id'])) /* checks weather $_GET['empids'] is set */
	{
		$checkbox = $_REQUEST['id'];
		if (is_array($checkbox)) /* value is stored in $checkbox variable */
		{
			foreach ($checkbox as $key => $checkbox) /* for each loop is used to get id and that id is used to delete the record below */
  			{
				//echo $check = $_REQUEST['id'];
				
				$q="DELETE FROM pg_messages WHERE id = '$checkbox' ";/* Sql query to delete the records whose id is equal to $your_slected_id */
				$db->query($q); /* runs the query */
				
			}
			
		}
		$msg[] = "<div class=\"success\"><span>The Message has been Delete Successfully!</span></div>";
	}
	else 
	{	
		$msg[] = "<div class=\"error\"><span>There is no selected record</span></div>";
	}
}	

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,12)) AS run_id FROM $tblname";
    $sql_slct = $db_klas2;
    $sql_slct->query($sql_slct_max);
    $sql_slct->next_record();

    if($sql_slct->num_rows($sql_slct_max)== 0 || $sql_slct->f("run_id")==NULL) 
	{
        $run_id = date("Ymd").$run_start;
    } 
	else 
	{
        $todate = date("Ymd");
        
        if($todate > substr($sql_slct->f("run_id"),0,8)) 
		{
            $run_id = $todate.$run_start;
        } 
		else 
		{
            $run_id = $sql_slct->f("run_id") + 1; 
        }
    }
    return $run_id;
}

function convertname($user_id)
{
	global $db;
	$sql_login = "SELECT name FROM new_employee WHERE empid='$user_id' union SELECT name FROM student WHERE matrix_no='$user_id'";
	$db->query($sql_login);
	$db->next_record();
	$rows = $db->rowdata();
	$name = $rows['name'];
	return $name;
}
?>
<?php

		$curdatetime = date("Y-m-d H:i:s");

		//$db->free();
///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 5;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}

///////////////////////////////////////////////////////////////
		
		$status = 0;
		$name = convertname($user_id);
		
		$sql2 = "SELECT COUNT(*) as total  
		FROM pg_messages 
		WHERE `receive_by` = '$user_id' 
		AND `process_status` = 'Submit' 
		ORDER BY `status` ASC,`date_time` DESC ";
		
		$dbb->query($sql2);
		$dbb->next_record();
    	$a = $dbb->f('total');
		
		if($a > '0')
		{	
		
    	$sql = "SELECT *  
		FROM pg_messages 
		WHERE `receive_by` = '$user_id' 
		AND `process_status` = 'Submit' 
		ORDER BY `status` ASC,`date_time` DESC 
		LIMIT  $startpoint,$perpage  ";
		
		$result_sql_msg = $db->query($sql);
		$db->next_record();
	
	
?>	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/default.css" />
	<link rel="stylesheet" type="text/css" href="../../../theme/css/CSSTableGenerator.css" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	
    <style type="text/css">
<!--
.style3 {color: #FFFFFF; font-weight: bold; }
.style4 {color: #FFFFFF}
.style6 {color: #000000; font-weight: bold; }
.style7 {color: #000000}
-->
    </style>
</head>
<body>
 <?php
    if(!empty($msg)) {
        foreach($msg as $err) 
		{
            echo $err;
        }
    }
?>
<script>

	$(document).ready(function() {         
		$('.error').hide();
		var showError = <?php echo $msg; ?>;         
		if (showError) {             
			$('.error').fadeIn(500).delay(5000).fadeOut(500);
			$msg.focus();       
		} else {
			
		}
	
	});				
</script>
 <form id="form1" name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
   <p>
     <SCRIPT LANGUAGE="JavaScript">

     </SCRIPT>
       <script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
     </script>
   </p>
   <fieldset>
<legend><strong>View Inbox </strong></legend>	
<div class = "CSSTableGenerator">
	<table width="898">
		<tr bgcolor="#5B74A8">
		  <td width="34"><span class="style4"></span></td>
		    
	        <td width="295"><span class="style4"><strong>From</strong></span></td>
          <td width="313"><p class="style4"><strong>Subject</strong></p></td>
            <td width="145"><span class="style4"><strong>Sent</strong></span></td>
			<td width="87"><span class="style4"><strong>Status</strong></span></td>
      </tr>
		<?
	 		do{
			
			$id = $db->f("id");
   			$from=$db->f("sent_by");
   			$userID=$db->f("user_id");
   			$subject=$db->f("subject");
			$message=$db->f("message");
   			$date=$db->f("date_time");
			$status = $db->f("status");
			
			if($status == 0)
			{
				$open = "New";
				
			}
			else
			{
				$open = "Read";
			}
   		?>
			<tr bgcolor="#DBDBDB">
		 	<?php 
					$time = strtotime($date);
					$myFormatForView = date("d/m/y g:i A", $time); 
					
			?>
		<?
			//echo '<td><a href = "?msg='.$id.'">'.$id.'</a></td>';
			echo '<td><input type="checkbox" name="id[]" value= "<?=$id;?>"/></td>'; 
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$from.'</a></td>';
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$subject.'</a></td>';
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$myFormatForView.'</a></td>';
			echo '<td><a style="color: #000000" href = "read_inbox.php?msg='.$id.'">'.$open.'</a></td>';
			echo '</tr>'

		?>
				<?
				$result = $db->next_record(); 

	 		}while($result);
			 
		?>
	</table>
 </div> 
	   <legend>
	   <input type="submit" name="btnDelete" value="Delete" onclick = "return confirm('Do you wish to proceed?:')" />
	   </legend>

       <p><?
	   	// count total number of result - without LIMIT
    	$count_total_result = "SELECT count(*) as total FROM pg_messages 
		WHERE `receive_by` = '$user_id' 
		AND `process_status` = 'Submit' 
		ORDER BY `status` ASC,`date_time` DESC";
    	$db->query($count_total_result);
		$db->next_record();
    	$a = $db->f('total');					

    	$db->free();
		
			doPages($perpage, 'inbox.php', $varParamSend, $a); 
			//doPages($perpage, 'student_programme_staff.php', $varParamSend, $a);
		}
		else
		{
		
		echo "<fieldset><legend><strong>View Inbox </strong></legend>
				<div>
					<table>
						<tr> 
							<td>
								<p>There is no message available.</p>
							</td>
						</tr> 
					</table>
				</div>
				</fieldset>";
		
		}
		?></p>
		


	  		
</fieldset>
		</table>
	</fieldset>	

 
</form>
</body>
</html>
