<?
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: approve_proposal.php
//
// Created by: Zuraimi on 26-Dec-2014
// Modified by: Zuraimi on 30-Dec-2014 - Added code for thesis proposal approval
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$studentMatrixNo=$_SESSION['studentMatrixNo'];


///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 2;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}

function runnum($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
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

if(isset($_POST['btnSubmit']) && ($_POST['btnSubmit'] <> "")) {
	$endorsedStatus=$_POST['endorsedStatus'];
	$respondedByDate=$_POST['respondedByDate'];
	$myApprovalBox=$_POST['myApprovalBox'];
	$myPgThesisId=$_SESSION['myPgThesisId'];
	$myStudentMatrixNo=$_SESSION['myStudentMatrixNo'];
	$myStudentName=$_SESSION['myStudentName'];
	$myReportDate=$_SESSION['myReportDate'];
	$myProposalId=$_SESSION['myProposalId'];

	/*echo 'endorsedStatus'.$endorsedStatus;
	echo 'myPgThesisId'.$myPgThesisId;
	echo 'myStudentMatrixNo'.$myStudentMatrixNo;
	echo 'myStudentName'.$myStudentName;
	echo 'myReportDate'.$myReportDate;
	echo 'myProposalId'.$myProposalId;*/
	
	
	$curdatetime = date("Y-m-d H:i:s");
	
	$proposalApprovalId = "A".runnum('id','pg_proposal_approval');	
	
	if (sizeof($_POST['myApprovalBox'])>0) {
		
		$sql7 = "INSERT INTO pg_proposal_approval
		(id, senate_mtg_date,endorsed_by,endorsed_date,endorsed_remarks,insert_by,insert_date,modify_by,modify_date)
		VALUES ('$proposalApprovalId',STR_TO_DATE('$senateMtgDate','%d-%b-%Y'), '$userid', '$curdatetime', '$endorsedRemarks', '$userid', '$curdatetime', '$userid', '$curdatetime')";
			
		$result_sql7=$dbg->query($sql7);
	
	}
	
	$curdatetime = date("Y-m-d H:i:s");
	
	//$process = $dbg->query($sql5);
	
	for ($i=0; $i<sizeof($_POST['myApprovalBox']); $i++) {
		
		$sql6_1 = "SELECT id, report_date, thesis_title, thesis_type, introduction, objective, 
		description,discussion_status,verified_by, 
		IFNULL(verified_date,'0000-00-00 00:00:00') as verified_date, verified_status, verified_remarks, endorsed_by, 
		IFNULL(endorsed_date,'0000-00-00 00:00:00') as endorsed_date, endorsed_remarks,status, 
		insert_by, IFNULL(insert_date,'0000-00-00 00:00:00') as insert_date, 
		modify_by, IFNULL(modify_date,'0000-00-00 00:00:00') as modify_date, 
		pg_thesis_id, pg_proposal_approval_id
		FROM pg_proposal 
		WHERE id = '$myProposalId[$i]'";

		//echo "sql6_1 ".$sql6_1;exit();
		$result6_1 = $dbg->query($sql6_1);		
		//var_dump($dbg);
		$dbg->next_record();
		
		
		$id = $dbg->f('id'); 
		$reportDate = $dbg->f('report_date'); 			
		$thesisTitle = $dbg->f('thesis_title'); 
		$thesisType = $dbg->f('thesis_type'); 
		$introduction = $dbg->f('introduction'); 
		$objective = $dbg->f('objective'); 
		$description = $dbg->f('description'); 
		$discussionStatus = $dbg->f('discussion_status'); 
		$verifiedBy = $dbg->f('verified_by'); 
		$verifiedDate = $dbg->f('verified_date'); 
		$verifiedStatus = $dbg->f('verified_status'); 
		$verifiedRemarks = $dbg->f('verified_remarks');
		$endorsedBy = $dbg->f('endorsed_by'); 
		$endorsedDate = $dbg->f('endorsed_date'); 
		$endorsedRemarks = $dbg->f('endorsed_remarks'); 
		$status = $dbg->f('status'); 
		$insertBy = $dbg->f('insert_by'); 
		$insertDate = $dbg->f('insert_date'); 
		$modifyBy = $dbg->f('modify_by'); 
		$modifyDate = $dbg->f('modify_date'); 
		$pgThesisId = $dbg->f('pg_thesis_id'); 
		$pgProposalApprovalId = $dbg->f('pg_proposal_approval_id');
		
		$proposal_id = "P".runnum('id','pg_proposal');	
		
		$sql6_2 = "INSERT INTO pg_proposal 
		(id, report_date, thesis_title, thesis_type, introduction, objective, description, discussion_status, 
		verified_by, verified_date, verified_status, verified_remarks, 
		endorsed_by, endorsed_date, endorsed_remarks, status, 
		insert_by, insert_date, modify_by, modify_date, pg_thesis_id, pg_proposal_approval_id)
		VALUES
		('$proposal_id', '$reportDate', '$thesisTitle', '$thesisType', '$introduction', '$objective', '$description', '$discussionStatus', 
		'$verifiedBy', '$verifiedDate', '$verifiedStatus', '$verifiedRemarks', 
		'$userid', '$curdatetime', '$endorsedRemarks','$endorsedStatus', 
		'$insertBy', '$insertDate', '$userid', '$curdatetime', '$pgThesisId','$proposalApprovalId')";
			
		//echo "sql6_2 ".$sql6_2;

		$result6_2 = $dbg->query($sql6_2); 					
		//var_dump($dbg);
		////$dbg->next_record();
	
		
		$sql6 = "UPDATE pg_proposal 		
			SET archived_status = 'ARC', archived_date = '$curdatetime', 
			modify_by = '$modifyBy', modify_date = '$modifyDate' 			
			WHERE id = '$myProposalId[$i]'";
			
		/*$sql6 = "UPDATE pg_proposal SET
		endorsed_date = '$curdatetime',
		endorsed_remarks = '$approvalRemark', endorsed_by = '$userid', status = '$approvalStatus',
		modify_by = '$userid',modify_date = '$curdatetime'
		WHERE id = '$myProposalId[$i]'
		AND archived_status is NULL";*/
		
		//echo $sql6;echo "XXXXXXXXXX";exit();
		$dbg->query($sql6); 
		//// $dbg->next_record();
		
			
		
			
	} 
	//$process = $dbg->query($sql6);

	if (strcmp($endorsedStatus,"APP")==0){
			
		for ($i=0; $i<sizeof($_POST['myApprovalBox']); $i++) {			
			$sql7_1 = "UPDATE pg_thesis
				SET ref_thesis_status_id_proposal = '$endorsedStatus', ref_thesis_status_id_defense = 'INP',
				modify_by = '$userid', modify_date = '$curdatetime', status = 'INP'
				WHERE id = '$myPgThesisId[$i]'
				AND student_matrix_no = '$myStudentMatrixNo[$i]'";
			//echo $sql7_1;
			$dbg->query($sql7_1);
			
		}			
			
	}
	else {//endorsedStatus=="DIS"
		for ($i=0; $i<sizeof($_POST['myApprovalBox']); $i++) {	
			 $sql7_2 = "UPDATE pg_thesis
				SET ref_thesis_status_id_proposal = '$endorsedStatus', status = 'INC',
				modify_by = '$userid', modify_date = '$curdatetime'
				WHERE id = '$myPgThesisId[$i]'
				AND student_matrix_no = '$myStudentMatrixNo[$i]'";
				//echo $sql7_2;
				$dbg->query($sql7_2);
		}
	}
			 
	
	/*$process = $dbg->query($sql7);
	$process=true;
	if($process) 
		{       
			
			$msg= "alert(\"Record Save.\");";
		}
	*/

}	
	
 $sql2 = " SELECT a.pg_thesis_id , a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_status, a.verified_by, 
		a.verified_date, a.verified_remarks, a.status as endorsedStatus, a.discussion_status, 
		c.description AS endorsedDesc, c1.description AS verifiedDesc,d.student_matrix_no, e.name,
		a.endorsed_remarks
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type)
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no)	
		LEFT JOIN ref_proposal_status c1 ON (c1.id = a.verified_status) 	 
		WHERE a.status in ('OPN','APP','DIS','APC')
		AND a.verified_status = 'APP'
		AND d.status in ('INP','INC')
		AND archived_status is NULL
		ORDER BY a.pg_thesis_id, a.id";		

		$result2 = $db->query($sql2); 
		//echo $sql2;exit();
		
		//var_dump($db);
		//$db->next_record();

