<?php
    include("../../../lib/common.php");
    checkLogin();
 
function runnum2($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,3,11)) AS run_id FROM $tblname";
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
  if($_POST['close'] <> "") {
	   echo "<script>parent.$.fn.colorbox.close();</script>";
  }   
    if($_POST['submit'] <> "") {
	
        $msg = array();
		$curdatetime = date("Y-m-d H:i:s");
		$description = $_REQUEST['description'];
		$sequence = $_REQUEST['seq'];
		$status = $_REQUEST['status'];
		
		$veId = "VE".runnum2('id','ref_overall_style_viva');
        if(empty($_POST['description'])) $msg[] = "<div class=\"error\"><span>Please insert Description</span></div>";
		if(empty($_POST['seq'])) $msg[] = "<div class=\"error\"><span>Please insert Sequence</span></div>";

        //if(empty($_POST['const_value'])) $msg[] = "<div class=\"error\"><span>Please insert constant value</span></div>";
        
        if(empty($msg)) 
		{
				$sql2 = "INSERT INTO ref_overall_style_viva (`id`,`description` , seq,`insert_by`, insert_date, status) 
				VALUES ('$veId','$description', '$sequence','$user_id', '$curdatetime', '$status')";
				$db->query($sql2);

                                
                $msg[] = "<div class=\"success\"><span>Question successfully added!</span></div>";
                //header("refresh:2; url=menu_manager.php");
         }
            
        
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title>Add Menu</title>
    <link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <style type="text/css">
<!--
.style1 {color: #FF0000}
-->
    </style>
</head>

<body>
    <div class="padding-5 margin-5 outer">
        <?php
        if(!empty($msg)) {
            foreach($msg as $err) {
                echo $err;
            }
        }
        ?>
       
	<form method="post" id="form-set">
	 <fieldset><legend><strong>ADD SECTION A: OVERALL STYLE AND ORGANIZATION</strong></legend>

        <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
		<table>
			<tr>
			  <td width="223"><span class="style1">*</span> Description </td>
			  <td width="493"><textarea name="description" cols="50"></textarea></td>
			</tr>
			<tr>
				<td><span class="style1">*</span>Sequence</td>
			    <td><input type="text" name="seq" size="40" value="" />
				</td>
			</tr>
			<tr>
		
				<td>Status</td>
		      <td><select name="status" id="status">
				<option value="A" selected="selected">Active</option>
				<option value="I">Inactive</option>
				</select></td>
			
			</tr>
		</table>
				<label>&nbsp;</label><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="" />
				<input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="" /><!--fancy-button-grey fancy-button-green-->
		</fieldset>
<?
	$sqlamend = "SELECT id,description, seq from ref_overall_style_viva";
	
	$result_sqlamend = $dbb->query($sqlamend); 
	$dbb->next_record();
	$row_cnt5 = mysql_num_rows($result_sqlamend);
	$i= 0;
	$inc= 0;
	$id = array();
	$description = array();
	$sequence = array();
	
	do{
		$id[$i] = $dbb->f('id');
		$description[$i] = $dbb->f('description');
		$sequence[$i] = $dbb->f('seq');
		$i++;
		$inc++;

	}while($dbb->next_record());
	
?>

	<table class="thetable" width="80%" border="1">
		<tr>
			<th>No</th>
			<th>Description</th>
			<th>Sequence</th>
			<th>Action</th>
		</tr>
		<? for ($i=0; $i<$inc; $i++){ ?>
		<tr>
		
			<td><?=$i+1?></td>
			<td><?=$description[$i]?></td>
			<td><?=$sequence[$i]?></td>
			<td>Test</td>
		
		</tr>
		<? }?>
	</table>
	</form>
		
    </div>
</body>
</html>