<?php 
    include("../../../lib/common.php"); 
    checkLogin();
	session_start();

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
/*if(isset($_POST['btnSearchByCategory']) && ($_POST['btnSearchByCategory'] <> "")) 
{

		//normal search
				$searchjob = $_REQUEST['search_box'];
		
		if ($searchjob == "") 
		{
			$tmpSearchjob = "";
		}
		else 
		{
			$tmpSearchjob = " WHERE area like '%$searchjob%' OR JobArea like '%$searchjob%'";
		}

      	$sql = "SELECT JobArea, area 
		from job_list_category" 
		.$tmpSearchjob." 
		LIMIT  $startpoint,$perpage";
		
		$result = $dbc->query($sql);
		$dbc->next_record();
		$row_cnt = mysql_num_rows($result); 
		$a = array();
		$b = array();
		
		if($row_cnt > 0) 
		{ //continue from the if isset($_POST['search']
			$inc = 0;
			$i = 0;
			do{
				if($inc % 2) $color="first-row"; else $color="second-row";
				
				$color;
				$a[$inc] = $dbc->f('JobArea');
				$b[$inc] = $dbc->f('area');
				$inc++;
				$i++;
				} while($dbc->next_record());
		}
		
		     //echo $inc-1 ." result(s) found."; // show number of result found using search func

}*/

