<? 

include("../../../lib/common.php"); 

$sqlProposal = "SELECT a.id, a.viva_status, d.description AS vivaStatDesc, a.pg_calendar_id, a.student_matrix_no, 
a.pg_thesis_id, defense_date, DATE_FORMAT(f.defense_date,'%d-%b-%Y') AS viva_date, g.id AS pg_proposal_id,
DATE_FORMAT(f.defense_stime,'%h:%i%p') AS viva_stime, DATE_FORMAT(f.defense_etime,'%h:%i%p') AS viva_etime, 
f.venue, g.thesis_title
FROM pg_viva a
LEFT JOIN pg_evaluation_viva b ON (b.pg_viva_id = a.id)
LEFT JOIN pg_amendment c ON (c.pg_viva_id = a.id)
LEFT JOIN ref_recommendation d ON (d.id = a.viva_status)
LEFT JOIN pg_calendar f ON (f.id = a.pg_calendar_id)
LEFT JOIN pg_proposal g ON (g.pg_thesis_id = a.pg_thesis_id)
WHERE a.status = 'ARC'
AND a.submit_status = 'CON'
AND c.status <> 'ARC'
AND ((a.viva_status = 'PMI' AND c.confirm_status = 'CON2') 
OR (a.viva_status = 'PMA' AND c.confirm_status = 'CON2') 
OR (a.viva_status = 'PAS' AND c.status = 'ARC1'))
-- OR (a.viva_status = 'FAI' AND a.appeal_result = 'DIS')
-- OR (a.viva_status = 'PMR' AND c.confirm_status = 'CON2')
-- OR (a.viva_status = 'FAI' AND a.appeal_result IS NULL AND CURDATE() > b.end_appeal_date ))
AND b.final_result IN ('PAS', 'PMI', 'PMA')
AND g.archived_status IS NULL
-- AND a.id NOT IN (SELECT pg_viva_id FROM pg_senate)";

$result_sqlProposal = $db->query($sqlProposal);

//$row_cnt = mysql_num_rows($result_sqlProposal);


	$i = 0;
	$no = 1;
	while($db->next_record())
	{
		$studentMatrixNo[$i] = $db->f("student_matrix_no");
		$vivaStatDesc[$i] = $db->f("vivaStatDesc");
		$thesisDesc[$i] = $db->f("thesis_desc");
		$thesisId[$i] = $db->f("pg_thesis_id");
		$thesisTitle[$i] = $db->f("thesis_title");
		$proposalId[$i] = $db->f("pg_proposal_id");

		
		
		$sql3 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNo[$i]'";		
		if (substr($studentMatrixNo,0,2) != '07') { 
			$dbConnStudent= $dbc; 
		} 
		else { 
			$dbConnStudent=$dbc1; 
		}
		$result3 = $dbConnStudent->query($sql3); 
		$dbConnStudent->next_record();
		$studentName[$i]=$dbConnStudent->f('name');
		//$senateRevision = '<input type="checkbox" name="checkbox" value="checkbox" checked="checked" />Approved';
		$senate = array();
		$j = 1;
		$k = 0;

		$sql_supervisor = " SELECT a.pg_employee_empid, a.ref_supervisor_type_id, d.description as supervisor_type, 
		DATE_FORMAT(a.acceptance_date,'%d-%b-%Y') as acceptance_date, h.description as role_status_desc
		FROM pg_supervisor a 
		LEFT JOIN ref_supervisor_type d ON (d.id = a.ref_supervisor_type_id) 
		LEFT JOIN pg_thesis f ON (f.student_matrix_no = a.pg_student_matrix_no)
		LEFT JOIN pg_proposal g ON (g.pg_thesis_id = f.id)
		LEFT JOIN ref_role_status h ON (h.id = a.role_status)
		WHERE a.pg_student_matrix_no='$studentMatrixNo[$i]'
		AND g.pg_thesis_id = '$thesisId[$i]'
		AND g.id = '$proposalId[$i]'
		AND a.acceptance_status = 'ACC'
		AND a.ref_supervisor_type_id in ('SV','CS','XS')
		AND g.verified_status in ('APP','AWC')
		AND g.status in ('APP','APC')
		AND g.archived_status IS NULL
		AND a.status = 'A'
		ORDER BY d.seq, a.ref_supervisor_type_id";
		
		$result_sql_supervisor = $db_klas2->query($sql_supervisor); 
		$row_cnt_supervisor = mysql_num_rows($result_sql_supervisor);	
		while($db_klas2->next_record())
		{					
			$empid[$k] = $db_klas2->f("pg_employee_empid");
			$spType[$k] = $db_klas2->f("supervisor_type");

			$sqlname = "Select name from new_employee where empid = '$empid'";
			$dbc2 = $dbc;		
			$result_sqlname = $dbc2->query($sqlname); 
			$dbc2->next_record();
			$spName = $dbc2->f("name");

			$spv = $j . ". " . $spName . " - " .$spType;

			$j++;
			$k++;
			
		}
		$TT = "Id: ". $thesisId	. "<br/>" . "Title: " . $thesisTitle;
						
		$no++;
		$i++;
	}
	$no2 = 1;
	$no3 = 1;

?>

<style>
#logo{

	float: left;
}
#internal {
	float right;
}
#header {
	width:100%;
}
</style>

<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
<div id = "header">
	<div id="logo"> <img src="../../../theme/images/msuLogo.gif" alt=""></div>
		<center id = "title">List of Thesis for Senate Endorsement</center>
	<div id="internal"><span id="to">For Internal Use</span></div>
</div>
<br />
<br />
<br />
<br />
<br />
<div>
	<table class = 'thetable'>
		<tr>
			<th>No</th>
			<th>Student Name</th>
			<th>Matrix No</th>
			<th>Thesis Title/Thesis ID</th>
			<th>List of Supervisor</th>
			<th>Result</th>
			<th>Senate Remark</th>
		</tr>
		<tr>
			<?
				for($b = 0; $b<$i; $b++) { 
				$sql3 = "SELECT name
				FROM student
				WHERE matrix_no = '$studentMatrixNo[$b]'";		
				if (substr($studentMatrixNo,0,2) != '07') { 
					$dbConnStudent= $dbc; 
				} 
				else { 
					$dbConnStudent=$dbc1; 
				}
				$result3 = $dbConnStudent->query($sql3); 
				$dbConnStudent->next_record();
				$studentName=$dbConnStudent->f('name');
			?>
			<td><?=$no2?></td>
			<td><?=$studentName?></td>
			<td><?=$studentMatrixNo[$b]?></td>
			<td>Id: <?=$thesisId[$b]?><br /> Title: <?=$thesisTitle[$b]?></td>
			<td>
				<?
				for($c = 0; $c<$k; $c++) { 

					$sqlname = "Select name from new_employee where empid = '$empid'";
					$dbc2 = $dbc;		
					$result_sqlname = $dbc2->query($sqlname); 
					$dbc2->next_record();
					$spName = $dbc2->f("name");

					echo $no3 . ". " . $spName . " - " .$spType[$c]. "<br/>";
					$no3++;
				}
				?>
			</td>
			<td><?=$vivaStatDesc[$b]?></td>
			<td></td>
			<?		$no2++;
				} ?>
		</tr>
	</table>
</div>