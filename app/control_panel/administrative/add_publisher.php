<?php
    include("../../../lib/common.php");
    checkLogin();
 
function runnum2($column_name, $tblname) 
{ 
    global $db_klas2;
    
    $run_start = "001";
    
    $sql_slct_max = "SELECT MAX(SUBSTR($column_name,2,11)) AS run_id FROM $tblname";
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
    
    if($_POST['submit'] <> "") {
	
        $msg = array();
		$curdatetime = date("Y-m-d H:i:s");
		$pName = $_REQUEST['pub_name'];
		$crf = $_REQUEST['crf'];
		$status = $_REQUEST['status'];
		
		$publisherId = "P".runnum2('id','ref_publisher');
        if(empty($_POST['pub_name'])) $msg[] = "<div class=\"error\"><span>Please insert Name</span></div>";
        //if(empty($_POST['const_value'])) $msg[] = "<div class=\"error\"><span>Please insert constant value</span></div>";
        
        if(empty($msg)) 
		{
				$sql2 = "INSERT INTO ref_publisher (`id`,`publisher_name`,`comp_ref_no`,`insert_by`, insert_date, status) 
				VALUES ('$publisherId','$pName','$crf','$user_id', '$curdatetime', '$status')";
				$db->query($sql2);

                                
                $msg[] = "<div class=\"success\"><span>Publisher successfully added!</span></div>";
                //header("refresh:2; url=menu_manager.php");
                echo "<script>parent.$.fn.colorbox.close();</script>";
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
        <h3>Add Publsiher </h3>
        <div class="info"><span><?php echo load_lang('compulsary'); ?></span></div><br />
        <form method="post" id="form-set">
		<table>
			<tr>
			  <td width="223">* Publisher Name</td>
			  <td width="493"><input type="text" name="pub_name" size="40" value="" /></td>
			</tr>
			<tr>
				<td>Company Reference No</td>
			    <td><input type="text" name="crf" size="40" value="" />
				</td>
			</tr>
			<tr>
		
				<td>Status</td>
			    <td><select name="status" id="status">
				<option value="A" selected="selected">Yes</option>
				<option value="I">No</option>
				</select>Status: Active or Not Active
				</td>
			
			</tr>
		</table>
				<label>&nbsp;</label><input type="submit" name="submit" value="<?php echo load_lang('save'); ?>" class="fancy-button-green" />
				<input type="reset" name="reset" value="<?php echo load_lang('reset'); ?>" class="fancy-button-grey" />
        </form>
    </div>
</body>
</html>