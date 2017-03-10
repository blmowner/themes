<?php
//include("common.php");

/**
 * Generate HTML for multi-dimensional menu from MySQL database
 * with ONE QUERY and WITHOUT RECURSION 
 * @author J. Bruni
 */
//global $lang;
 
class MenuBuilder 
{
	/**
	 * MySQL connection
	 */
	var $conn;
    	
	/**
	 * Menu items
	 */
	var $items = array();
	
	/**
	 * HTML contents
	 */
	var $html  = array();
	
	/**
	 * Create MySQL connection
	 */
     
	function MenuBuilder()
	{
	   global $db;
		//$this->conn = mysql_connect( 'localhost:3307', 'root', '' );
        //mysql_select_db( 'frameworkv2', $this->conn );
        $this->conn = mysql_connect( $db->Host, $db->User, $db->Password );
		mysql_select_db( $db->Database, $this->conn );
    }
	
	/**
	 * Perform MySQL query and return all results
	 */
	function fetch_assoc_all( $sql )
	{
		$result = mysql_query( $sql, $this->conn );
		
		if ( !$result )
			return false;
		
		$assoc_all = array();
		
		while( $fetch = mysql_fetch_assoc( $result ) )
			$assoc_all[] = $fetch;
		
		mysql_free_result( $result );
		
		return $assoc_all;
	}
	
	/**
	 * Get all menu items from database
	 */
	function get_menu_items()
	{
	   global $lang;
		// Change the field names and the table name in the query below to match tour needs
		//$sql = 'SELECT id, parent_id, title, link, position FROM menu_item ORDER BY parent_id, position';
 
        /*$sql = "SELECT busm.menu_id as id, parent_id,text as title, menu_link as link, menu_level as position 
                from base_user_role bur
                left join base_menu_link bml on (bml.role_id=bur.role_id)
                left join base_user_sys_menu busm on (busm.menu_id=bml.menu_id)
                left join base_language_text blt on (blt.variable=bml.menu_id)
                where blt.language_code='$lang' and bur.role_id='".$_SESSION['user_role']."'
                order by parent_id, position";*/

         $sql = "SELECT busm.menu_id as id, parent_id,text as title, menu_link as link, menu_level as position 
                from base_user_role bur
                left join base_menu_link bml on (bml.role_id=bur.role_id)
                left join base_user_sys_menu busm on (busm.menu_id=bml.menu_id)
                left join base_language_text blt on (blt.variable=bml.menu_id)
                where blt.language_code='$lang' and bur.role_id='".$_SESSION['user_role']."'
                order by busm.menu_sequence";

        
		return $this->fetch_assoc_all( $sql );
	}
	
	/**
	 * Build the HTML for the menu 
	 */
	function get_menu_html( $root_id = 0 )
	{
		$this->html  = array();
		$this->items = $this->get_menu_items();
		
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
					'%1$s<li><a href="%2$s" target="main" onclick="showTitle('.$option['value']['id'].')">%3$s</a>',
					$tab,   // %1$s = tabulation
					$option['value']['link'],   // %2$s = link (URL)
					$option['value']['title']   // %3$s = title
				); 
				$this->html[] = $tab . "\t" . '<ul class="submenu">';
				
				array_push( $parent_stack, $option['value']['parent_id'] );
				$parent = $option['value']['id'];
			}
			else
				// HTML for menu item with no children (aka "leaf") 
				$this->html[] = sprintf(
                
                    '%1$s<li><a href="%2$s" target="main" onclick="showTitle('.$option['value']['id'].')">%3$s</a></li>',
					str_repeat( "\t", ( count( $parent_stack ) + 1 ) * 2 - 1 ),   // %1$s = tabulation
					$option['value']['link'],   // %2$s = link (URL)
					$option['value']['title']   // %3$s = title
				);
		}
		
		// HTML wrapper for the menu (close)
		$this->html[] = '</ul>';
		
		return implode( "\r\n", $this->html );
	}
}

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: application/xml; charset=utf-8");

?>