$sql3 = "SELECT value
		FROM pg_parameter
		WHERE id = 'RESPOND_DURATION'
		AND STATUS = 'A'";

		$result3 = $dbb->query($sql3); 
		//echo $sql3;
		//var_dump($dbb);
		$dbb->next_record();
		

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" /> 
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		 
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../js/windowopen.js"></script>	
		
	   		
<script>
<?=$msg;?>
function open_win(what_link,the_x,the_y,toolbar,addressbar,directories,statusbar,menubar,scrollbar,resize,history,pos,wname)
{ 
  var the_url = what_link;
  the_x -= 0;
  the_y -= 0;
  var how_wide = screen.availWidth;
  var how_high = screen.availHeight;

  if(toolbar == "0"){var the_toolbar = "no";}else{var the_toolbar = "yes";}
  if(addressbar == "0"){var the_addressbar = "no";}else{var the_addressbar = "yes";}
  if(directories == "0"){var the_directories = "no";}else{var the_directories = "yes";}
  if(statusbar == "0"){var the_statusbar = "no";}else{var the_statusbar = "yes";}
  if(menubar == "0"){var the_menubar = "no";}else{var the_menubar = "yes";}
  if(scrollbar == "0"){var the_scrollbars = "no";}else{var the_scrollbars = "yes";}
  if(resize == "0"){var the_do_resize =  "no";}else{var the_do_resize = "yes";}
  if(history == "0"){var the_copy_history = "no";}else{var the_copy_history = "yes";}
  if(pos == 1){top_pos=0;left_pos=0;}
  if(pos == 2){top_pos = 0;left_pos = (how_wide/2) -  (the_x/2);}
  if(pos == 3){top_pos = 0;left_pos = how_wide - the_x;}
  if(pos == 4){top_pos = (how_high/2) -  (the_y/2);left_pos = 0;}
  if(pos == 5){top_pos = (how_high/2) -  (the_y/2);left_pos = (how_wide/2) -  (the_x/2);}
  if(pos == 6){top_pos = (how_high/2) -  (the_y/2);left_pos = how_wide - the_x;}
  if(pos == 7){top_pos = how_high - the_y;left_pos = 0;}
  if(pos == 8){top_pos = how_high - the_y;left_pos = (how_wide/2) -  (the_x/2);}
  if(pos == 9){top_pos = how_high - the_y;left_pos = how_wide - the_x;}
  if (window.outerWidth )
  {
    var option = "toolbar="+the_toolbar+",location="+the_addressbar+",directories="+the_directories+",status="+the_statusbar+",menubar="+the_menubar+",scrollbars="+the_scrollbars+",resizable="+the_do_resize+",outerWidth="+the_x+",outerHeight="+the_y+",copyhistory="+the_copy_history+",left="+left_pos+",top="+top_pos;
    wname=window.open(the_url, wname, option);
    wname.focus();
  }
  else
  {
    var option = "toolbar="+the_toolbar+",location="+the_addressbar+",directories="+the_directories+",status="+the_statusbar+",menubar="+the_menubar+",scrollbars="+the_scrollbars+",resizable="+the_do_resize+",Width="+the_x+",Height="+the_y+",copyhistory="+the_copy_history+",left="+left_pos+",top="+top_pos;
    if (!wname.closed && wname.location)
    {
      wname.location.href=the_url;
    }
    else
    {
      wname=window.open(the_url, wname, option);
      //wname.resizeTo(the_x,the_y);
      wname.focus();
      wname.location.href=the_url;
    }
  }
} 
</script>

