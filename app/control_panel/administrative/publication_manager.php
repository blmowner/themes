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
    <h3>Publication Manager</h3>
	<fieldset>
	<legend><strong>Publisher Manager</strong></legend>
    Total Result :<?=$a?><span class="float-right">
	<a class = "add_publisher" href= "add_publisher.php">Add Publisher </a></span><br />
	<br class="clear" />
	<div class = "viewA" style="overflow:auto; height: 200px;">
    <table cellpadding="3" cellspacing="3" width="100%" class="thetable">
        <tr style="color: #000000">
            <th width="20%">Id</th>
            <th width="24%">Publisher</th>
            <th width="17%">Company Reference No</th>
            <th width="25%">Status</th>
            <th width="14%">Action</th>
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
            <td align="center"><?=$id[$i] ?></td>
            <td align="center"><?=$publisher_name[$i] ?></td>
            <td align="center">
			<?
			if(empty($comp_ref_no[$i]))
			{
				echo "<input name = \"onoff[$i]\" style = \"width:100px;\" id=\"onoff\" data-no-uniform=\"true\" value = \"None\" type=\"button\" class=\"btn btn-danger btn-sm\" disabled>";
			}
			else{
			
			}
			?></td>
            <td align="center" >
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
			<a class="edit btn btn-primary btn-sm" id="editEmail" style = "width:100px;" href="edit_publisher.php?id=<?=$id[$i]?>">Edit</a>
			<!--<a class="editEmail" style = "width:100px;" href="edit_email1.php?id=<?=$id[$i]?>">Edit</a>--></td>
	</tr>
<?php
    	}
?>
    </table>
	</div>
	<!--<input type="submit" class="btn btn-success btn-sm" name = "btnUpdate1" id= "btnUpdate1" value = "Update"/>-->
</fieldset>	
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

<fieldset>
<legend><strong>ISSN Manager</strong></legend>
<div class = "viewb" style="">

    Total Result :<?=$b?>
	<span style = "margin-left:610px;""><a class = "add_issn" href= "add_issn.php">Add ISSN No </a></span><br />
<div style="overflow:auto; width:800px; height: 200px;">
    <table cellpadding="3" cellspacing="3" width="100%" class="thetable">
        <tr style="color: #000000">
            <th>ISSN No</th>
            <th>Status</th>
			<th>Action</th>
		</tr>
<?		for ($j=0; $j<$b; $j++){ ?>
		<tr>
            <td align="center"><?=$issn_no[$j] ?></td>
            <td align="center">
			<? if($status1[$j] == "A") 
			{
				echo "<input name = \"onoff1[$j]\" style = \"width:100px;\" id=\"onoff1\" data-no-uniform=\"true\" value = \"Active\" type=\"button\" class=\"btn btn-success btn-sm\" disabled>";
					//echo "<input name = \"onoff[$i]\" id=\"onoff\" data-no-uniform=\"true\" value = ".$id[$i]." checked=\"checked\" type=\"checkbox\" class=\"iphone-toggle\">";
			}
			else if ($status1[$j] == "I"){
				
				echo "<input name = \"onoff1[$j]\" style = \"width:100px;\" id=\"onoff1\" data-no-uniform=\"true\" value = \"Inactive\" type=\"button\" class=\"btn btn-danger btn-sm\" disabled>";
			}
			?>
			</td>
			 <td align="center"><!--<input type="submit" class="btn btn-primary btn-sm" name = "btnUpdate" id= "btnUpdate" value = "Update"/>btn btn-primary btn-sm-->
			<a class="edit_issn btn btn-primary btn-sm" id="editEmail" style = "width:100px;" href="edit_issn.php?id=<?=$issn_no[$j]?>">Edit</a>
		</tr>
		<? }?>
	</table>
	</div>
</div>
</fieldset>
</div>
</form>
</body>
</html>