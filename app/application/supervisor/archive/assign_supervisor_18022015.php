<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_supervisor.php
//
// Created by: Zuraimi on 14-Jan-2015
// Modified by: Zuraimi on 14-Jan-2015l
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_SESSION['user_id'];
$studentMatrixNo=$_SESSION['studentMatrixNo'];
$curdatetime = date("Y-m-d H:i:s");

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

if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	
	if ($searchThesisId!="") 
	{
		$tmpSearchThesisId = " AND a.pg_thesis_id = '$searchThesisId'";
	}
	else 
	{
		$tmpSearchThesisId="";
	}
	if ($searchStudent!="") 
	{
		$tmpSearchStudent = " AND (d.student_matrix_no = '$searchStudent' OR e.name like '%$searchStudent%')";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
			b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, 
			c.description AS status_desc, d.student_matrix_no, e.name, d.supervisor_status,
			a.verified_status, c2.description as verified_desc
			FROM pg_proposal a
			LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type) 
			LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
			LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status) 
			LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
			LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
			WHERE a.verified_status not in ('SAV')"
			.$tmpSearchThesisId." "
			.$tmpSearchStudent." "."
			AND a.archived_status IS NULL
			AND d.status = 'INP'";
			
	$result2 = $db->query($sql2); 
	//$db->next_record();

}
else 
{
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
			b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, 
			c.description AS status_desc, d.student_matrix_no, e.name, d.supervisor_status,
			a.verified_status, c2.description as verified_desc
			FROM pg_proposal a
			LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type) 
			LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
			LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status) 
			LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
			LEFT JOIN student e ON (e.matrix_no = d.student_matrix_no) 
			WHERE a.verified_status not in ('SAV')
			AND a.archived_status IS NULL
			AND d.status = 'INP'";
			
	$result2 = $db->query($sql2); 
	//$db->next_record();
}
?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />    
		<script src="../../lib/js/jquery.min2.js"></script>
		<script src="../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script type="text/javascript" src="../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>		
	    <style type="text/css"></style>
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		</head> 
	<body>  
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">				
				<fieldset>
				<legend><strong>List of Thesis / Proposal</strong></legend>
					<table>
						<tr>							
							<td><strong>Please enter searching criteria below</strong></td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
							<td>Thesis ID</td>
							<td>:</td>
							<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
						</tr>
						<tr>
							<td>Student Name / Matrix No</td>
							<td>:</td>
							<td><input type="text" name="searchStudent" size="12" id="searchStudent" value="<?=$searchStudent;?>"/></td>
							<td><input type="submit" name="btnSearch" value="Search" />  Note: When clicked, if no parameters are provided, it will search all.</td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>							
							<td><strong>Searching Results:-</strong></td>
						</tr>
					</table>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%">
                      <tr>
                        <td width="24"><strong>No.</strong></td>
                        <td width="86"><strong>Status by Faculty</strong></td>
                        <td width="88"><strong>Status by Senate</strong></td>
                        <td width="137"><strong>Student Name</strong></td>
                        <td width="95"><strong>Thesis / Project ID</strong></td>
                        <td width="199"><strong>Thesis / Project Title</strong></td>
                        <td width="102"><strong>Supervisor</strong></td>
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
							$supervisorStatus=$db->f('supervisor_status');	
							$verifiedDesc=$db->f('verified_desc');	
							$statusDesc=$db->f('status_desc');							
																
						?>
                      <tr>
                        <input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalId;?>"/>
                        <?$myProposalId[$no]=$proposalId;?>
                        <?//echo "myProposalId[$no] ".$myProposalId[$no];?>
                        <td><?=$no+1;?>
                          .</td>
                        <?$myStudentMatrixNo[$no]=$studentMatrixNo;?>
                        <?//echo "myStudentMatrixNo[$no] ".$myStudentMatrixNo[$no];?>
                        <td width="86"><label name="myVerifiedDesc[]" cols="45" id="verifiedDesc">
                          <?=$verifiedDesc?>
                        </label></td>
                        <td width="88"><label name="myStatusDesc[]" cols="45" id="statusDesc">
                          <?=$statusDesc?>
                        </label></td>
                        <td><label name="myStudentName[]" size="30" id="studentName"></label>
                            <?=$studentName;?>
                          <br/>
                          (
                          <?=$studentMatrixNo;?>
                          )</td>
                        <td><a href="assign_supervisor_outline.php?thesisId=<? echo $pgThesisId;?>&proposalId=<? echo $proposalId;?>" name="myPgThesisId[]" value="<?=$pgThesisId?>" title="Outline of Proposed Case Study by the Student - Read more...">
                          <?=$pgThesisId;?>
                        </a></td>
                        <?$myPgThesisId[$no]=$pgThesisId;?>
                        <?//echo "myPgThesisId[$no] ".$myPgThesisId[$no];?>
                        <td><label name="myThesisTitle[]" cols="45" id="thesisTitle">
                          <?=$thesisTitle?>
                        </label></td>
                        
                        <td width="120"><?$_SESSION['studentMatrixNo']=$studentMatrixNo;
								if ($supervisorStatus =='A') {?>
                            <a href="../supervisor/assign_supervisor_change.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>&proposalId=<? echo $proposalId;?>&sname=<?php echo $studentName?>">Change <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" /></a><br/>
                            <br/>
                          <a href="../supervisor/assign_supervisor_view.php?sname=<?php echo $studentName?>&mn=<? echo $myStudentMatrixNo[$no]?>&tid=<? echo $myPgThesisId[$no];?>">View</a>
                            <?}
								else {?>
                            <a href="../supervisor/assign_supervisor_change.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>&proposalId=<? echo $proposalId;?>&sname=<?php echo $studentName?>">Assign <img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor is not assigned yet" /></a>
                            <?}?>
                        </td>
                      </tr>
                      <?
						$no=$no+1;
						};	
						?>
                      <?$_SESSION['myPgThesisId'] = $myPgThesisId;?>
                      <?$_SESSION['myStudentMatrixNo'] = $myStudentMatrixNo;?>
                    </table>
					<br/>
					
		  </fieldset>				
		</form>
	</body>
</html>




