<?php

include("../../../lib/common.php");
checkLogin();

session_start();
$userid=$_REQUEST['uid'];
//$studentMatrixNo=$_REQUEST['uid'];
//$pgThesisId=$_REQUEST['tid'];
//$pgProposalId=$_REQUEST['pid'];

?>	


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
	<link rel="stylesheet" type="text/css" href="../../../theme/css/<?php echo $css; ?>" />
    <link rel="stylesheet" type="text/css" href="../../../theme/css/colorbox.css" media="screen" />
   	<link rel="stylesheet" href="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<link rel="stylesheet" href="../../lib/js/datePicker/jquery-ui-1.8.11.custom.css" />
	<script type="text/javascript" src="../../../lib/js/ckeditor/ckeditor.js"></script>
	<script src="../../../lib/js/jquery.min2.js"></script>
    <script type="text/javascript" src="../../../lib/js/datePicker/jquery-ui-1.8.11.custom.min.js"></script>
    <style type="text/css">
<!--
.style2 {font-size: 24px}
-->
    </style>
</head>
<body>
 <?php
    if(!empty($msg)) {
        foreach($msg as $err) {
            echo $err;
        }
    }
?>


<script>
$(document).ready(function() {

              
        CKEDITOR.instances.textarea.updateElement();
					//CKEDITOR.instances.answer.updateElement();

            
});			
</script>

<SCRIPT LANGUAGE="JavaScript">

</SCRIPT>
<script>
$(function() {
	$( "#datepickerFirst" ).datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '-100:+0',
		dateFormat: 'dd-mm-yy'
		});
});
</script>

 <form id="form1" name="form1" method="post" enctype="multipart/form-data"> 
  

<br/>			
<fieldset>
<legend><strong>View Frequently Asked Question</strong></legend>
<p align="left" class="ui-priority-primary style2">Student</p>
<table width="624" align="left">
  <tr>
    <?
			
/*onsubmit="return saveRec();"*/
$sql_faq_student = "SELECT faq.question, faq.answer, ref_faq_category.description, faq.id FROM faq
LEFT JOIN ref_faq_category ON faq.category_id=ref_faq_category.id
WHERE faq.category_id = 1 ";
				
	$result_sql_faq_student = $db->query($sql_faq_student);
	$db->next_record();

	 		do{
			$faq_id=$db->f("id");
   			$category=$db->f("description");
			$category_id=$db->f("category_id");
   			$question=$db->f("question");
   			$answer=$db->f("answer");

   		?>
    <td width="54" bgcolor="#CCCCCC">Question</td>
    <td width="558" bgcolor="#CCCCCC"><b>
      <?=$question?>
    </b></td>
  </tr>
  <tr bgcolor="#B7B7FF">
    <td height="157" bgcolor="#00CCCC">Answer</td>
    <td bgcolor="#00CCCC"><b>
      <?=$answer?>
    </b></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="21">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?
	 		}while($db->next_record());
	
		?>
</table>
<p align="left" class="ui-priority-primary style2">&nbsp;</p>
<p align="left" class="ui-priority-primary style2">&nbsp;</p>
<p align="left" class="ui-priority-primary style2">&nbsp;</p>
<p align="left" class="ui-priority-primary style2">&nbsp;</p>
<p align="left" class="ui-priority-primary style2">&nbsp;</p>
<fieldset style="width:800px">
	   	 
       <p align="left" class="ui-priority-primary style2">Faculty</p>
       <table width="624" align="left">
         <tr>
           <?
			
/*onsubmit="return saveRec();"*/
$sql_faq_faculty = "SELECT faq.question, faq.answer, ref_faq_category.description, faq.id FROM faq
LEFT JOIN ref_faq_category ON faq.category_id=ref_faq_category.id
WHERE faq.category_id = 2";
				
	$result_sql_faq_student = $db->query($sql_faq_faculty);
	$db->next_record();

	 		do{
			$faq_id=$db->f("id");
   			$category=$db->f("description");
			$category_id=$db->f("category_id");
   			$question=$db->f("question");
   			$answer=$db->f("answer");

   		?>
           <td width="54" bgcolor="#CCCCCC">Question</td>
           <td width="558" bgcolor="#CCCCCC"><b>
             <?=$question?>
           </b></td>
         </tr>
         <tr bgcolor="#B7B7FF">
           <td height="157" bgcolor="#00CCCC">Answer</td>
           <td bgcolor="#00CCCC"><b>
             <?=$answer?>
           </b></td>
         </tr>
         <tr bgcolor="#FFFFFF">
           <td height="21">&nbsp;</td>
           <td>&nbsp;</td>
         </tr>
         <?
	 		}while($db->next_record());
	
		?>
       </table>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">
       
  <fieldset style="width:800px">
			 <p align="left" class="ui-priority-primary style2">Supervisor</p>