<script type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script><script type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "css/example.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
</script>

<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0'
		});
});
</script>	

</head>
	<body>  
	
		<form id="form1" name="form1" method="post" action="<? echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">	
		<input type="hidden" name="empid" id="empid" value="<?php echo $user_id; ?>">			
		<fieldset>
			<legend><strong>List of Thesis Proposal for Senate Review and Approval</strong></legend><br/>				
		<?  
		
		$row_cnt = mysql_num_rows($result2);
		if ($row_cnt>0) {?>	
			<table>
				<tr>
					<td width="171">Senate Meeting Date</td>
				   <td width="567"><input type="text" name="senateMtgDate" size="15" id="senateMtgDate" value="<?=date('d-M-Y');?>"/></td>
				 </tr>
			</table>
			<br/>
			<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1">
						

				<tr>						
					<td width="30" align="center"><strong>Tick</strong></td>	
					<td width="25" align="center"><strong>No.</strong></td>					
					<td width="131"><strong>Thesis/Project ID</strong></td>
					<td width="208"><strong>Thesis/Project Title</strong></td>
					<td width="102"><strong>Student </strong></td>												
					<td width="151"><strong>Supervisor </strong></td>
					<td width="100"><strong>Faculty Status</strong></td>
					<td width="100"><strong>Senate Status</strong></td>
				</tr>
				<?
				$no=0;
				while($db->next_record()) {						
					$pgThesisId=$db->f('pg_thesis_id');	
					$studentMatrixNo=$db->f('student_matrix_no');
					$studentName=$db->f('name');						
					$proposalId=$db->f('id');
					$reportDate=$db->f('theReportDate');
					$thesisTitle=$db->f('thesis_title');
					$lecturer_name=$db->f('lecturer_name');
					$lecturer_id=$db->f('lecturer_id');
					$description=$db->f('description');
					$endorsedRemarks=$db->f('endorsed_remarks');
					$endorsedStatus=$db->f('endorsedStatus');
					$endorsedDesc=$db->f('endorsedDesc');
					$verifiedStatus=$db->f('verified_status');
					$verifiedDesc=$db->f('verifiedDesc');
														
														
				?>
					<tr>
						<? if ($endorsedStatus=='APP' || $endorsedStatus=='APC' || $endorsedStatus=='DIS'){
							?><td align="center"><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" disabled="disabled" /></td><?							
						}
							else {
								?><td align="center"><input name="myApprovalBox[]" type="checkbox" value="<?=$no;?>" /></td><?
						}
						?>		
						
						<input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
						<? $myProposalId[$no]=$proposalId;?>
						<?php /*?><?$myProposalId[$no];?><?php */?>
						
						
						<td align="center"><?=$no+1;?></td>	
						
						<td><a href="senate_approval_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $proposalId;?>" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisId;?><br/>
						
						
						<? if ($endorsedRemarks == null || $endorsedRemarks ==""){?>
						
							<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Senate Remark is not yet provided" ></a>Enter Remarks</td>	
						<? }
						else {
						?>
							<img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Senate Remark is provided" ></a>Read Remarks</td>	
						<?
						}?>
						
												
						<? $myPgThesisId[$no]=$pgThesisId;?>
						<? //echo "myPgThesisId[$no] ".$myPgThesisId[$no];?>
						
						<td><label name="myThesisTitle[]" id="thesisTitle" ></label><?=$thesisTitle; ?></td>
						
						<td><label name="myStudentName[]" size="30" id="studentName" ></label><?=$studentName;?>
						(<?=$studentMatrixNo;?>)</td>
						<?$myStudentMatrixNo[$no]=$studentMatrixNo;?>
												
						<td>
						<?	$sqlSupervisor="SELECT ps.id, ps.ref_supervisor_type_id,ne.skype_id,ne.name AS 
									name,ne.empid,
									ne.mobile,rst.description  
									FROM  pg_supervisor ps 
									LEFT JOIN ref_supervisor_type rst ON (rst.id=ps.ref_supervisor_type_id)
									LEFT JOIN new_employee ne ON (ps.pg_employee_empid=ne.empid)
									WHERE ps.pg_student_matrix_no='$studentMatrixNo'
									AND ps.status = 'A'
									GROUP BY ne.name, ps.pg_student_matrix_no";
							
							$result_sqlSupervisor = $db_klas2->query($sqlSupervisor);	
							$row_cnt = mysql_num_rows($result_sqlSupervisor);							
							$no1=1;
							
								if ($row_cnt>0) {
									
									while($row = mysql_fetch_array($result_sqlSupervisor)) 
									{ ?>
										<?=$no1?>) <?=$row["name"];?> (<?=$row["empid"];?>)<br/>								
									<? $no1++;} ?>
									 <? if($endorsedStatus!='DIS') { ?>
									<a href="../supervisor/edit_supervisor_senate.php?mn=<?=$studentMatrixNo;?>" name="mySupervisor[]"><img src="../images/edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor details" ></a>
									 <? } else {} ?>
								<? }
								else {
									    if($endorsedStatus!='DIS') { ?> 
									<a href="../supervisor/edit_supervisor_senate.php?mn=<?=$studentMatrixNo;?>" name="mySupervisor[]">Supervisor unavailable. Please assign.<img src="../images/red_edit.jpg" width="20" height="19" style="border:0px;" title="Supervisor details" ></a>
								<?	} else {}
								}
						?>
						</td>
						<td><label name="myVerifiedDesc[]" id="verifiedDesc" ></label><?=$verifiedDesc; ?></td>
						<td><label name="myEndorsedDesc[]" id="endorsedDesc" ></label><?=$endorsedDesc; ?></td>
										  
					</tr>
				<?
				$no=$no+1;
				};	
				?>
				<? $_SESSION['myPgThesisId'] = $myPgThesisId;?>				
				<? $_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
		</table>				
		<br />		
		<br/>
		</fieldset>

		 <br />
		 <fieldset>
			<legend><strong>Approval Confirmation by Senate</strong></legend><br/>				
			<table>				 
				 <tr>
					<td>Approval Status</td>
					<td>:</td>
					<td>
						<input type="radio" name="endorsedStatus"value="APP" checked="checked"/>Approved
						<input type="radio" name="endorsedStatus"value="APC" />Approved with Changes
						<input type="radio" name="endorsedStatus" value="DIS"/>Disapproved	  
					</td>
				</tr>
				<tr>
				<td>Overall Remarks by Senate</td>				
				<td></td>
				<td><textarea name="endorsedRemarks" class="ckeditor" cols="50" id="endorsedRemarks"></textarea></td>
			</tr>				
		</table>
		</fieldset>
		<? $_SESSION['myApprovalBox'] = $myApprovalBox;?>
			<? $_SESSION['myPgThesisId'] = $myPgThesisId;?>	
			<? $_SESSION['myProposalId'] = $myProposalId;?>		
		<table>
			<tr>	
				<td><input type="submit" name="btnPrintThesis" id="btnPrintProposal" value="Print Proposal List" /></td>
				<td><input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" /></td>
			</tr>
		</table>
		<br/>
		<?}
		else {
			?>
			<table>
				<tr>
					<td>Currently no proposal available for Senate approval.</td>
				</tr>
			</table>
			<?
		}?>

	  </form>
	</body>
</html>




