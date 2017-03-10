<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Submit Defence Proposal</title>
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
		<script language="JavaScript" src="../../../lib/js/windowopen.js"></script>

	</head> 
	<body> 
	
<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: view_defense.php
//
// Created by: Zuraimi
// Created Date: 06-Jul-2015
// Modified by: Zuraimi
// Modified Date: 06-Jul-2015
//
//**************************************************************************************

include("../../../lib/common.php");
checkLogin();

session_start();
$user_id=$_SESSION['user_id'];
$thesisId = $_GET['tid']; 
$proposalId = $_GET['pid'];
$referenceNo=$_GET['ref'];
$defenseId=$_GET['id'];
$calendarId=$_GET['cid'];

if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
	   
		public function __construct($date) {
			$this->date = strtotime($date);
		}
	   
		public function setTimeZone($timezone) {
			return;
		}
	   
		private function __getDate() {
			return date(DATE_ATOM, $this->date);   
		}
	   
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
	   
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

/**
 *
 * @create a roman numeral from a number
 *
 * @param int $num
 *
 * @return string
 *
 */
function romanNumerals($num) 
{
    $n = intval($num);
    $res = '';
 
    /*** roman_numerals array  ***/
    $roman_numerals = array(
                'M'  => 1000,
                'CM' => 900,
                'D'  => 500,
                'CD' => 400,
                'C'  => 100,
                'XC' => 90,
                'L'  => 50,
                'XL' => 40,
                'X'  => 10,
                'IX' => 9,
                'V'  => 5,
                'IV' => 4,
                'I'  => 1);
 
    foreach ($roman_numerals as $roman => $number) 
    {
        /*** divide to get  matches ***/
        $matches = intval($n / $number);
 
        /*** assign the roman char * $matches ***/
        $res .= str_repeat($roman, $matches);
 
        /*** substract from the number ***/
        $n = $n % $number;
    }
 
    /*** return the res ***/
    return $res;
}

$sql2 = "SELECT a.id, a.reference_no, DATE_FORMAT(a.submit_date, '%d-%b-%Y') as submit_date, 
a.student_matrix_no, 
a.pg_thesis_id, a.pg_proposal_id, a.status as defense_status, 
a.insert_by, a.insert_date, a.modify_by, a.modify_date,	c1.description as defense_desc,
a.respond_status
FROM pg_defense a
LEFT JOIN ref_proposal_status c1 ON (c1.id = a.status)
WHERE a.student_matrix_no = '$user_id'
AND a.reference_no = '$referenceNo'
AND a.pg_thesis_id = '$thesisId'
AND a.pg_proposal_id = '$proposalId'
AND a.archived_status is null";
		
$result2 = $dba->query($sql2); 
$dba->next_record();

$id=$dba->f('id');
$defenseStatus=$dba->f('defense_status');
$defenseDesc=$dba->f('defense_desc');
$defenseDetailId=$dba->f('defense_detail_id');
$defenseDetailDesc=$dba->f('defense_detail_desc');
$defenseDate=$dba->f('defense_date');
$submitDate=$dba->f('submit_date');
$respondStatus=$dba->f('respond_status');
$row_cnt2 = mysql_num_rows($result2);

$sql2_1 = "SELECT a.id as chapter_id, a.chapter_no, a.description as chapter_desc, 
b.id as subchapter_id, b.subchapter_no, b.description as subchapter_desc,
a.discussed_status as chapter_discussed_status, 
b.discussed_status as subchapter_discussed_status
FROM pg_chapter a
LEFT JOIN pg_subchapter b ON (b.chapter_id = a.id)  
WHERE a.status = 'A'
AND a.student_matrix_no = '$user_id'
AND (b.status = 'A' OR b.status IS NULL)
ORDER BY a.chapter_no, b.subchapter_no";

$result2_1 = $dbb->query($sql2_1); 
$dbb->next_record();
$row_cnt2_1 = mysql_num_rows($result2_1);

$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type,
DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
FROM pg_supervisor a 
LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
LEFT JOIN ref_role_status h ON (h.id = a.role_status)
WHERE a.pg_student_matrix_no='$user_id'
AND g.pg_thesis_id = '$thesisId'
AND g.id = '$proposalId'
AND a.acceptance_status = 'ACC'
AND a.ref_supervisor_type_id in ('SV','CS','XS')
AND g.verified_status in ('APP','AWC')
AND g.status in ('APP','APC')
AND g.archived_status IS NULL
AND a.status = 'A'
ORDER BY d.seq, a.ref_supervisor_type_id";

