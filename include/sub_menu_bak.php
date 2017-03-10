<?php

/**
 * @author MJMZ
 * @copyright 2011
 */
include("../lib/common.php");
checkLogin();

$sqlParent = "SELECT busm.menu_id, busm.parent_id,bur.role_id,role_type,menu_link,blt.text 
            from base_user_role bur
            left join base_menu_link bml on (bml.role_id=bur.role_id)
            left join base_user_sys_menu busm on (busm.menu_id=bml.menu_id)
            left join base_language_text blt on (blt.variable=bml.menu_id)
            where blt.language_code='".$lang."' AND busm.menu_level != 1 
            AND bur.role_id='".$_SESSION['user_role']."' AND parent_id='".$_GET['PARENT_MENU_ID']."'
            order by busm.menu_id ASC"; 
            
$parent = $db->query($sqlParent);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="softboxkid" />

	<title>Untitled 3</title>

</head>
<body>
<ul>
<?php
    if($db->num_rows($parent) > 0) {
        while($parentRow = mysql_fetch_array($parent)) {
            echo "<li><a target=\"main\" onclick=\"showTitle(".$parentRow['menu_id'].")\" href=".$parentRow['menu_link']."?SUB_MENU_ID=".$parentRow['menu_id'].">".$parentRow['text']."</a></li>";
            $sqlChild = "SELECT busm.menu_id, busm.parent_id,bur.role_id,role_type,menu_link,blt.text 
                          FROM base_user_role bur
                          LEFT JOIN base_menu_link bml on (bml.role_id=bur.role_id)
                          LEFT JOIN base_user_sys_menu busm on (busm.menu_id=bml.menu_id)
                          LEFT JOIN base_language_text blt on (blt.variable=bml.menu_id)
                          WHERE blt.language_code='".$lang."' AND busm.menu_level != 1 
                          AND bur.role_id='".$_SESSION['user_role']."' AND parent_id='".$parentRow['menu_id']."'
                          ORDER BY busm.menu_id ASC"; 
            $child = $dba->query($sqlChild);
            if($dba->num_rows($child) > 0){
                echo "<li><ul>";
                while($childRow = mysql_fetch_array($child)) {
                    echo "<li><a target=\"main\" onclick=\"showTitle(".$childRow['menu_id'].")\" href=".$childRow['menu_link']."?SUB_MENU_ID=".$childRow['menu_id'].">".$childRow['text']."</a></li>";
                }
                echo "</ul></li>";
            }
            $dba->free();
        }
    } 
    
    $db->free();
?>
</ul>
<br class="clear" />
<?php 

?>

</body>
</html>
