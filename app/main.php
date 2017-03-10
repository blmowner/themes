<?php 
//session_start();
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
include("../lib/common.php"); 
checkLogin();

$sql = "SELECT a.id, a.message_id, b.sender, b.subject, DATE_FORMAT(b.message_date,'%d-%b-%Y %h:%i %p') AS message_date, a.recipient_status,
c.description as message_detail_desc
FROM pg_messages_detail a
LEFT JOIN pg_messages b ON (b.id = a.message_id) 
LEFT JOIN ref_message_status c ON (c.id = a.recipient_status)
WHERE a.recipient = '".$_SESSION['user_id']."'
 AND a.recipient_status IN ('NEW')
ORDER BY a.recipient_status, b.message_date";

$result_sql = $dbf->query($sql);
$dbf->next_record();
$row_cnt = mysql_num_rows($result_sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>" style="height:80%;">

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta name="author" content="MJMZ" />

	<title><?php echo load_lang('frame_name_main'); ?></title>
    <link rel="stylesheet" type="text/css" href="../theme/css/<?php echo $css; ?>" media="screen" />
    <script type="text/javascript" src="../lib/js/rightClick.js"></script>
    <script type="text/javascript">
        function showLink(str,t)
        {
        if (str=="")
          {
          document.getElementById("left-panel").innerHTML = '';
          return;
          //var str=1;
          }
        if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
          }
        else
          {// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          }
        xmlhttp.onreadystatechange=function()
          {
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
            document.getElementById("left-panel").innerHTML=xmlhttp.responseText;
            }
          }
		  
		if(t == 1)
		{
			$("#PAGER-TITLE").load("../include/home.php");
			//$("#PAGER-TITLE").load("../include/get_title.php?SUB_MENU_ID=113");
		}  
        xmlhttp.open("GET","../include/sub_menu.php?PARENT_MENU_ID="+str,true);
        xmlhttp.send();
        }
        
        function showTitle(st)
        {
        if (st=="")
          {
          document.getElementById("PAGER-TITLE").innerHTML = '';
          return;
          //var str=1;
          }
        if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
          }
        else
          {// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
          }
        xmlhttp.onreadystatechange=function()
          {
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
            document.getElementById("PAGER-TITLE").innerHTML=xmlhttp.responseText;
            }
          }
        xmlhttp.open("GET","../include/get_title.php?SUB_MENU_ID="+st,true);
        xmlhttp.send();
        }
        
        /////// active hyperlink //////////
        var previousElement = null;
        function changeClass (newElement) {
             if (previousElement != null) {
                  previousElement.className = "";
             }
        
             newElement.className = "navcurrent";
             previousElement = newElement;
        }        
    </script>
    
</head>

<body onload="showLink('1')" style = "height: 100%;">
<div id="idletimeout">
    <?php echo load_lang('idle_logout_msg'); ?>
</div>
<!-- START : put this code below the counting bar -->
<script src="../lib/js/idle/jquery.min.js" type="text/javascript"></script>
<script src="../lib/js/idle/jquery.idletimer.js" type="text/javascript"></script>
<script src="../lib/js/idle/jquery.idletimeout.js" type="text/javascript"></script>
<script type="text/javascript">
$.idleTimeout('#idletimeout', '#idletimeout a', {
	idleAfter: 3600,
	pollingInterval: 2,
	//keepAliveURL: 'keepalive.php',
	serverResponseEquals: 'OK',
	onTimeout: function(){
		$(this).fadeOut();
		window.location = "<?php echo load_constant('REDIRECT_LOGOUT') ?>";
	},
	onIdle: function(){
		$(this).fadeIn(); // show the warning bar
	},
	onCountdown: function( counter ){
		$(this).find("span").html( counter ); // update the counter
	},
	onResume: function(){
		$(this).fadeOut(); // hide the warning bar
	}
});

</script>
<!--END : put this code below the counting bar -->
<div id="wrapper"><!-- content wrapper -->
    
    <div id="header"  class="inline-ul"><!-- header -->
        
        <h2><?php echo load_lang('frame_name_main'); ?></h2>
        <span class="display_name">
		    <? 
			$user_id=$_SESSION['user_id'];
			$sqlstd="SELECT CHAR_LENGTH(staff_id) as total, SUBSTR(staff_id,1,2) as prefix, user_type
			FROM user_acc WHERE staff_id='$user_id'";
			$db->query($sqlstd);
			$db->next_record();
			$total=$db->f('total');
			$prefix=$db->f('prefix');
			$userType=$db->f('user_type');
			?>
		    <? if ($total=='12' && $prefix=='07'){ ?>
            <?php echo getValue_lk('name','student','matrix_no',$_SESSION['user_id']); ?><br class="clear" />
			<? }
			else if ($total=='12' && $prefix!='07'){ ?>
            <?php echo getValue('name','student','matrix_no',$_SESSION['user_id']); ?><br class="clear" />
			<? } else { ?>
			<?php echo getValue('name','new_employee','empid',$_SESSION['user_id']); ?><br class="clear" />
            <? } ?>
			<?if ($userType=="E") $userDesc = "Staff";
			else if ($userType=="S") $userDesc = "Student";?>
			<strong>User ID:</strong> <?php echo $user_id?> (<?=$userDesc?>)<br class="clear" />
            <!-- START check whether the config for language selection is enable or not -->
            <!--<?php if($langSelect == 1) {  ?>
            <span class="choose_lang"><?php echo load_lang('language'); ?></span><br class="clear" />
            <?php } ?>-->
            <!-- END check whether the config for language selection is enable or not -->
            <strong>Last Login:</strong> <?php echo getValue_local('user_last_login','user_acc','staff_id',$_SESSION['user_id']); ?><br class="clear" />

            <a href="../logout.php" class="logout"><?php echo load_lang("logout"); ?></a>
        </span>
