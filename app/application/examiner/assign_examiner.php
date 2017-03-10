<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: assign_examiner.php
//
// Created by: Zuraimi on 11-Jun-2015
// Modified by: Zuraimi on 11-Jun-2015l
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
		$tmpSearchStudent = " AND d.student_matrix_no = '$searchStudent'";
	}
	else 
	{
		$tmpSearchStudent="";
	}
	if ($searchStudentName!="") 
	{
		$tmpSearchStudentName = " AND name LIKE '%$searchStudentName%'";
	}
	else 
	{
		$tmpSearchStudentName="";
	}
	 $sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
		b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, 
		c.description AS status_desc, d.student_matrix_no, d.reviewer_status,
		a.verified_status, c2.description as verified_desc
		FROM pg_proposal a
		LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type) 
		LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
		LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status) 
		LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
		WHERE a.verified_status IN ('INP','REQ','APP','AWC')"
		.$tmpSearchThesisId." "
		.$tmpSearchStudent." "."
		AND d.status IN ('INP')
		AND a.archived_status IS NULL
		ORDER BY a.verified_status, a.pg_thesis_id";
			
		$result2 = $db->query($sql2); 
		$db->next_record();
		
		$pgThesisIdArray = Array();	
		$studentMatrixNoArray = Array();
		$proposalIdArray = Array();
		$reportDateArray = Array();
		$thesisTitleArray = Array();
		$verifiedStatusArray = Array();
		$examinerStatusArray = Array();
		$verifiedDescArray = Array();	
		$statusDescArray = Array();	
		
		$no1=0;
		$no2=0;
		if (mysql_num_rows($result2) > 0){
			do {
				$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
				$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
				$studentNameArray[$no1] = $db->f('name');						
				$proposalIdArray[$no1] = $db->f('id');
				$reportDateArray[$no1] = $db->f('theReportDate');
				$thesisTitleArray[$no1] = $db->f('thesis_title');
				$verifiedStatusArray[$no1] = $db->f('verified_status');
				$examinerStatusArray[$no1] = $db->f('examiner_status');	
				$verifiedDescArray[$no1] = $db->f('verified_desc');	
				$statusDescArray[$no1] = $db->f('status_desc');
				$no1++;
				
			} while ($db->next_record());
		
			$studentNameArray = Array();
			for ($i=0; $i<$no1; $i++){
				$sql9 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNoArray[$i]'"
				.$tmpSearchStudentName." ";
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
					$verifiedStatusArray[$no2] = $verifiedStatusArray[$i];
					$examinerStatusArray[$no2] = $examinerStatusArray[$i];
					$verifiedDescArray[$no2] = 	$verifiedDescArray[$i];
					$statusDescArray[$no2] = $statusDescArray[$i];
					$no2++;
				}
			}
			if ($no2 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}	
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
	}
	$row_cnt = $no2;
}
else 
{
	$sql2 = "SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.report_date,'%d-%b-%Y') AS theReportDate, a.thesis_title, a.thesis_type, 
	b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.status, a.discussion_status, 
	c.description AS status_desc, d.student_matrix_no, d.examiner_status,
	a.verified_status, c2.description as verified_desc
	FROM pg_proposal a
	LEFT JOIN ref_thesis_type b ON (b.id = a.thesis_type) 
	LEFT JOIN ref_proposal_status c ON (c.id = a.status) 
	LEFT JOIN ref_proposal_status c2 ON (c2.id = a.verified_status) 
	LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id) 
	WHERE a.verified_status IN ('INP','REQ','APP','AWC')
	AND d.status IN ('INP')
	AND a.archived_status IS NULL
	ORDER BY a.verified_status, a.pg_thesis_id";
			
	$result2 = $db->query($sql2); 
	$db->next_record();
	
	$pgThesisIdArray = Array();	
	$studentMatrixNoArray = Array();
	$proposalIdArray = Array();
	$reportDateArray = Array();
	$thesisTitleArray = Array();
	$verifiedStatusArray = Array();
	$examinerStatusArray = Array();
	$verifiedDescArray = Array();	
	$statusDescArray = Array();	
	
	$no1=0;
	$no2=0;
	if (mysql_num_rows($result2) > 0){
		do {
			$pgThesisIdArray[$no1] = $db->f('pg_thesis_id');	
			$studentMatrixNoArray[$no1] = $db->f('student_matrix_no');
			$studentNameArray[$no1] = $db->f('name');						
			$proposalIdArray[$no1] = $db->f('id');
			$reportDateArray[$no1] = $db->f('theReportDate');
			$thesisTitleArray[$no1] = $db->f('thesis_title');
			$verifiedStatusArray[$no1] = $db->f('verified_status');
			$examinerStatusArray[$no1] = $db->f('examiner_status');	
			$verifiedDescArray[$no1] = $db->f('verified_desc');	
			$statusDescArray[$no1] = $db->f('status_desc');
			$no1++;
			
		} while ($db->next_record());
		
		$studentNameArray = Array();
		for ($i=0; $i<$no1; $i++){
			$sql9 = "SELECT name
			FROM student
			WHERE matrix_no = '$studentMatrixNoArray[$i]'"
			.$tmpSearchStudentName." ";
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
				$verifiedStatusArray[$no2] = $verifiedStatusArray[$i];
				$examinerStatusArray[$no2] = $examinerStatusArray[$i];
				$verifiedDescArray[$no2] = 	$verifiedDescArray[$i];
				$statusDescArray[$no2] = $statusDescArray[$i];
				$no2++;
			}
		}
		if ($no2 == 0) {			
			$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";			
		}	
	}
	else {
		$msg[] = "<div class=\"error\"><span>No record(s) found.</span></div>";
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
		
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script src="../../../lib/js/jquery.colorbox.js"></script>
		<script src="../../lib/js/jquery.mask_input-1.3.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
   		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
    	<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>	
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>		
		</head> 
	<body>  
	<?php
	if(!empty($msg)) 
	{
		foreach($msg as $err) 
		{
			echo $err;
		}
	}
	?>	
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">				

				<fieldset>
				<legend><strong>List of Thesis / Proposal</strong></legend>
					<table>
						<tr>							
							<td>Please enter searching criteria below:-</td>
						</tr>
					</table>
					<br/>
					<table>
						<tr>
							<?$searchRequestDate = date("d-M-Y");?>
							<td>Thesis / Project ID</td>
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
					<table>
						<tr>							
							<td>Searching Results:- <?=$row_cnt ?> record(s) found.</td>
						</tr>
					</table>
					<? if($row_cnt > 5)
					{ ?>
						<div id = "tabledisplay" style="overflow:auto; height:500px;">
					<? }
					else
					{?>
						<div id = "tabledisplay" style="overflow:auto;">		
					<? }
					?>
					<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="100%" class="thetable">
                      <tr>
                        <th width="24" align="center"><strong>No.</strong></th>
                        <th width="86"><strong>Faculty Status </strong></th>
                        <th width="137"><strong>Student Name</strong></th>
                        <th width="95"><strong>Thesis / Project ID</strong></th>
                        <th width="199"><strong>Thesis / Project Title</strong></th>
						<th width="102"><strong>Evaluation Panel</strong></th>
                      </tr>
                      <?
						if ($row_cnt>0) {
						$no=0;
						//while($db->next_record()) {	
						for ($i=0; $i<$no2; $i++){
						
							$thesisTitleString[$i] = strip_tags($thesisTitleArray[$i]);
							
							if (strlen($thesisTitleString[$i]) > 100) 
							{
								$more[$i] = "<a href=\"#\" value=\".$pgThesisIdArray[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitleArray[$i])."\">... Read more</a>";
							}
							//$string;
							$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);
												
						?>
                      <tr>
                        <input type="hidden" name="myProposalId[]" size="12" id="proposalId" value="<?=$proposalIdArray[$i];?>"/>
                        <?$myProposalId[$no]=$proposalIdArray[$i];?>
                        <td align="center"><?=$no+1;?>
                          .</td>
                        <?$myStudentMatrixNo[$no]=$studentMatrixNoArray[$i];?>
                        <td width="86"><label name="myVerifiedDesc[]" cols="45" id="verifiedDesc"><?=$verifiedDescArray[$i]?></label></td>
                        
						<td><label name="myStudentName[]" size="30" id="studentName"></label><?=$studentNameArray[$i];?><br/>(<?=$studentMatrixNoArray[$i];?>)</td>
						  
                        <td><a href="../examiner/assign_examiner_outline.php?thesisId=<? echo $pgThesisIdArray[$i];?>&proposalId=<? echo $proposalIdArray[$i];?>" name="myPgThesisId[]" value="<?=$pgThesisIdArray[$i]?>" title="Outline of Proposed Case Study by the Student - Read more...">
                          <?=$pgThesisIdArray[$i];?><br/>
                        </a></td>
                        <?$myPgThesisId[$no]=$pgThesisIdArray[$i];?>
                        <td><label name="myThesisTitle[]" cols="45" id="thesisTitle"><?=$thesisTitleCut[$i]?></label><?=$more[$i]?></td>
                        
                        <td width="120"><?$_SESSION['studentMatrixNo']=$studentMatrixNoArray[$i];
						if ($examinerStatusArray[$i] =='A') {?>
								<a href="../examiner/assign_examiner_change.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>">Change <img src="../images/person_reassigned.jpg" width="20" height="19" style="border:0px;" title="Examiner has been assigned" /></a><br/>
								<br/>
							  <a href="../examiner/assign_examiner_view.php?mn=<? echo $myStudentMatrixNo[$no]?>&tid=<? echo $myPgThesisId[$no]?>">View</a>
							<?}
							else {?>
								<a href="../examiner/assign_examiner_change.php?pid=<?php echo $myProposalId[$no]?>&tid=<? echo $myPgThesisId[$no]?>&mn=<? echo $myStudentMatrixNo[$no]?>">Assign <img src="../images/person_assigned.jpg" width="20" height="19" style="border:0px;" title="Examiner is not assigned yet" /></a>
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