if(isset($_REQUEST['btnSearch']) && ($_REQUEST['btnSearch'] <> "")) 
{
		$id = $_REQUEST['job_id'];
		$searchjob = $_POST['search_box'];
		//$id2 = explode(" , ", $_REQUEST['job_id']);	
		
		$lastDigit = '000';
		//exit();
		//if category is choosen
		if ($id == "") 
		{
			$tmpSearchCat = " WHERE SUBSTRING(JobArea,3,5)"; // JobArea like '%%' AND
		}
		else 
		{
			$tmpSearchCat = " WHERE SUBSTRING(selection,1,1) =SUBSTRING('$id',1,1) AND selection <> '$id'";
		}
		
		if ($searchjob == "") 
		{
			$tmpSearchjob = "";
		}
		else 
		{
			$tmpSearchjob = " AND area like '%$searchjob%' OR JobArea like '%$searchjob%'";
		}


       $sql = "SELECT JobArea, area,selection 
		from job_list_category" 
		.$tmpSearchCat." "
		.$tmpSearchjob."
		ORDER BY JobArea ASC 
		LIMIT  $startpoint,$perpage";
		
		$result = $dbc->query($sql);
		$dbc->next_record();
		$row_cnt = mysql_num_rows($result); 
		$a = array();
		$b = array();
		
		if($row_cnt > 0) 
		{ 
			$inc = 0;
			$i = 0;
			do{
				if($inc % 2) $color="first-row"; else $color="second-row";
				
				$color;
				$a[$i] = $dbc->f('JobArea');
				$b[$i] = $dbc->f('area');
				$inc++;
				$i++;
				} while($dbc->next_record());
		}
		
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Select User</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <script>
        function closeThePop(data,data2,data3,data4) 
		{
            parent.$.fn.colorbox.close(); //call the colorbox's close function
			
			if (data2 === undefined) {
				parent.$.fn.getParameterValue(data); // pass the parameter to parent
			} else {
				parent.$.fn.getParameterValue2(data, data2, data3, data4);
				//parent.$fn.colorbox.returnFocus();
			}
            //parent.$.fn.getParameterValue(data); // pass the parameter to parent
        }
    </script>
	
	<script language="javascript">
	function selValue()
	{
		var val = '';
		for(i=0;i<frmPopup.hdnLine.value;i++)
		{
			if(eval("frmPopup.Chk"+i+".checked")==true)
			{
				val = val + eval("frmPopup.Chk"+i+".value") + ' , ';
			}
		}
		
		//window.opener.document.getElementById("txtSel").value = val;
		//window.close();
		
		parent.$.fn.getParameterValue(val); // pass the parameter to parent
		parent.$.fn.colorbox.close(); //call the colorbox's close function
		
		return false;
	}
</script>
    <style type="text/css">
<!--
.style2 {color: #000000}
-->


.close-btn { 
    border: 2px solid #c2c2c2;
    position: relative;
    padding: 1px 5px;
    bottom: 38px;
    background-color: #605F61;
    left: 752px; 
    border-radius: 20px;
}

.close-btn a {
    font-size: 15px;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

    </style>
</head>

<body>
<h3 style="margin-top:10px; margin-left:35px;">Search Thesis Proposal Area </h3>
<form id="frmPopup" action="" onsubmit="" method="post" name="frmPopup" enctype="multipart/form-data">
  <p>
    <?
		//$area1 = $_REQUEST['field']; 		
  		$sqlif = "SELECT area FROM job_list_category";
		$resultif= $dbc->query($sqlif);
		$dbc->next_record();
		$area = $dbc->f('area');

	?>
  
	<span class="close-btn"><a href="#" onclick="javascript: parent.$.fn.colorbox.close();">X</a></span>
	
  <table width="80%" style="margin-top:10px; margin-left:55px;">
		<tr>
			<td width="35%">Proposal Area Category</td>
			<td width="45%"><select name="job_id" id ="job_id" style="font-family:verdana; font-size: 11.0px;">
			  <option value="">--Please Select--</option>
			  <?php 
				$parent = '00';
				$sqlsearch = "SELECT JobArea,area,selection FROM job_list_category WHERE SUBSTRING(selection,2,3) = '$parent' ORDER BY JobArea ASC ";
				$resultsearch = $dbk->query($sqlsearch);
		
				while($dbk->next_record())
				{
					$desc = $dbk->f('area');
					$jobid = $dbk->f('selection');
					if(isset($_POST['btnSearch']) && ($_POST['btnSearch'] <> "")) 
					{ 				
		
					?>
			  <option value="<?=$jobid?>"<?php if($_POST['job_id'] == $jobid): echo "selected='selected'"; endif;?> >
			  <?= $desc ?>
			  </option>
			  <? 
					}
					else
					{
					?>
			  <option value="<?=$jobid?>">
				<?=$desc?>
			  </option>
			  <? //<?php echo load_lang('sub_category_id'); <?php echo load_lang('description'); 
					}	
				}
			?>
			</select></td>
		</tr>
		<tr>
			<td><span class="style2"> Sub Category ID/Description </span></td>
			<td><input type="text" size="39" name="search_box" value = "<?=$search_box;?>"  style="font-family:verdana; font-size: 11.0px;"/></td>
			<td width="59"><input type="submit" name="btnSearch" id = "btnSearch" value="Search" />			</td>
		</tr>
  </table>
  <table cellpadding="3" cellspacing="3" width="85%" class="thetable" style="margin-top:10px; margin-left:35px;">
    <tr>
      
      <th width="22%"><?php echo load_lang('sub_category_id')?></th>
        <th width="58%"><?php echo load_lang('description');?></th>
		
    </tr>
<?
	for ($i=0; $i<$inc; $i++) 
	{
?>
    <tr class="<?php echo $color; ?>">
      <td style="display:none;" align="center"><?php echo $a[$i] ?></td>
      <td align="center"><?php echo $a[$i] ?></td>
	  <td><a href="#" onclick="closeThePop('<?php echo $b[$i]; ?>','<?=$_GET['field'];?>','<?php echo $a[$i]; ?>','<?=$_GET['field2'];?>');"><?php echo $b[$i]; ?></a></td>
    </tr>    
    <?php			
     
	 } 
	      
       
    ?>
 </table>
 
 
  <input name="hdnLine" type="hidden" value="<?php echo $i;?>">
  <p>
   <?php
		if(isset($_REQUEST['btnSearch']) && ($_REQUEST['btnSearch'] <> "")) 
		{
			$id = $_REQUEST['job_id'];
			$searchcat = $_POST['category'];
			$searchjob = $_REQUEST['search_box'];
			//without limit
			if ($id == "") 
			{
			$tmpSearchCat = " WHERE SUBSTRING(JobArea,3,5)";
			}
			else 
			{
				$tmpSearchCat = " WHERE SUBSTRING(selection,1,1) =SUBSTRING('$id',1,1) AND selection <> '$id'";
			}
			
			if ($searchjob == "") 
			{
				$tmpSearchjob = "";
			}
			else 
			{
				$tmpSearchjob = " AND area like '%$searchjob%' OR JobArea like '%$searchjob%'";
			}
		
		
			$count_total_result = "SELECT JobArea, area,selection 
			from job_list_category" 
			.$tmpSearchCat." "
			.$tmpSearchjob." ";
			$dbc->query($count_total_result);
			$total = $dbc->num_rows($count_total_result);
			$dbc->free();

		}
		if($_REQUEST['btnSearch'] <> "") 
		{
			//echo $total;
			doPages($perpage, 'select_job.php', $varParamSend, $total);
        }
 ?>
</form></p>
<p>&nbsp; </p>
</body>
</html>