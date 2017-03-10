<?php
    include("../../../lib/common.php");
    checkLogin(); 
    $delete = $_GET['delete'];
    if(isset($delete)) {
        $sql_delete = "DELETE FROM base_constant WHERE const_id='".$_GET['delete']."'";
        $process = $db->query($sql_delete);
        if($process) {
            tracking($_SESSION['user_id'], load_constant('DELETE'), 'DELETE MENU');
            header("refresh:1; url=email_notification_manager.php");
        }
        
    }
	
if(isset($_POST['btnUpdate']) && ($_POST['btnUpdate'] <> "")) 
{	
	$onoff = $_REQUEST['onoff'];
	while (list ($key,$val) = @each ($onoff)) 
	{
		echo "value : ".$onoff[$key];
	}
}

if(isset($_POST['btnUpdate1']) && ($_POST['btnUpdate1'] <> ""))
{
	$tick=$_POST['onoff'];
	$curdatetime = date("Y-m-d H:i:s");
	$value = $_POST['value'];
	
	while (list ($key,$val) = @each ($tick)) 
	{
		$sql = "SELECT * 
		FROM base_constant 
		WHERE const_category = 'EMAIL' 
		AND const_id = '$tick[$key]'";
		$var = $db;
		$count_total_result=$var->query($sql);
		$var->next_record();
		$valueData = $var->f('const_value');
		$a = $var->num_rows($count_total_result);
		if($a < 1)
		{
			$sql = "SELECT * FROM base_constant 
			WHERE const_value = 'Y'
			AND const_category = 'EMAIL' 
			AND const_id <> '$tick[$key]'";
			$count_total_result=$dbf->query($sql);
			$dbf->next_record();
			$valueData = $dbf->f('const_value');
			$b = $dbf->num_rows($count_total_result);
			
			do {
				$id[$i] = $var->f('const_id');
				$category[$i] = $var->f('const_category');
				$term[$i] = $var->f('const_term');
				$value[$i] = $var->f('const_value');
				$inc++; 
				$i++;     
			} while($var->next_record());


		}
		if($valueData != $value[$key])
		{
			if($value == 'N')
			{
				/*$sql1 = "UPDATE base_constant
				SET const_value = 'Y'
				WHERE id='$tick[$key]'";
				$dbg->query($sql1);*/
			}
		}
		echo "Success ".$tick[$key]."value = $value[$key]<br>"; 			
	}

	/*if (sizeof($_POST['onoff'])>0) 
	{
	}
	else {
		echo "Failure";
	}*/
}
   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="NR" />
<!-- basic -->
	<title>Base Manager</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
	<script src="../../../lib/js/jquery.min2.js"></script>
	<script src="../../../lib/js/jquery.colorbox.js"></script>
	
	<link id="bs-css" href="../../../theme/css/button.css" rel="stylesheet" />
	
    <script language="Javascript">
        function DoConfirm(message, url)
        {
        	if(confirm(message)) location.href = url;
        }
    </script>    
    <script>
  
		$(document).ready(function(){
              
               $(".add_publisher").colorbox({width:"70%", height:"60%", iframe:true,          
               onClosed:function(){ 
                window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                window.location = window.location;   //reload the page   
                                
                } }); 
			   $(".add_issn").colorbox({width:"70%", height:"60%", iframe:true,          
               onClosed:function(){ 
                window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                window.location = window.location;   //reload the page   
                                
                } }); 

                
			   $(".edit_issn").colorbox({width:"70%", height:"60%", iframe:true,          
               onClosed:function(){ 
                //window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                  window.location = window.location;   //reload the page                 
                } }); 
				$(".edit").colorbox({width:"70%", height:"60%", iframe:true,          
               onClosed:function(){ 
                //window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                  window.location = window.location;   //reload the page                 
                } }); 


          });
	</script>
</head>

<body>
<form method="post" id="form1" name="form1" action = "" enctype="multipart/form-data">
<?php
    
    //select all LENGTH(const_value) as length
    $sql = "SELECT id, publisher_name, comp_ref_no, status
	FROM ref_publisher 
	Order by publisher_name ASC";
    $var = $db;
    $count_total_result=$var->query($sql);
	$var->next_record();
	$a = $var->num_rows($count_total_result);
	    
?>

<div class="padding-5 margin-5 outer">
    <h3>Publisher Manager</h3>
    Total Result :<?=$a?><span class="float-right">
	<!--<a class = "add_publisher" href= "add_publisher.php">Add Publisher </a>--></span><br />
	<br class="clear" />
	<div class = "viewA" style="overflow:auto; height: 200px;">
    <table cellpadding="3" cellspacing="3" width="76%" class="thetable" border="1">
        <tr style="color: #000000">
            <th width="5%" align="left">No</th>
            <th width="26%" align="left">Publisher</th>
            <th width="22%" align="left">Company Registration No</th>
            <th width="10%" align="left">Status</th>
            <th width="19%" align="center">Action</th>
        </tr>