<script type='text/javascript'>
window.onload = firstLoad;
	function firstLoad() 
	{
		var total = document.getElementById("totalmessages").value;
		if(total>0)
		{
			$("#J").load("messagecount.php");
		}
		else
		{
		
		}
		$("#PAGER-TITLE").load("../include/home.php");
	}
	
	var auto_refresh = setInterval("k()", 600);
	function k()
	{
		var total = document.getElementById("totalmessages").value;
		/*var total = document.getElementById("totalmessages").value; //txt area 1
		//var id = document.getElementById("lol").value;
		//var total = $("#totalmessages").val();
		var L = $("#totalmessages").load("messagecount.php");
		
		
		//$('#total').text($(this).val());	
		if(total > 0)
		{
			sNotify.addToQueue('You have '+total+' messages');
			sNotify.alterNotifications('chat_msg');
		}
		else
		{
			sNotify.addToQueue('There is no message laa '+total);
			sNotify.alterNotifications('chat_msg');
		}*/
		if(total> 0)
		{
			$("#J").load("messagecount.php");
		}
		else
		{
		
		}
	}
	
</script>

		
        <br class="clear" />
        <ul>
            <?php
			$sql_role = "SELECT * FROM user_acc WHERE staff_id = '$user_id'";
			
			$dbg->query($sql_role);
			$dbg->next_record();
			$dbg->f("role_id");	
			
                $sql_top_menu = "SELECT busm.menu_id,bur.role_id,role_type,menu_link,blt.text 
                                 FROM base_user_role bur
                                 LEFT JOIN base_menu_link bml on (bml.role_id=bur.role_id)
                                 LEFT JOIN base_user_sys_menu busm on (busm.menu_id=bml.menu_id)
                                 LEFT JOIN base_language_text blt on (blt.variable=bml.menu_id)
                                 WHERE blt.language_code='".$lang."' AND busm.menu_level=1 AND bur.role_id='".$_SESSION['user_role']."'
                                 /* ORDER BY busm.menu_id ASC */ 
								 ORDER BY menu_sequence
								 ";
                $db->query($sql_top_menu);
				$db->next_record();
                do {
                   //echo "<li><a href=".$_SERVER['SCRIPT_NAME']."?pid=".$db->f("menu_id").">".$db->f("text")."</a></li>";
                   //echo "<li><a href=\"#\" onclick=\"showLink(".$db->f("menu_id")."); changeClass(this);\">".$db->f("text")."</a></li>";
				   if($db->f("text")=='Home' && $dbg->f("role_id")!='20150410001')
					{
						echo "<li><a href=\"control_panel/announcement/announcement.php\" target=\"main\" onclick=\"showLink(".$db->f("menu_id").",1); changeClass(this);\">".$db->f("text").""; 	
						
					}
					else if($db->f("text")=='Home' && $dbg->f("role_id")=='20150410001')
					{
echo "<li><a href=\"control_panel/announcement/announcement.php\" target=\"main\" onclick=\"showLink(".$db->f("menu_id").",1); changeClass(this);\">".$db->f("text")."";
					}
					else
					{
						echo "<li><a href=\"#\" onclick=\"showLink(".$db->f("menu_id")."); changeClass(this);\">".$db->f("text")."";
					}
				   if($db->f("text")=='Message')
				   {
				   		/*for ($i=0; $i<$row_cnt; $i++)
						{
							//echo "<input type = 'text' name='lol' id = 'lol' value = '<?=$messageid[$i]?>'/>";*/
											
                   			echo " <span id = 'J' style=\"color:red;\"></span>";//<sup>
						//}
					}
					echo "</a></li>";
                } while($db->next_record());
            ?>
		    <input type = "hidden" name="totalmessages" id = "totalmessages" value = "<?=$row_cnt?>"/>
        </ul>

        <br class="clear" />
    </div>
    
    <div id = "back-left"><div id="left-panel"><!-- left panel --></div></div>
    

        
    <div id="content"><!-- content -->
        <div id="title-set">
            <h2 id="PAGER-TITLE"></h2>
        </div>
        
        <iframe style="background: #FFFFFF; padding-top:5px;" src="control_panel/announcement/announcement.php" name="main" id="main" width="100%" height="96%" marginheight="0" marginwidth="0" frameborder="0"></iframe>
    
    <br class="clear" />
    </div>
    <div id="footer"><!-- footer -->
        <?php echo load_lang('footer_disclaimer'); ?><br />
        <?php echo load_lang('frame_name_main'); ?>
    </div>
</div>

</body>
</html>