<?php

include("../../../lib/common.php"); 
   
   $id=$_REQUEST["id"];

   //$sql_login = "SELECT * from student_profile where stud_matric_no='$stud_matric_no'";
   //$db->query($sql_login);
   //$db->next_record();
 
   $txtstud_name=$_REQUEST["txtstud_name"];
   $sql_delete = "DELETE from ref_faq_category WHERE id= '$id'";
   
    //$sql_delete = "DELETE from student_profile WHERE stud_matric_no= '$stud_matric_no'";

   $process = $db->query($sql_delete);
   header("location:delete_category.php");
  		


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>

</body>
</html>