<table width="624" align="left">
         <tr>
           <?
			
/*onsubmit="return saveRec();"*/
$sql_faq_supervisor = "SELECT faq.question, faq.answer, ref_faq_category.description, faq.id FROM faq
LEFT JOIN ref_faq_category ON faq.category_id=ref_faq_category.id
WHERE faq.category_id = 3";
				
	$result_sql_faq_student = $db->query($sql_faq_supervisor);
	$db->next_record();

	 		do{
			$faq_id=$db->f("id");
   			$category=$db->f("description");
			$category_id=$db->f("category_id");
   			$question=$db->f("question");
   			$answer=$db->f("answer");

   		?>
           <td width="54" bgcolor="#CCCCCC">Question</td>
           <td width="558" bgcolor="#CCCCCC"><b>
             <?=$question?>
           </b></td>
         </tr>
         <tr bgcolor="#B7B7FF">
           <td height="157" bgcolor="#00CCCC">Answer</td>
           <td bgcolor="#00CCCC"><b>
             <?=$answer?>
           </b></td>
         </tr>
         <tr bgcolor="#FFFFFF">
           <td height="21">&nbsp;</td>
           <td>&nbsp;</td>
         </tr>
         <?
	 		}while($db->next_record());
	
		?>
  </table>
       <p align="left" class="ui-priority-primary style2"></p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
  <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       </fieldset>
	   
	   <fieldset style="width:800px">
			 <p align="left" class="ui-priority-primary style2">Reviewer</p>
             <table width="624" align="left">
               <tr>
                 <?
			
/*onsubmit="return saveRec();"*/
$sql_faq_reviewer = "SELECT faq.question, faq.answer, ref_faq_category.description, faq.id FROM faq
LEFT JOIN ref_faq_category ON faq.category_id=ref_faq_category.id
WHERE faq.category_id = 4";
				
	$result_sql_faq_student = $db->query($sql_faq_reviewer);
	$db->next_record();

	 		do{
			$faq_id=$db->f("id");
   			$category=$db->f("description");
			$category_id=$db->f("category_id");
   			$question=$db->f("question");
   			$answer=$db->f("answer");

   		?>
                 <td width="54" bgcolor="#CCCCCC">Question</td>
                 <td width="558" bgcolor="#CCCCCC"><b>
                   <?=$question?>
                 </b></td>
               </tr>
               <tr bgcolor="#B7B7FF">
                 <td height="157" bgcolor="#00CCCC">Answer</td>
                 <td bgcolor="#00CCCC"><b>
                   <?=$answer?>
                 </b></td>
               </tr>
               <tr bgcolor="#FFFFFF">
                 <td height="21">&nbsp;</td>
                 <td>&nbsp;</td>
               </tr>
               <?
	 		}while($db->next_record());
	
		?>
             </table>
             <p align="left" class="ui-priority-primary style2"></p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       </fieldset>
	   
	  <fieldset style="width:800px">
			 <p align="left" class="ui-priority-primary style2">Senate</p>
             <table width="624" align="left">
               <tr>
                 <?
			
/*onsubmit="return saveRec();"*/
$sql_faq_senate = "SELECT faq.question, faq.answer, ref_faq_category.description, faq.id FROM faq
LEFT JOIN ref_faq_category ON faq.category_id=ref_faq_category.id
WHERE faq.category_id = 5";
				
	$result_sql_faq_student = $db->query($sql_faq_senate);
	$db->next_record();

	 		do{
			$faq_id=$db->f("id");
   			$category=$db->f("description");
			$category_id=$db->f("category_id");
   			$question=$db->f("question");
   			$answer=$db->f("answer");

   		?>
                 <td width="54" bgcolor="#CCCCCC">Question</td>
                 <td width="558" bgcolor="#CCCCCC"><b>
                   <?=$question?>
                 </b></td>
               </tr>
               <tr bgcolor="#B7B7FF">
                 <td height="157" bgcolor="#00CCCC">Answer</td>
                 <td bgcolor="#00CCCC"><b>
                   <?=$answer?>
                 </b></td>
               </tr>
               <tr bgcolor="#FFFFFF">
                 <td height="21">&nbsp;</td>
                 <td>&nbsp;</td>
               </tr>
               <?
	 		}while($db->next_record());
	
		?>
             </table>
             <p align="left" class="ui-priority-primary style2"></p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
       <p align="left" class="ui-priority-primary style2">&nbsp;</p>
  </fieldset>
   </fieldset>
  
	
   <fieldset>
	   <legend></legend>	
	   <p>&nbsp;</p>
	   
   </fieldset>	
		
</form>
 <p>&nbsp;</p>
 <p>&nbsp;</p>
 <p>&nbsp;</p>
 <p>&nbsp;</p>
 <p>&nbsp;</p>
 <p>&nbsp;</p>
</body>
</html>
