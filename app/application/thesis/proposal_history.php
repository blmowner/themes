<?php
//**************************************************************************************
// Post Graduate Thesis Monitoring System v1.0.0
// Program Name: proposal_history.php
//
// Created by: Fizmie
// Created Date: 24 Dec 2014
// Modified by: Zuraimi
// Modified Date: 24 Dec 2014
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

///////////////////////////////////////////////////////////////
	
$sql="SELECT a.pg_thesis_id, a.id, DATE_FORMAT(a.modify_date,'%d-%b-%Y %h:%i %p') AS theModifiedDate, a.thesis_title, 
a.thesis_type, b.description AS theThesisTypeDescription, a.introduction, a.objective, a.description, a.verified_by, 
DATE_FORMAT(a.verified_date,'%d-%b-%Y') AS theVerifiedDate, a.verified_remarks, a.verified_status, a.status, a.discussion_status, c1.description AS confirm_desc, c2.description AS status_desc,
a.archived_status, c3.description as archived_desc
FROM pg_proposal a
LEFT JOIN ref_thesis_type b ON (b.id=a.thesis_type)
LEFT JOIN ref_proposal_status c1 ON (c1.id=a.verified_status)
LEFT JOIN ref_proposal_status c2 ON (c2.id=a.status)
LEFT JOIN ref_proposal_status c3 ON (c3.id=a.archived_status)
LEFT JOIN pg_thesis d ON (d.id = a.pg_thesis_id)
WHERE d.student_matrix_no = '$userid'
ORDER BY a.id desc
LIMIT $startpoint, $perpage";
		
		
?>

	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Thesis History</title>
		<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
		<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
		<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
		<script src="../../../lib/js/jquery.min2.js"></script>
		<script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
	</head>
<style type="text/css">

/***************** TABLE STYLE ********************/
.thetable {
    border: 1px solid #BFBFBF;
    border-collapse: collapse;
    border-spacing: 0px;    
    max-width: 100%;
    position: relative;
}

.thetable td {
    border: 1px solid #BFBFBF;
    border-collapse: collapse;
    padding-top: 25px;
    padding-bottom: 17px;
}


