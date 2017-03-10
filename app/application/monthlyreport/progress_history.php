<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: progress_history.php
//
// Created by: Zuraimi
// Created Date: 06 April 2015
// Modified by: Zuraimi
// Modified Date: 06 April 2015
//
//**************************************************************************************


//Read common library for page execution i.e. database connection. login validation
include("../../../lib/common.php");
//checkLogin();

session_start();
$userid=$_SESSION['user_id'];

///////////////////////////////////////////////////////////////
// used for pagination
	$page = ($_GET['page'] == 0 ? 1 : $_GET['page']);
	$perpage = 10;
	$startpoint = ($page * $perpage) - $perpage;

$varParamSend="";

foreach($_REQUEST as $key => $value)
{
	if($key!="page")
		$varParamSend.="&$key=$value";
}


if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) {
	
	$searchMonth = $_POST['reportMonth'];
	$searchYear = $_POST['reportYear'];
	$searchArchieved = $_POST['searchArchieved'];
	
	if ($searchMonth != "") 
	{
		$month = " AND a.report_month = '$searchMonth'";
	}
	else 
	{
		$month="";
	}
	
	if ($searchYear != "") 
	{
		$year = " AND a.report_year = '$searchYear'";
	}
	else 
	{
		$year="";
	}
	
	if ($searchArchieved == "ARC") 
	{
		$arc = " AND a.archived_status = '$searchArchieved'";
	}
	else 
	{
		$arc="";
	}
	
	$sql = "SELECT a.id, b.id as pdid, a.pg_thesis_id, a.pg_proposal_id, a.reference_no, 
	DATE_FORMAT(a.submit_date,'%d-%b-%Y %h:%i %p') AS submit_date,
	c.thesis_title, a.submit_status, d1.description as status_desc, b.status, d2.description as detail_status_desc, 
	a.archived_status, d3.description as archived_desc, b.pg_employee_empid, 
	DATE_FORMAT(b.responded_date,'%d-%b-%Y %h:%i %p') AS responded_date
	FROM pg_progress a
	LEFT JOIN pg_progress_detail b ON (b.pg_progress_id = a.id)
	LEFT JOIN pg_proposal c ON (c.id = a.pg_proposal_id)
	LEFT JOIN ref_proposal_status d1 ON (d1.id = a.submit_status)
	LEFT JOIN ref_proposal_status d2 ON (d2.id = b.status)
	LEFT JOIN ref_proposal_status d3 ON (d3.id = b.archived_status)
	WHERE a.student_matrix_no = '$user_id'"
	.$month." "
	.$year." "
	.$arc." 
	ORDER BY b.id DESC, a.pg_thesis_id, a.pg_proposal_id";

	$result = $db->query($sql); 
	$db->next_record();
	$row_cnt = mysql_num_rows($result);

	$id = array();
	$pdid = array();
	$thesisId= array();
	$proposalId= array();
	$referenceNo= array();	
	$thesisTitle= array();
	$submitDate= array();
	$submitStatus= array();
	$submitStatusDesc= array();						
	$employeeId= array();
	$detailStatus= array();
	$respondedDate= array();
	$detailStatusDesc= array();
	$archivedStatus= array();
	$archivedDesc= array();
	
	$no=$startpoint;
	$i = 0;
	$inc=0;
	do {						
		$id[$i]=$db->f('id');
		$pdid[$i]=$db->f('pdid');
		$thesisId[$i]=$db->f('pg_thesis_id');	
		$proposalId[$i]=$db->f('pg_proposal_id');	
		$referenceNo[$i]=$db->f('reference_no');	
		$thesisTitle[$i]=$db->f('thesis_title');
		$submitDate[$i]=$db->f('submit_date');
		$submitStatus[$i]=$db->f('submit_status');
		$submitStatusDesc[$i]=$db->f('status_desc');						
		$employeeId[$i]=$db->f('pg_employee_empid');
		$detailStatus[$i]=$db->f('detail_status');
		$respondedDate[$i]=$db->f('responded_date');
		$detailStatusDesc[$i]=$db->f('detail_status_desc');
		$archivedStatus[$i]=$db->f('archived_status');
		$archivedDesc[$i]=$db->f('archived_desc');
		$inc++;
		$i++;
	}while($db->next_record());

}
else {
	$row_cnt=0;
}
?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Monthly Progress Report History</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/rightClick.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	</head>
	<body>
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
			<fieldset>
			<table>
					<tr>							
						<td colspan="4"><strong>Please enter searching criteria below</strong></td>
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
					<td>Status</td>
					<td>:</td>
					<td colspan="4">
					<select name = "searchArchieved">
                      <option value="NULL" size="30"  selected="selected"></option>
					  <option value="ARC" size = "30">Archived</option>
                    </select></td>
				</tr>
				<tr>
					<td>Month</td>
					<td>:</td>
					<td><select name="reportMonth">
                        <?if ($reportMonth == "") $reportMonth = $_POST['reportMonth'];?>
                        <?if ($reportMonth=="") {?>
                      <option value="" selected="selected"></option>
                      <?} else {?>
                      <option value=""></option>
                      <?}?>
                        <?if ($reportMonth=="January") {?>
                      <option value="January" selected="selected">January</option>
                      <?} else {?>
                      <option value="January">January</option>
                      <?}?>
                        <?if ($reportMonth=="February") {?>
                      <option value="February" selected="selected">February</option>
                      <?} else {?>
                      <option value="February">February</option>
                      <?}?>
                        <?if ($reportMonth=="March") {?>
                      <option value="March" selected="selected">March</option>
                      <?} else {?>
                      <option value="March">March</option>
                      <?}?>
                        <?if ($reportMonth=="April") {?>
                      <option value="April" selected="selected">April</option>
                      <?} else {?>
                      <option value="April">April</option>
                      <?}?>
                        <?if ($reportMonth=="May") {?>
                      <option value="May" selected="selected">May</option>
                      <?} else {?>
                      <option value="May">May</option>
                      <?}?>
                        <?if ($reportMonth=="June") {?>
                      <option value="June" selected="selected">June</option>
                      <?} else {?>
                      <option value="June">June</option>
                      <?}?>
                        <?if ($reportMonth=="July") {?>
                      <option value="July" selected="selected">July</option>
                      <?} else {?>
                      <option value="July">July</option>
                      <?}?>
                        <?if ($reportMonth=="August") {?>
                      <option value="August" selected="selected">August</option>
                      <?} else {?>
                      <option value="August">August</option>
                      <?}?>
                        <?if ($reportMonth=="September") {?>
                      <option value="September" selected="selected">September</option>
                      <?} else {?>
                      <option value="September">September</option>
                      <?}?>
                        <?if ($reportMonth=="October") {?>
                      <option value="October" selected="selected">October</option>
                      <?} else {?>
                      <option value="October">October</option>
                      <?}?>
                        <?if ($reportMonth=="November") {?>
                      <option value="November" selected="selected">November</option>
                      <?} else {?>
                      <option value="November">November</option>
                      <?}?>
                        <?if ($reportMonth=="December") {?>
                      <option value="December" selected="selected">December</option>
                      <?} else {?>
                      <option value="December">December</option>
                      <?}?>
                      </select>
                        <select name="reportYear">
                          <?if ($reportYear == "") $reportYear = $_POST['reportYear'];?>
                          <?if ($reportYear=="") {?>
                          <option value="" selected="selected"></option>
                          <?} else {?>
                          <option value=""></option>
                          <?}?>
                          <?if ($reportYear=="2010") {?>
                          <option value="2010" selected="selected">2010</option>
                          <?} else {?>
                          <option value="2010">2010</option>
                          <?}?>
                          <?if ($reportYear=="2011") {?>
                          <option value="2011" selected="selected">2011</option>
                          <?} else {?>
                          <option value="2011">2011</option>
                          <?}?>
                          <?if ($reportYear=="2012") {?>
                          <option value="2012" selected="selected">2012</option>
                          <?} else {?>
                          <option value="2012">2012</option>
                          <?}?>
                          <?if ($reportYear=="2013") {?>
                          <option value="2013" selected="selected">2013</option>
                          <?} else {?>
                          <option value="2013">2013</option>
                          <?}?>
                          <?if ($reportYear=="2014") {?>
                          <option value="2014" selected="selected">2014</option>
                          <?} else {?>
                          <option value="2014">2014</option>
                          <?}?>
                          <?if ($reportYear=="2015") {?>
                          <option value="2015" selected="selected">2015</option>
                          <?} else {?>
                          <option value="2015">2015</option>
                          <?}?>
                          <?if ($reportYear=="2016") {?>
                          <option value="2016" selected="selected">2016</option>
                          <?} else {?>
                          <option value="2016">2016</option>
                          <?}?>
                          <?if ($reportYear=="2017") {?>
                          <option value="2017" selected="selected">2017</option>
                          <?} else {?>
                          <option value="2017">2017</option>
                          <?}?>
                          <?if ($reportYear=="2018") {?>
                          <option value="2018" selected="selected">2018</option>
                          <?} else {?>
                          <option value="2018">2018</option>
                          <?}?>
                          <?if ($reportYear=="2019") {?>
                          <option value="2019" selected="selected">2019</option>
                          <?} else {?>
                          <option value="2019">2019</option>
                          <?}?>
                          <?if ($reportYear=="2020") {?>
                          <option value="2020" selected="selected">2020</option>
                          <?} else {?>
                          <option value="2020">2020</option>
                          <?}?>
                      </select></td>
					  <td><input type="submit" name="btnSearch" value="Search" /><span style="color:#FF0000"> Note:</span> If no parameters are provided, it will search all.</td>
                  </tr>
				</table>
				<br/>				
				<table>
                <tr>
					<td colspan="4"><legend><strong>Summary List - </strong><?=$row_cnt?> record(s) found.</legend></td>
				</tr>
				</table>
				<?if ($row_cnt <= 0) {?>
					<div id = "tabledisplay" style="overflow:auto; height:80px;">
				<?}
				else {
					?>
					<div id = "tabledisplay" style="overflow:auto; height:480px;">
					<?
				}?>	
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse;" class="thetable">
				<?  
				
				?>

				<tr>
					<th width="4%"><strong>No.</strong></th>
					<th width="10%"><strong>ID</strong></th>
					<th width="10%"><strong>Thesis / Project ID</strong></th>
					<th width="30%"><strong>Thesis / Project Title</strong></th>
					<th width="10%"><strong>Reference No.</strong></th>						
					<th width="15%"><strong>Student Status</strong></th>	
					<th width="15%"><strong>Supervisor Status</strong></th>	
					<th width="6%"><strong>Record Status</strong></th>						
				</tr>
				<?
				if ($row_cnt>0) {
					for ($i=0; $i<$inc; $i++) 
					{
						// strip tags to avoid breaking any html
						$thesisTitleString[$i] = strip_tags($thesisTitle[$i]);
						
						if (strlen($thesisTitleString[$i]) > 100) 
						{
						
							$more[$i] = "<a href=\"#\" value=\".$thesisId[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitle[$i])."\">Read more..</a>";
						}
						//$string;
						$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);										
				
					?>
						<tr>
							<td align="center"><?=++$no?>.</td>
							<td><label><?=$pdid[$i]?></label></td>
							<td><label><a href="../monthlyreport/progress_detail_history.php?pid=<?=$proposalId[$i];?>"><?=$thesisId[$i]?></a></label></td>
							<td><label><?=$thesisTitleCut[$i]?></label><?=$more[$i]?></td>
							<td><label><a href="../monthlyreport/view_progress_history.php?pid=<?=$proposalId[$i];?>&tid=<?=$thesisId[$i]?>&id=<?=$id[$i]?>&pdid=<?=$pdid[$i]?>&empid=<?=$employeeId[$i]?>"><?=$referenceNo[$i]?></a></label></td>
							<td><label><?=$submitStatusDesc[$i]?><br/><?=$submitDate[$i]?><br/><br/></label></td>
							<?
							$sql2="SELECT name AS employee_name
							FROM new_employee
							WHERE empid = '$employeeId[$i]'";
							
							$dbc->query($sql2);
							$row_personal=$dbc->fetchArray();
							$employeeName=$row_personal['employee_name'];
							?>
							<td><label><?=$detailStatusDesc[$i]?><br/><?=$respondedDate[$i]?><br/><?=$employeeName?></label></td>
							<td><label><?=$archivedDesc[$i]?></label></td>
						</tr>
					<?
					}

					?>					
		  </table>
		  </div>
		  </fieldset>
			<?
				
				
				}
				else {
					?>
					<table>
						<tr>
							<td>
								<p>You don't have progress report history to view!</p>
							</td>
						</tr>
					</table>
					<?
				}
			?>
		</form>
	</body>
</html>