$result_sql_supervisor = $db_klas2->query($sql_supervisor); //echo $sql;
$db_klas2->next_record();
$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);

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
		<script language="JavaScript" type="text/javascript" src="../../../lib/js/tooltip.js"></script>	
	</head>
	<body>
	<form id="form1" name="form1" method="post" enctype="multipart/form-data">	
		<table border="0">
			<tr>
			<td><h3><strong>Defence Proposal Details - VIEW</strong><h3></td>
			</tr>
		</table>
		<table>
			<tr>
				<td>Final Status</td>
				<td>:</td>
				<?if ($defenseDesc=="") $defenseDesc='New';?>
				<td><strong><?=$defenseDesc?></strong></td>
			</tr>
			<tr>
				<td>Reference No</td>
				<td>:</td>
				<td><strong><?=$referenceNo?></strong></td>
			</tr>
			<tr>
				<td>Thesis / Project ID</td>
				<td>:</td>
				<td><label><?=$thesisId;?></label></td>				
			<tr>
				<td>Student Matrix No</td>
				<td>:</td>
				<td><?=$user_id?></td>
			</tr>
				<?
				$sql1 = "SELECT name AS student_name
				FROM student
				WHERE matrix_no = '$user_id'";
				if (substr($user_id,0,2) != '07') { 
					$dbConnStudent= $dbc; 
				} 
				else { 
					$dbConnStudent=$dbc1; 
				}
				$result1 = $dbConnStudent->query($sql1); 
				$dbConnStudent->next_record();
				$sname=$dbConnStudent->f('student_name');

				?>
			<tr>
				<td>Student Name</td>
				<td>:</td>
				<td><?=$sname?></td>
			</tr>  
			<tr>
				<td>Evaluation Session Schedule</td>
				<td>:</td>	
				<?				
					$sql7 = "SELECT const_value
					FROM base_constant
					WHERE const_category = 'DEFENSE_PROPOSAL'
					AND const_term = 'DEFENSE_DURATION'";
					
					$result_sql7 = $db->query($sql7); 
					$db->next_record();
					$defenseDurationParam = $db->f('const_value');
		
					$sql3 = "SELECT a.id, a.defense_date, DATE_FORMAT(a.defense_date,'%d-%b-%Y') as defense_date1, 
					DATE_FORMAT(a.defense_stime,'%h:%i%p') as defense_stime,
					DATE_FORMAT(a.defense_etime,'%h:%i%p') as defense_etime, a.venue, a.recomm_status, a.status, a.ref_session_type_id,
					c.description as session_type_desc
					FROM pg_calendar a
					LEFT JOIN pg_defense b On (b.pg_calendar_id = a.id)
					LEFT JOIN ref_session_type c ON (c.id = a.ref_session_type_id)
					WHERE a.id = '$calendarId'
					AND a.student_matrix_no = '$user_id'
					AND a.thesis_id = '$thesisId'
					AND a.status = 'A'
					ORDER BY a.defense_date ASC";
					
					$result_sql3 = $dba->query($sql3); 
					$dba->next_record();
					
					$recommendedId = $dba->f('id');
					$defenseDate = $dba->f('defense_date1');
					$defenseSTime = $dba->f('defense_stime');
					$defenseETime = $dba->f('defense_etime');	
					$venue = $dba->f('venue');		
					$calendarStatus = $dba->f('status');
					$recommStatus = $dba->f('recomm_status');
					$sessionTypeDesc = $dba->f('session_type_desc');
					?>				
				<td><label><?=$sessionTypeDesc?> - <?=$defenseDate?>, <?=$defenseSTime?> to <?=$defenseETime?>, <?=$venue?></label></td>				
			</tr>   		  			
		</table>
		<br/>		
		<?
			$sqlPublication=" SELECT DATE_FORMAT(a.published_date,'%d-%b-%Y') as published_date, a.publication_title, a.publication_id, 
			c.description as publication_type, a.website, b.description as country_desc, d.publisher_name as publication_name
			FROM pg_defense_publication a
			LEFT JOIN ref_country b ON (b.id = a.country_id)
			LEFT JOIN ref_publication_type c ON (c.id = a.publication_type)
			LEFT JOIN ref_publisher d ON (d.id = a.publication_id)
			WHERE a.reference_no = '$referenceNo'
			/* AND a.pg_defense_id = '$defenseId'*/
			AND a.pg_proposal_id='$proposalId'
			AND a.pg_thesis_id = '$thesisId'
			AND a.student_matrix_no = '$user_id' 
			AND a.ref_session_type_id = 'DEF'
			ORDER BY a.published_date DESC ";			
			
			$resultPublication = $db->query($sqlPublication); 
			$db->next_record();
			$row_cnt = mysql_num_rows($resultPublication);
			?>
			<fieldset>
			<legend><strong>Publications</strong></legend>
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
						<tr>
							<th width="5%">No</th>					
							<th width="10%">Published Date</th>
							<th align="left" width="25%">Title</th>
							<th align="left" width="20%">Name</th>
							<th align="left" width="10%">Type of Publication</th>
							<th align="left" width="15%">Website</th>
							<th align="left" width="15%">Country</th>
						</tr>
						
						<?php
					if ($row_cnt > 0) {
						
						$tmp_no = 0;
						do { 
							?><tr>
									<td align="center"><label><?=$tmp_no+1;?>.</label></td>
									<td align="center"><label><?=$db->f('published_date');?></label></td>
									<td align="left"><label><?=$db->f('publication_title');?></label></td>
									<td align="left"><label><?=$db->f('publication_name');?></label></td>									
									<td align="left"><label><?=$db->f('publication_type');?></label></td>
									<td align="left"><label><?=$db->f('website');?></label></td>
									<td align="left"><label><?=$db->f('country_desc');?></label></td>
								</tr>
							<?
							$tmp_no++;
						} while ($db->next_record());
					}
					else {
						?>
						<table>
							<tr>
								<td><label>No record found!</label></td>
							</tr>
						</table>
						<?
					}?> 
						
				</table>
			</fieldset>
			</br>
			<?
			$sqlPublication=" SELECT conference, location, DATE_FORMAT(conference_sdate,'%d-%b-%Y') as conference_sdate, 
			DATE_FORMAT(conference_edate,'%d-%b-%Y') as conference_edate, presentation_status, presentation_title			
			FROM pg_defense_conference 
			WHERE reference_no = '$referenceNo'
			/* AND pg_defense_id = '$defenseId'*/
			AND pg_proposal_id='$proposalId'
			AND pg_thesis_id = '$thesisId'
			AND student_matrix_no = '$user_id' 
			AND ref_session_type_id = 'DEF'
			ORDER BY conference_sdate DESC ";			
			
			$resultPublication = $db->query($sqlPublication); 
			$db->next_record();
			$row_cnt = mysql_num_rows($resultPublication);
			?>
			<fieldset>
			<legend><strong>Participation in Conference</strong></legend>
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">			
						<tr>
							<th width="5%">No</th>					
							<th align="left" width="20%">Conference</th>
							<th align="left" width="25%">Location</th>
							<th width="10%">Conference Start Date</th>
							<th width="10%">Conference End Date</th>
							<th width="10%">Presentation (Y/N)</th>
							<th align="left" width="20%">Title of Presentation</th>							
						</tr>
						
						<?php
					if ($row_cnt > 0) {
						
						$tmp_no = 0;
						do { 
							?><tr>
									<td align="center"><label><?=$tmp_no+1;?>.</label></td>
									<td align="left"><label><?=$db->f('conference');?></label></td>
									<td align="left"><label><?=$db->f('location');?></label></td>
									<td align="center"><label><?=$db->f('conference_sdate');?></label></td>									
									<td align="center"><label><?=$db->f('conference_edate');?></label></td>
									<?if ($db->f('presentation_status')=="Y") $presentationStatusDesc = "Yes";
									else $presentationStatusDesc = "No";?>
									<td align="center"><label><?=$presentationStatusDesc;?></label></td>
									<td align="left"><label><?=$db->f('presentation_title');?></label></td>
								</tr>
							<?
							$tmp_no++;
						} while ($db->next_record());
					}
					else {
						?>
						<table>
							<tr>
								<td><label>No record found!</label></td>
							</tr>
						</table>
						<?
					}?> 
						
				</table>
			</fieldset>
			<br/>
			<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='defense_proposal.php';" /></td>		
			</tr>
		</table>
		<h3><strong>List of Supervisor/Co-Supervisor</strong></h3>
		<table border="1" style="border-collapse:collapse;" cellpadding="2" cellspacing="1" width="90%" class="thetable">			
				<tr>
					<th width="5%">No</th>					
					<th width="15%">Role / Acceptance Date</th>
					<th width="15%" align="left">Staff ID</th>
					<th width="25%" align="left">Name</th>
					<th width="5%" align="left">Faculty</th>
					<th width="5%">View Feedback</th>
					<th width="15%" align="left">Proposed Status</th>
					<th width="20%">Last Update</th>
				</tr>
				<?
				$varRecCount=0;	
				if ($row_cnt_supervisor>0) {
					do {
					
					
						$employeeId = $db_klas2->f('pg_employee_empid');
						$supervisorType = $db_klas2->f('supervisor_type');
						$supervisorTypeId = $db_klas2->f('ref_supervisor_type_id');
						$acceptanceDate = $db_klas2->f('acceptance_date');
						$roleStatusDesc = $db_klas2->f('role_status_desc');
					
						$sql_employee="SELECT  b.name, c.id, c.description
						FROM new_employee b 
						LEFT JOIN dept_unit c ON (c.id = b.unit_id) 
						WHERE b.empid= '$employeeId'";
							
						$result_sql_employee = $dbc->query($sql_employee);
						$dbc->next_record();
						
						$employeeName = $dbc->f('name');
						$departmentId = $dbc->f('id');
						$departmentName = $dbc->f('description');

						$varRecCount++;

						?>
						<tr>
							<td align="center"><?=$varRecCount;?>.</td>	
							<td align="left">
							<?if ($supervisorTypeId != 'XS') {?>
								<?=$supervisorType;?><br/><?=$roleStatusDesc?>
							<?}
							else {
								?>
								<span style="color:#FF0000"><?=$supervisorType;?></span><br/><?=$roleStatusDesc?>
								<?
							}?>
							<br/><?=$acceptanceDate?></td>
							<td align="left"><?=$employeeId;?></td>
							<td align="left"><?=$employeeName;?></td>
							<td align="left"><a href="javascript:void(0);" onMouseOver="toolTip('<?=$departmentName;?>', 300)" onMouseOut="toolTip()"><?=$departmentId;?></a></td>
							
							<?
							$sql12 = "SELECT a.id as defense_id, b.id as defense_detail_id,
							b.status as defense_detail_status, c.description as defense_detail_desc,
							DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i%p') AS responded_date
							FROM pg_defense a
							LEFT JOIN pg_defense_detail b ON (b.pg_defense_id = a.id)
							LEFT JOIN ref_proposal_status c ON (c.id = b.status)
							WHERE a.student_matrix_no = '$user_id'
							AND a.reference_no = '$referenceNo'
							AND b.pg_employee_empid = '$employeeId'
							AND a.pg_thesis_id = '$thesisId'
							AND a.pg_proposal_id = '$proposalId'
							AND a.archived_status is null
							AND b.archived_status is NULL";
							
							$result12 = $dbg->query($sql12); 
							$dbg->next_record();
							$row_cnt12 = mysql_num_rows($result12);
							$defenseId=$dbg->f('defense_id');
							$defenseDetailId=$dbg->f('defense_detail_id');
							$defenseDetailStatus=$dbg->f('defense_detail_status');
							$defenseDetailDesc=$dbg->f('defense_detail_desc');
							$respondedDate=$dbg->f('responded_date');
							if ($row_cnt12>0) {
								?>
								<td align="center"><a href="view_defense_view_feedback.php?tid=<?=$thesisId;?>&pid=<?=$proposalId;?>&eid=<?=$employeeId;?>&id=<?=$id;?>&ref=<?=$referenceNo;?>&cid=<?=$calendarId?>">View</a></td>	
								<?}
								else {
									?>
									<td></td>
									<?
								}
								if ($defenseDetailStatus == '') {
								?>
									<td align="left"><label>Expecting Work Completion</label></td>
								<?}
								else if ($defenseDetailStatus == 'SV1') {
								?>
									<td align="left"><label><span style="color:#FF0000"><?=$defenseDetailDesc;?></span></label></td>
								<?}
								else {
									?>								
									<td align="left"><label><?=$defenseDetailDesc;?></label></td>
									<?
								}?>
							<td align="left"><label><?=$respondedDate;?></label></td>						
						</tr>
								
				<? 	} while($db_klas2->next_record());
					
				}
				else {
					?>
					<table>				
						<tr><td>Notes: <br/>No Supervisor/Co-Supervisor in the list.
									Possible Reasons:-<br/>
									1. Supervisor/Co-Supervisor is yet to be assigned<br/>
									2. Pending approval by the Senate.<br/>
									3. If already assigned, it could be the Supervisor/Co-Supervisor pending to accept<br/><br/>
									<span style="color:#FF0000"> Make sure your Supervisor has been assigned first before submit the Monthly Defence report</span></td>
						</tr>
					</table>
					<?
				}?>	
				</table>
		<?
		
		
		$chapterArray = Array();
		$no=0;
		do {											
			$chapterId[$no]=$dbb->f('chapter_id');	
			$chapterNo[$no]=$dbb->f('chapter_no');
			$chapterDesc[$no]=$dbb->f('chapter_desc');
			$subchapterId[$no]=$dbb->f('subchapter_id');	
			$subchapterNo[$no]=$dbb->f('subchapter_no');
			$subchapterDesc[$no]=$dbb->f('subchapter_desc');
			$chapterDiscussedStatus[$no]=$dbb->f('chapter_discussed_status');
			$subchapterDiscussedStatus[$no]=$dbb->f('subchapter_discussed_status');
			$no++;
		}while($dbb->next_record());	

		?>
		<br/>
		<table>
			<tr>
				<td><h3><label>Work Achievement</strong></label><h3></td>
			</tr>
		</table>
			<?
			for ($i=0; $i<$no; $i++){
			?>
			<table>
			
				<?
					$sql_discussion = " SELECT discussed_status, 
					DATE_FORMAT(planned_date,'%d-%b-%Y') AS planned_date,
					DATE_FORMAT(completion_date,'%d-%b-%Y') AS completion_date, 
					content_discussion
					FROM pg_achievement 
					WHERE pg_thesis_id = '$thesisId'
					AND pg_proposal_id = '$proposalId'
					AND reference_no = '$referenceNo'
					AND student_matrix_no = '$user_id'
					AND pg_chapter_id = '$chapterId[$i]'
					AND pg_subchapter_id = '$subchapterId[$i]'
					AND archived_status is null";
										
					$result_sql_discussion = $dbb->query($sql_discussion); 
					$dbb->next_record();
					$discussedStatus = $dbb->f('discussed_status');
					$plannedDate = $dbb->f('planned_date');
					$completionDate = $dbb->f('completion_date');
					$contentDiscussion = $dbb->f('content_discussion');
				?>
				<tr>
					<td><strong><label>Chapter <?=romanNumerals($chapterNo[$i]);?>. <?=$chapterDesc[$i];?></label>								
					<?if ($subchapterNo[$i] != "") {?>
							<label>, Subchapter <?=romanNumerals($subchapterNo[$i]);?>. <?=$subchapterDesc[$i];?></label>
					<?}?>					
					<strong></td>
				</tr>
				<table width="31%">
				<tr>
					<td width="25%">Planned Date</td>
					<td width="1%">:</td>
					<td width="5%"><label><?=$plannedDate?></label></td>
				</tr>
				<tr>
					<td width="25%">Completion Date</td>
					<td width="1%">:</td>
					<td width="5%"><label><?=$completionDate?></label></td>
				</tr>
				</table>
				<table>
					<tr>
						<td><label>Description of Work</label></td>
					</tr>
					<tr>
						<td><?=$contentDiscussion?><br/></td>
					</tr>
				</table>
			</table>
			<?
			}
			?>	
		<table>
			<tr>
				<td><input type="button" name="btnBack" value="Back" onclick="javascript:document.location.href='defense_proposal.php';" /></td>		
			</tr>
		</table>
		</form>
	</body>
</html>




