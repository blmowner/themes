<?php
//include("menu_class.php");

/**
 * Generate HTML for multi-dimensional menu with checkbox
 * @author MJMZ
 */

 
class MenuRoles //extends MenuBuilder
{
   	var $conn;
	
	var $items = array();

	var $html  = array();
	
     
	/*function MenuRoles()
	{
	   parent::MenuBuilder();
    }*/
    
    function MenuBuilder()
	{
	   global $db;
		//$this->conn = mysql_connect( 'localhost:3307', 'root', '' );
        //mysql_select_db( 'frameworkv2', $this->conn );
        $this->conn = mysql_connect( $db->Host, $db->User, $db->Password );
		mysql_select_db( $db->Database, $this->conn );
        
        return $this->conn;
    }
    
    function fetch_assoc_all( $sql )
	{
		$result = mysql_query( $sql, $this->MenuBuilder() );
		
		if ( !$result )
			return false;
		
		$assoc_all = array();
		
		while( $fetch = mysql_fetch_assoc( $result ) )
			$assoc_all[] = $fetch;
		
		mysql_free_result( $result );
		
		return $assoc_all;
	}

	/**
	 * GET MENU FROM DB
	 */
	function get_menu_role_items() //get_menu_items
	{
	   global $lang;
		// Change the field names and the table name in the query below to match tour needs
		//$sql = 'SELECT id, parent_id, title, link, position FROM menu_item ORDER BY parent_id, position';

        $sql = "select busm.menu_id AS id, parent_id,text AS title, menu_link AS link, menu_level AS position
from base_user_sys_menu busm
LEFT JOIN base_language_text blt on (blt.variable=busm.menu_id)
WHERE blt.language_code='$lang' ORDER BY parent_id, position";
        
		return $this->fetch_assoc_all( $sql );
	}
	
	/**
	 * GENERATE HTML CODE FOR THE MENU 
	 */
	function get_menu_role( $root_id = 0 )
	{
		$this->html  = array();
		$this->items = $this->get_menu_role_items();
		
		foreach ( $this->items as $item )
			$children[$item['parent_id']][] = $item;
		
		// loop will be false if the root has no children (i.e., an empty menu!)
		$loop = !empty( $children[$root_id] );
		
		// initializing $parent as the root
		$parent = $root_id;
		$parent_stack = array();
		
		// HTML wrapper for the menu (open)
		$this->html[] = '<ul>';
		
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $root_id ) ) )
		{
			if ( $option === false )
			{
				$parent = array_pop( $parent_stack );
				
				// HTML for menu item containing childrens (close)
				$this->html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 ) . '</ul>';
				$this->html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ) . '</li>';
			}
			elseif ( !empty( $children[$option['value']['id']] ) )
			{
				$tab = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 );
				
				// HTML for menu item containing childrens (open)
				$this->html[] = sprintf(
					'%1$s<li><input type="checkbox" name="select[]" value="%2$d" />%3$s',
					$tab,   // %1$s = tabulation
					$option['value']['id'],   // %2$s = link (URL)
					$option['value']['title'].$option['value']['link']   // %3$s = title
				); 
				$this->html[] = $tab . "\t" . '<ul class="submenu">';
				
				array_push( $parent_stack, $option['value']['parent_id'] );
				$parent = $option['value']['id'];
			}
			else
				// HTML for menu item with no children (aka "leaf") 
				$this->html[] = sprintf(
                
                    '%1$s<li><input type="checkbox" name="select[]" value="%2$d" />%3$s</li>',
					str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ),   // %1$s = tabulation
					$option['value']['id'],   // %2$s = link (URL)
					$option['value']['title'].$option['value']['link']   // %3$s = title
				);
		}
		
		// HTML wrapper for the menu (close)
		$this->html[] = '</ul>';
		
		return implode( "\r\n", $this->html );
	}
    
    /** 
     * Get the checked value
     */ 
    function get_checked_role($role_id,$menuID) {
            global $dbf;
            $sqlSelectCheck = "SELECT menu_id FROM base_menu_link WHERE role_id='$role_id' AND menu_id='$menuID'";
            $a = $dbf->query($sqlSelectCheck);
            $rows = mysql_fetch_array($a);
            
            $val = $rows['menu_id'];
            return $val;
    }
    
    /**
     * Prepare the checked menu and list them all
     */ 
    function get_role( $root_id = 0 )
	{       
		$this->html  = array();
		$this->items = $this->get_menu_role_items();
		
		foreach ( $this->items as $item )
			$children[$item['parent_id']][] = $item;
		
		// loop will be false if the root has no children (i.e., an empty menu!)
		$loop = !empty( $children[$root_id] );
		
		// initializing $parent as the root
		$parent = $root_id;
		$parent_stack = array();
		
		// HTML wrapper for the menu (open)
		$this->html[] = '<ul>';
		
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $root_id ) ) )
		{
			if ( $option === false )
			{
				$parent = array_pop( $parent_stack );
				
				// HTML for menu item containing childrens (close)
				$this->html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 ) . '</ul>';
				$this->html[] = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ) . '</li>';
			}
			elseif ( !empty( $children[$option['value']['id']] ) )
			{
			
            // Assign the "m" valiable with the value menu_id
            $m = $this->get_checked_role($_GET['rlid'], $option['value']['id']);
            
            if($m == $option['value']['id']) {
                $checked ="checked=\"\"";
            } else {
                $checked ="";
            }
             
				$tab = str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 );
				
				// HTML for menu item containing childrens (open)
				$this->html[] = sprintf(
					'%1$s<li><input type="checkbox" name="select[]" value="%2$d" %4$s />%3$s',
					$tab,   // %1$s = tabulation
					$option['value']['id'],   // %2$s = link (URL)
					$option['value']['title']."-".$option['value']['link'],   // %3$s = title
                    $checked // checked/not - checkbox
				); 
                
				$this->html[] = $tab . "\t" . '<ul class="submenu">';
                
				
				array_push( $parent_stack, $option['value']['parent_id'] );
				$parent = $option['value']['id'];
                
                // Assign the "m" valiable with the value menu_id
                $m = $this->get_checked_role($_GET['rlid'], $option['value']['id']);
                
                if($m == $option['value']['id']) {
                    $checked ="checked=\"\"";
                } else {
                    $checked ="";
                }
                               
			}
			else {
            
            // Assign the "m" valiable with the value menu_id
                $m = $this->get_checked_role($_GET['rlid'], $option['value']['id']);
            
                if($m == $option['value']['id']) {
                    $checked ="checked=\"\"";
                } else {
                    $checked ="";
                }
            
				// HTML for menu item with no children (aka "leaf") 
				$this->html[] = sprintf(
                
                    '%1$s<li><input type="checkbox" name="select[]" value="%2$d" %4$s />%3$s</li>',
					str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ),   // %1$s = tabulation
					$option['value']['id'],   // %2$s = link (URL)
					$option['value']['title']."-".$option['value']['link'],   // %3$s = title
                    $checked // checked/not - checkbox
				); 
                
                // Assign the "m" valiable with the value menu_id
                $m = $this->get_checked_role($_GET['rlid'], $option['value']['id']);
            
                if($m == $option['value']['id']) {
                    $checked ="checked=\"\"";
                } else {
                    $checked ="";
                }
                
           }     
                
		}
		// HTML wrapper for the menu (close)
		$this->html[] = '</ul>';
		
		return implode( "\r\n", $this->html );
	}
    
    
}

//$rolemenu = new MenuRoles();
//echo $rolemenu->get_menu_role();

header("Cache-Control: no-cache, must-revalidate");
//header("Content-Type: application/xml; charset=utf-8");

?>