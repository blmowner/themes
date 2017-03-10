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
              
               $(".add_email").colorbox({width:"70%", height:"60%", iframe:true,          
               onClosed:function(){ 
                window.location.reload(true); //uncomment this line if you want to refresh the page when child close
                window.location = window.location;   //reload the page   
                                
                } }); 
                
			   $(".btn").colorbox({width:"35%", height:"32%", iframe:true,          
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
	
    
    //demo
    $sql = "SELECT const_value, LENGTH(const_value) as length, const_term, const_id, const_category, const_description 
	FROM base_constant
	Order by const_category ASC";
    $var = $db;
    $count_total_result=$var->query($sql);
	$var->next_record();
	$a = $var->num_rows($count_total_result);
	    
?>

<div class="padding-5 margin-5 outer">
    <h3>Demo Base Constant</h3>
    Total Result Demo:<?=$a?><span class="float-right">
	<!--<a class = "add_email" href= "add_email_notification.php">Add Email Notification Status</a>--></span><br /><br class="clear" />
    <table width="48%" class="thetable">
        <tr style="color: #000000">
            <th width="8%">Constant Id</th>
            <th width="12%">Constant Category</th>
            <th width="20%" align="left">Constant Term-Demo</th>
            <th width="18%" align="left">Constant Term-Live</th>
        </tr>
<?php
    
        $inc = 1;
		$i = 0;
		$id = array();
		$category = array();
		$term = array();
		$value = array();

        do {
			$id[$i] = $var->f('const_id');
			$category[$i] = $var->f('const_category');
			$term[$i] = $var->f('const_term');
			$value[$i] = $var->f('const_value');
			$description[$i] = $var->f('const_description');
			$inc++; 
			$i++;     
        } while($var->next_record());

		for ($i=0; $i<$a; $i++){
		
		    $sqlLive = "SELECT const_value, LENGTH(const_value) as length, const_term, const_id, const_category, const_description 
			FROM base_constant 
			WHERE const_term = '$term[$i]'
			Order by const_category ASC";
			
			$count_total_sqlLive=$dbLive->query($sqlLive);
			$dbLive->next_record();
			   			
			$idLive = $dbLive->f('const_id');
			$categoryLive = $dbLive->f('const_category');
			$termLive = $dbLive->f('const_term');
			$valueLive = $dbLive->f('const_value');
			$descriptionLive = $dbLive->f('const_description');
			
			if (empty($termLive)) {
				$color = "red";
			} else {
				$color = "black";
			}
?>		
        <tr>
            <td align="center"><?=$id[$i] ?></td>
            <td align="center"><?=$category[$i] ?></td>
            <td align="left"><span style="color:<?=$color?>"><?=$term[$i] ?></span></td>
            <td align="left" ><?=$termLive?></td>
      </tr>
<?php
    	}
?>
    </table>

</div>
</form>
</body>
</html>