<?php
    
        $inc = 1;
		$i = 0;
		$id = array();
		$publisher_name = array();
		$comp_ref_no = array();
		$status = array();

        do {
			$id[$i] = $var->f('id');
			$publisher_name[$i] = $var->f('publisher_name');
			$comp_ref_no[$i] = $var->f('comp_ref_no');
			$status[$i] = $var->f('status');
			$inc++; 
			$i++;     
        } while($var->next_record());

		for ($i=0; $i<$a; $i++){
?>		
        <tr>
            <td align="left"><?=$i+1 ?></td>
            <td align="left"><?=$publisher_name[$i] ?></td>
            <td align="left">
			<?
			if(empty($comp_ref_no[$i]))
			{
				/*echo "<input name = \"onoff[$i]\" style = \"width:100px;\" id=\"onoff\" data-no-uniform=\"true\" value = \"None\" type=\"button\" class=\"btn btn-danger btn-sm\" disabled>";*/
			}
			else{
				echo "$comp_ref_no[$i]";
			}
			?></td>
            <td align="left" >
			<? if($status[$i] == "A") {
					
					echo "<input name = \"onoff[$i]\" style = \"width:100px;\" id=\"onoff\" data-no-uniform=\"true\" value = \"Active\" type=\"button\" class=\"btn btn-success btn-sm\" disabled>";
					//echo "<input name = \"onoff[$i]\" id=\"onoff\" data-no-uniform=\"true\" value = ".$id[$i]." checked=\"checked\" type=\"checkbox\" class=\"iphone-toggle\">";
				}
				else if ($status[$i] == "I"){
				
					echo "<input name = \"onoff[$i]\" style = \"width:100px;\" id=\"onoff\" data-no-uniform=\"true\" value = \"Inactive\" type=\"button\" class=\"btn btn-danger btn-sm\" disabled>";
					/*echo "<input name = \"onoff[$i]\" id=\"onoff\" value = ".$id[$i]." type=\"hidden\">";	
					echo "<input name = \"onoff[$i]\" id=\"onoff\" data-no-uniform=\"true\" value = ".$id[$i]." type=\"checkbox\" class=\"iphone-toggle\">";*/										
				}
				else {
					echo $value[$i];
				}
					
			?><input type= "hidden" value = "<?=$value[$i]?>" name = "value[]" id = "value" /></td>
            <td align="center"><!--<input type="submit" class="btn btn-primary btn-sm" name = "btnUpdate" id= "btnUpdate" value = "Update"/>btn btn-primary btn-sm-->
			 <button class="edit" id="editEmail" name="editAnnoun" style = "width:80px;" href="edit_publisher.php?id=<?=$id[$i]?>">Edit</button>
			<!--<a class="edit" id="editEmail" style = "width:100px;" href="edit_publisher.php?id=<?=$id[$i]?>">Edit</a>-->
			<!--<a class="editEmail" style = "width:100px;" href="edit_email1.php?id=<?=$id[$i]?>">Edit</a> btn btn-primary btn-sm--></td>
	</tr>
<?php
    	}
?>
    </table>
	</div>
	<script type="text/javascript">
	function editReport(id) 
	{
		document.location.href = "edit_publisher.php?id=" + tid;
		return true;
	}
	</script>
	<!--<input type="submit" class="btn btn-success btn-sm" name = "btnUpdate1" id= "btnUpdate1" value = "Update"/>-->
<?
    $sql1 = "SELECT issn_no, status FROM pg_publication
	Order by issn_no ASC";
    $count_total_result1=$dbf->query($sql1);
	$dbf->next_record();
	$b = $dbf->num_rows($count_total_result1);

        $inc1 = 1;
		$j = 0;
		$issn_no = array();
		$status = array();

        do {
			$issn_no[$j] = $dbf->f('issn_no');
			/*$publisher_name[$i] = $var->f('publisher_name');
			$comp_ref_no[$i] = $var->f('comp_ref_no');*/
			$status1[$j] = $dbf->f('status');
			$inc1++; 
			$j++;     
        } while($dbf->next_record());

		//for ($j=0; $i<$b; $j++){

?>
<table>
	<tr>
		<td>
		
		 <button class="add_publisher" id="editEmail" name="editAnnoun" style = "width:80px;" href="add_publisher.php">Add</button>
		</td>
	</tr>
</table>

</div>
</form>
</body>
</html>