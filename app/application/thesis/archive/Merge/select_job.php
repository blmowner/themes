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
		//if category is choosen
		if ($id == "") 
		{
			$tmpSearchCat = " WHERE JobArea like '%%'";
		}
		else 
		{
			$tmpSearchCat = " WHERE SUBSTRING(JobArea,1,2) =SUBSTRING('$id',1,2) AND JobArea <> '$id'";
		}
		
		if ($searchjob == "") 
		{
			$tmpSearchjob = "";
		}
		else 
		{
			$tmpSearchjob = " AND area like '%$searchjob%' OR JobArea like '%$searchjob%'";
		}


       $sql = "SELECT JobArea, area 
		from job_list_category" 
		.$tmpSearchCat." "
		.$tmpSearchjob." 
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
    </style>
</head>

<body>
<h3>Search Job Area </h3>
<form id="frmPopup" action="" onsubmit="" method="post" name="frmPopup" enctype="multipart/form-data">
  <p>
    <?
		//$area1 = $_REQUEST['field']; 		
  		$sqlif = "SELECT area FROM job_list_category";
		$resultif= $dbc->query($sqlif);
		$dbc->next_record();
		$area = $dbc->f('area');

	?>
  <table width="559">
		<tr>
			<td width="190"><span class="style2">Job Area Category </span></td>
			<td width="234"><select name="job_id" id ="job_id" style="font-family:verdana; font-size: 11.5px;">
			  <option value="">--Please Select--</option>
			  <?php 
				$parent = '000';
				$sqlsearch = "SELECT JobArea,area FROM job_list_category WHERE SUBSTRING(JobArea,3,5) = '$parent' ORDER BY area ASC ";
				$resultsearch = $dbk->query($sqlsearch);
		
				while($dbk->next_record())
				{
					$desc = $dbk->f('area');
					$jobid = $dbk->f('JobArea');
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
			  <? 
					}	
				}
			?>
			</select></td>
		</tr>
		<tr>
			<td><span class="style2"> Sub Category ID/Description </span></td>
			<td><input type="text" size="39" name="search_box" value = "<?=$search_box;?>"  style="font-family:verdana; font-size: 11.5px;"/></td>
			<td width="59"><input type="submit" name="btnSearch" id = "btnSearch" value="Search" />			</td>
		</tr>
  </table>
  <table cellpadding="3" cellspacing="3" width="83%" class="thetable">
    <tr>
      <th width="17%" style="display:none;" >&nbsp;</th>
      <th width="22%"><div align="left"><?php echo load_lang('sub_category_id'); ?></div></th>
        <th width="61%"><?php echo load_lang('description'); ?></th>
    </tr>
<?
	for ($i=0; $i<$inc; $i++) 
	{
?>
    <tr class="<?php echo $color; ?>">
      <td style="display:none;" align="center"><?php echo $a[$i] ?></td>
      <td align="center"><div align="left"><?php echo $a[$i] ?></div></td>
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
			$tmpSearchCat = " WHERE JobArea like '%%'";
			}
			else 
			{
				$tmpSearchCat = " WHERE SUBSTRING(JobArea,1,2) =SUBSTRING('$id',1,2) AND JobArea <> '$id'";
			}
			
			if ($searchjob == "") 
			{
				$tmpSearchjob = "";
			}
			else 
			{
				$tmpSearchjob = " AND area like '%$searchjob%' OR JobArea like '%$searchjob%'";
			}
		
		
			$count_total_result = "SELECT JobArea, area 
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