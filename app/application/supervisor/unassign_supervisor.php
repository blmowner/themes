<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: unassign_supervisor.php
//
// Created by: Zuraimi on 16-Jun-2015
// Modified by: Zuraimi on 16-Jun-2015l
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


if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchThesisId = $_POST['searchThesisId'];
	$searchStudent = $_POST['searchStudent'];
	$searchStudentName = $_POST['searchStudentName'];
	
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
		$tmpSearchStudent = " AND d.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	
	 $sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, 
		a.thesis_title, a.thesis_type, a.introduction, a.objective, 
		a.description, a.status, a.discussion_status, d.student_matrix_no, 
		d.supervisor_status, a.verified_status, b.description AS theThesisTypeDescription, 
		c.description AS status_desc, a.verified_status, c2.description AS verified_desc 
		FROM pg_thesis d
		LEFT JOIN pg_proposal a ON (a.pg_thesis_id = d.id) 
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status) 
		WHERE a.verified_status NOT IN ('SAV','CAN')"
		.$tmpSearchThesisId." "
		.$tmpSearchStudent." "." 
		AND a.archived_status IS NULL 
		/*AND d.supervisor_status = 'A' */
		AND d.status = 'INP'";
		
		
		$result2 = $db->query($sql2); 
		$db->next_record();
		
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$supervisorStatusArray = Array();
		$verifiedDescArray = Array();	
		$statusDescArray = Array();	
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');
			$verifiedDescArray[$no1] = $db->f('verified_desc');	
			$statusDescArray[$no1] = $db->f('status_desc');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$pgThesisIdArray[$no2] = $pgThesisIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$reportDateArray[$no2] = $reportDateArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];
				$supervisorStatusArray[$no2] = $supervisorStatusArray[$i];
				$verifiedDescArray[$no2] = 	$verifiedDescArray[$i];
				$statusDescArray[$no2] = $statusDescArray[$i];
				$no2++;
			}
		}
		$row_cnt = $no2;

}
else 
{
		$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, 
		a.thesis_title, a.thesis_type, a.introduction, a.objective, 
		a.description, a.status, a.discussion_status, d.student_matrix_no, 
		d.supervisor_status, a.verified_status, b.description AS theThesisTypeDescription, 
		c.description AS status_desc, a.verified_status, c2.description AS verified_desc 
		FROM pg_thesis d
		LEFT JOIN pg_proposal a ON (a.pg_thesis_id = d.id) 
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status) 
		WHERE a.verified_status NOT IN ('SAV','CAN') 
		AND a.archived_status IS NULL 
		/*AND d.supervisor_status = 'A' */
		AND d.status = 'INP'";
			
		$result2 = $db->query($sql2); 
		$db->next_record();
		
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$supervisorStatusArray = Array();
		$verifiedDescArray = Array();	
		$statusDescArray = Array();	
		
		$no1=0;
		$no2=0;
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$supervisorStatusArray[$no1] = $db->f('supervisor_status');
			$verifiedDescArray[$no1] = $db->f('verified_desc');	
			$statusDescArray[$no1] = $db->f('status_desc');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'
				AND name like '%$searchStudentName%'";
			if (substr($studentMatrixNoArray[$i],0,2) != '07') { 
				$dbConnStudent= $dbc; 
			} 
			else { 
				$dbConnStudent=$dbc1; 
			}
			$result9 = $dbConnStudent->query($sql9); 
			$dbConnStudent->next_record();
			if (mysql_num_rows($result9)>0) {
				$studentNameArray[$no2] = $dbConnStudent->f('name');
				$pgThesisIdArray[$no2] = $pgThesisIdArray[$i];
				$studentMatrixNoArray[$no2] = $studentMatrixNoArray[$i];
				$proposalIdArray[$no2] = $proposalIdArray[$i];
				$reportDateArray[$no2] = $reportDateArray[$i];
				$thesisTitleArray[$no2] = $thesisTitleArray[$i];
				$supervisorStatusArray[$no2] = $supervisorStatusArray[$i];
				$verifiedDescArray[$no2] = 	$verifiedDescArray[$i];
				$statusDescArray[$no2] = $statusDescArray[$i];
				$no2++;
			}
		}
		$row_cnt = $no2;
}
?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />

		<script src="../../lib/js/datePicker/jquery.ui.core.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.widget.js"></script>
		<script src="../../lib/js/datePicker/jquery.ui.datepicker.js"></script>
		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>		
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
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
							<td>Thesis/Project ID</td>
							<td>:</td>
							<td><input type="text" name="searchThesisId" size="15" id="searchThesisId" value="<?=$searchThesisId;?>"/></td>
						</tr>
						<tr>
							<td>Matrix No</td>
							<td>:</td>
							<td><input type="text" name="searchStudent" size="15" id="searchStudent" value="<?=$searchStudent;?>"/></td>
						</tr>
						<tr>
							<td>Student Name</td>
							<td>:</td>
							<td><input type="text" name="searchStudentName" size="30" id="searchStudentName" value="<?=$searchStudentName;?>"/></td>
							<td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no entry is provided, it will search all.</td>
						</tr>
					</table>
					<br/>
					<??>
					<table>
						<tr>							
							<td>Searching Results:- <?=$row_cnt?> record(s) found.</td>
						</tr>
					</table>
					<? if ($row_cnt >5)
					{?>
						<div id = "tabledisplay" style="overflow:auto; height:400px;">
					<? }
					else 
					{ ?>
						<div id = "tabledisplay">
					<? } ?>

					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
                      <tr>
                        <th width="24" align="center"><strong>No.</strong></th>
                        <th width="86"><strong>Faculty Status</strong></th>
                        <th width="88"><strong>Status by Senate</strong></th>
                        <th width="137"><strong>Student Name</strong></th>
                        <th width="95"><strong>Thesis / Project ID</strong></th>
                        <th width="199"><strong>Thesis / Project Title</strong></th>
                        <th width="102"><strong>Supervisor/Co-Supervisor</strong></th>
                      </tr>
                      <?
						if ($row_cnt>0) {
						$no=0;
						//while($db->next_record()) {
						for ($i=0; $i<$no2; $i++){															
						?>
                      <tr>
                        <input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalIdArray[$i];?>"/>
                        <?$myProposalId[$no]=$proposalIdArray[$i];?>
                        
						<td align="center"><?=$no+1;?>.</td>
                        <?$myStudentMatrixNo[$no]=$studentMatrixNoArray[$i];?>
                        <td><label name="myVerifiedDesc[]" cols="45" id="verifiedDesc"><?=$verifiedDescArray[$i]?></label></td>
                        <td><label name="myStatusDesc[]" cols="45" id="statusDesc"><?=$statusDescArray[$i]?></label></td>
                        <td><label name="myStudentName[]" size="30" id="studentName"></label><?=$studentNameArray[$i];?><br/>(<?=$studentMatrixNoArray[$i];?>)</td>
                        <td><a href="unassign_supervisor_outline.php?thesisId=<? echo $pgThesisIdArray[$i];?>&proposalId=<? echo $proposalIdArray[$i];?>" name="myPgThesisId[]" value="<?=$pgThesisIdArray[$i]?>" title="Outline of Proposed Case Study by the Student - Read more..."><?=$pgThesisIdArray[$i];?></a></td>
                        <?$myPgThesisId[$no]=$pgThesisIdArray[$i];?>
                        <td>
						<?
								// strip tags to avoid breaking any html
								$thesisTitle[$i] = strip_tags($thesisTitleArray[$i]);
								
								if (strlen($thesisTitle[$i]) > 100) {
								
									// truncate string
									
								
									// make sure it ends in a word so assassinate doesn't become ass...
									
									$more[$i] = "<a href=\"#\" value=\".$pgThesisIdArray[$i].\" title=\".$thesisTitleArray[$i].\"> . . read more</a>";
								}
								//$string;
								$thesisTitleCut[$i] = substr($thesisTitle[$i], 0, 100);
								?>
                        <label name="myThesisTitle[]" cols="45" id="thesisTitle"><?=$thesisTitleCut[$i]?></label><?=$more[$i]?>
						</td>
                        
                        <td><?$_SESSION['studentMatrixNo']=$studentMatrixNoArray[$i];
								if ($supervisorStatusArray[$i] =='A') {?>
                            <a href="../supervisor/unassign_supervisor_change.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>&proposalId=<? echo $proposalIdArray[$i];?>">Unassign <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Supervisor has been assigned" /></a><br/>
                            <br/>
                          <a href="../supervisor/unassign_supervisor_view.php?mn=<? echo $myStudentMatrixNo[$no]?>&tid=<? echo $myPgThesisId[$no];?>">View</a>
                            <?}
								else {?>
                            <a href="../supervisor/unassign_supervisor_view.php?mn=<? echo $myStudentMatrixNo[$no]?>&tid=<? echo $myPgThesisId[$no];?>">View</a>
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
					</div>
					<br/>
					
		  </fieldset>
<?
				}
				else {
					?>
					<table>
						<tr>
							<td>No record found!</td>
						</tr>
					</table>

					<?
				}				
				?>			  
		</form>
	</body>
</html>