.thetable th {
    /* for Internet Explorer 5.5 - 7 */
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#FFFFFF, endColorstr=#DBEBFF);
    /* for Internet Explorer 8 */
    -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#FFFFFF, endColorstr=#DBEBFF)";
    /* for Webkit (Safari, Google Chrome, etc) */
    background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#DBEBFF));
    /* for mozilla */
	   background:-moz-linear-gradient(center top, #FFFFFF, #DBEBFF) scroll 0 0 transparent;
}

.first-row {
    background:#EEEEEE;
}
.second-row {
    background:#DBD9D9;
}
/***************** END TABLE STYLE ********************/


/***************** TABLE2 STYLE ********************/
.thetable2 {
    border: 1px solid #BFBFBF;
    border-collapse: collapse;
    border-spacing: 0px;    
}

.thetable2 td {
    border: 1px solid #BFBFBF;
    border-collapse: collapse;
}

.thetable2 th {
    /* for Internet Explorer 5.5 - 7 */
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#FFFFFF, endColorstr=#837ECD);
    /* for Internet Explorer 8 */
    -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#FFFFFF, endColorstr=#DBEBFF)";
    /* for Webkit (Safari, Google Chrome, etc) */
    background:-webkit-gradient(linear, left top, left bottom, from(#FFFFFF), to(#837ECD));
    /* for mozilla */
	   background:-moz-linear-gradient(center top, #FFFFFF, #837ECD) scroll 0 0 transparent;
}



</style>
	<script>
$(function() {
    var $table = $('#TestTable');
    var $thead = $table.find('thead');
    var $tbody = $table.find('tbody');
    var $tfoot = $table.find('tfoot');
    
    if (!$thead.length)
        $thead = $('<thead />').prependTo($table);
    if (!$tbody.length)
        $tbody = $('<tbody />').insertAfter($thead);
    if (!$tfoot.length)
        $tfoot = $('<tfoot />').insertAfter($tbody);

    var $hrow = $('<tr />').appendTo($thead);
    var $frow = $('<tr />').appendTo($tfoot);
    
    for (var row = 0; row < 200; row++)
        $tbody.append($('<tr />'));
    
    var $brow = $tbody.find('tr');
    
    for (var col = 0; col < 10; col++) {
        $('<th />').html('Hdr'+col)
            .appendTo($hrow);
        $('<td />').html('Val'+col)
            .appendTo($brow);
        $('<th />').html('Ftr'+col)
            .appendTo($frow);
    }
    
    $brow.each(function(ix, el) {
        $(el).children().first().html('R:'+ix);
    });
    
});
	</script>
	<body>
        
        <!-- <legend><strong>SUMMARY LIST</strong></legend> -->
		<form id="form1" name="form1" method="post" enctype="multipart/form-data">
		<div class="scrollable-table-wrapper">
			
				<!--<legend style="margin-bottom:40px;"><strong>SUMMARY LIST</strong></legend>-->
				<table width="100%" border="1" cellpadding="3" cellspacing="1" style="border-collapse:collapse; margin: 0;" class="thetable">
				<?  
					$result = $db->query($sql); 
					//echo "sql ".$sql;
					//var_dump($db);
					$db->next_record();
					$row_cnt = mysql_num_rows($result);

					$no=$startpoint;
					if ($row_cnt>0) {?>
                    
                    <thead style="position:fixed; top: 0; left: 0; width: 100%;">
					<tr>
						<th width="24" align="center"><strong>No.</strong></th>
						<th width="120"><strong>Thesis/Project ID</strong></th>
						<th width="94"><strong>Proposal ID</strong></th>						
						<th width="263"><strong>Thesis/Project Title</strong></th>
						<th width="130"><strong>Modification Date</strong></th>
						<th width="120"><strong>Faculty Status</strong></th>	
						<th width="120"><strong>Senate Status</strong></th>	
						<th width="120"><strong>Archive Status</strong></th>						
					</tr>
					</thead>
					<?
					$i =0;
					$inc = 0;
					$pgThesisId=array();
					$id=array();	
					$modifiedDate=array();
					$thesisTitle=array();
					$confirmDesc=array();	
					$statusDesc=array();						
					$status=array();
					$archivedStatus=array();
					$archivedDesc=array();

					do {						
						$pgThesisId[$i]=$db->f('pg_thesis_id');	
						$id[$i]=$db->f('id');	
						$modifiedDate[$i]=$db->f('theModifiedDate');
						$thesisTitle[$i]=$db->f('thesis_title');
						$confirmDesc[$i]=$db->f('confirm_desc');	
						$statusDesc[$i]=$db->f('status_desc');						
						$status[$i]=$db->f('status');
						$archivedStatus[$i]=$db->f('archived_status');
						$archivedDesc[$i]=$db->f('archived_desc');;// $db->f('archived_status'); '<img src="../../../theme/images/accept.png">'
						$inc++;
						$i++;
						}while($db->next_record());
					?>
					<?
					for ($i=0; $i<$inc; $i++) 
					{
								// strip tags to avoid breaking any html
								$thesisTitleString[$i] = strip_tags($thesisTitle[$i]);
								
								if (strlen($thesisTitleString[$i]) > 100) 
								{
									$more[$i] = "<a href=\"#\" value=\".$thesisId[$i].\" title=\"".preg_replace('/"/',"'",$thesisTitle[$i])."\"> . . Read more</a>";
								}
								//$string;
								$thesisTitleCut[$i] = substr($thesisTitleString[$i], 0, 100);
								
						?>
						<tbody>
						<tr>
							<td style="width:24px;" align="center"><?=$i+1?>.</td>
							<td style="width:112.5px;" class="thesis_id"><label><?=$pgThesisId[$i]?></label></td>
							<td style="width:5px;"><label><a href="../thesis/detail_proposal_history_student.php?pid=<?=$id[$i];?>"><?=$id[$i]?></a></label></td>						
							<td style="width:218px;"><label><?=$thesisTitleCut[$i]?></label><?=$more[$i]?></td>
							<td style="width:118px;"><label><?=$modifiedDate[$i]?></label></td>
							<td style="width:100px;"><label><?=$confirmDesc[$i]?></label><div align="center"></div></td>
							<td style="width:101px;"><label><?=$statusDesc[$i]?></label></td>

							<? if($archivedDesc[$i] == 'Archived')
							   $a = 'asdsadsadsa';
						       {
							     echo '<td><label><center><img src="../../../theme/images/accept.png"></label></center></td>';
							    // echo '<td><label><center>'.$a.'</label></center></td>';
						       }

/*
						       foreach ($archivedDesc as $value) {
						       	 

						       	 echo $value;

						       }
*/
						     ?>
							
						</tr>



						
				<tr><td>1</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td></tr>
               	<tr><td>2</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td></tr>
               	<tr><td>3</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td></tr>
               	<tr><td>4</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td><td>asdasdsad</td><td>asdsad</td></tr>

               	
						</tbody>
						<tfoot></tfoot>
					<?
					
					}

					?>					
		  </table>
		  
			<?
				$count_total_result ="SELECT count(*) as total from pg_proposal a, pg_thesis b 
				WHERE a.pg_thesis_id = b.id AND b.student_matrix_no = '$userid'";
				$db->query($count_total_result);
				$db->next_record();
				$a = $db->f('total');
				$db->free();
			
				//This is the actual usage of function, It prints the paging links
				doPages($perpage, 'proposal_history.php', $varParamSend, $a);					
				
				}
				else {
					?>
					<table>
						<tr>
							<td>
								<p><span style="color:#FF0000">Note: </span>You don't have thesis history to view! Please click <strong>Thesis </strong> tab to create a new one and submit. </p>
							</td>
						</tr>
					</table>
					<?
				}
			?>
			</div>
		</form>
	</body>
</html>




