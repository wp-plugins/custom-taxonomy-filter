<?php
/*
Plugin Name: Custom Taxonomy filter in Wordpress Admin Post Listing
Plugin URI: http://codeboxr.com/product/custom-taxonomy-filter-in-wordpress-admin-post-listing
Description: This plugin adds custom taxonomy filter in wordpress admin post listing panel.
Author: Codeboxr Team
Version: 1.0
Author URI: http://codeboxr.com
*/


/*
    Copyright 2012-2014  codeboxr.com (email : sabuj@codeboxr.com)
    

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?php
// avoid direct calls to this file where wp core files not present
if (!function_exists ('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
/*
 * Use WordPress 2.6 Constants
 */
if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}

$customtaxfilterinadmin = get_option('customtaxfilterinadmin');

register_activation_hook( __FILE__, 'customtaxfilterinadmin_activate' );
register_deactivation_hook(__FILE__, 'customtaxfilterinadmin_deactivation');

add_action('admin_menu', 'customtaxfilterinadmin_admin');   //adding menu in admin menu settings





//plugin activation action
function customtaxfilterinadmin_activate()
{
    global $customtaxfilterinadmin;
    $defaults = array('post' => 'on' );
    foreach($defaults  as $key => $value)
    {
        $customtaxfilterinadmin[$key] = $value;
    }
    update_option('customtaxfilterinadmin',$customtaxfilterinadmin);

}

//plugin deactivation action
function customtaxfilterinadmin_deactivation()
{
    global $customtaxfilterinadmin;
    //let's keep the otpion table clean
    delete_option('customtaxfilterinadmin');
    
}

function customtaxfilterinadmin_admin()
{
    global $customtaxfilterinadmin_hook, $customtaxfilterinadmin;
    //add_options_page(page_title, menu_title, access_level/capability, file, [function]);
    if (function_exists('add_options_page')) {
            $page_hook = add_options_page('Custom Taxonomy filter in Wordpress Admin Post Listing', 'Custom Tax Filter', 8, 'customtaxfilterinadmin', 'customtaxfilterinadmin_admin_option');
    }    
    

}

add_action( 'init', 'cb_create_customtaxfilterinadmin', 0 );
function cb_create_customtaxfilterinadmin(){
    add_filter('request', 'cb_customtaxfilterinadmin_feedrequest');
}

function cb_customtaxfilterinadmin_feedrequest($qv) {
	if (isset($qv['feed']) && !isset($qv['post_type'])){
            $customtaxfilterinadmin = get_option('customtaxfilterinadmin'); 
            //var_dump($customtaxfilterinadmin);
            $ptypes =  array();
            if(!empty($customtaxfilterinadmin)):
                foreach($customtaxfilterinadmin as $key => $value){
                    if($value == 'on')
                    $ptypes[] = $key;
                }
                $qv['post_type'] = $ptypes;
            endif;
        }    
	return $qv;
}

//admin option page
function customtaxfilterinadmin_admin_option()
{       
    //global $wp_taxonomies;
    //var_dump($wp_taxonomies);
    ?>
    <div class="wrap">
	<div class="icon32" id="icon-options-general"><br/></div>
	<h2>Custom Taxonomy filter in Wordpress Admin Post Listing</h2>
	<?php
        global $customtaxfilterinadmin;
        //var_dump($customtaxfilterinadmin);
        $builtinposts = array();
        $customposts  = array();
        $alltypeposts = array();
        
        $builtintaxs = array();
        $customtaxs  = array();
        $alltypetaxs = array();
        
        

        $builtinargs = array(
          'public'   => true,
          'show_ui'  => true,
          '_builtin' => true
          //'publicly_queryable' => true
        ); 

        $customargs = array(
          'public'   => true,
          'show_ui'  => true,
          '_builtin' => false
          //'publicly_queryable' => true
        ); 

        $output = 'objects'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
        
        //builtin post types
        $post_typesb = get_post_types($builtinargs, $output, $operator); 
        foreach ($post_typesb  as $post_typeb ) {
            $label  = $post_typeb->labels->name;
            $name   = $post_typeb->name;
            $alltypeposts[$name] = $label;
            $builtinposts[$name] = $label;
            
            $taxonomies = get_object_taxonomies($name, 'objects');
            foreach($taxonomies as $taxonomy){
                $labeltax   = $taxonomy->labels->name;
                $nametax    = $taxonomy->name;
                if($taxonomy->_builtin == '1'){                    
                    $builtintaxs[$nametax]  = $labeltax;
                    $alltypetaxs[$nametax]  = $labeltax;
                 }
                else{
                    $customtaxs[$nametax]   = $labeltax;
                    $alltypetaxs[$nametax]  = $labeltax;
                }
            }                        
        }
                
        //custom post types
        $post_typesc = get_post_types($customargs, $output, $operator); 
        foreach ($post_typesc  as $post_typec ) {
            $label = $post_typec->labels->name;
            $name = $post_typec->name;
            $alltypeposts[$name] = $label;
            $customposts[$name]  = $label;
            
            $taxonomies = get_object_taxonomies($name, 'objects');
            foreach($taxonomies as $taxonomy){
                $labeltax   = $taxonomy->labels->name;
                $nametax    = $taxonomy->name;
                if($taxonomy->_builtin == '1'){                    
                    $builtintaxs[$nametax]  = $labeltax;
                    $alltypetaxs[$nametax]  = $labeltax;
                 }
                else{
                    $customtaxs[$nametax]   = $labeltax;
                    $alltypetaxs[$nametax]  = $labeltax;
                }
            }  
        }                    
        
	if(isset($_POST['ucustomtaxfilterinadmin'])) {
		check_admin_referer('customtaxfilterinadmin');
		//post var
		foreach($alltypeposts  as $key => $value){
                    $customtaxfilterinadmin[$key] = trim($_POST['pt'.$key]);                                                            
                }                                               
                foreach($alltypetaxs  as $key => $value){
                    $customtaxfilterinadmin[$key] = trim($_POST['tx'.$key]);                                                            
                }                                               
                update_option('customtaxfilterinadmin',$customtaxfilterinadmin);
                //var_dump($customtaxfilterinadmin);
                
	}//end main if       
        
        $customtaxfilterinadmin = (array)get_option('customtaxfilterinadmin');       

        if(isset($_POST['ucustomtaxfilterinadmin'])) {
            echo '<!-- Last Action --><div id="message" class="updated fade"><p>Options updated</p></div>';
        }

?>

    <div style="width: 50%; float: left; display: inline; margin-right: 10px;">
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <?php wp_nonce_field('customtaxfilterinadmin'); ?>
        <h3>Plugin Options</h3>
        <table cellspacing="0" class="widefat post fixed">
            <thead>
            <tr>
                <th style="" class="manage-column" scope="col">Post Types</th>
                <th style="" class="manage-column" scope="col">Selection</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th style="" class="manage-column" scope="col">Post Types</th>
                <th style="" class="manage-column" scope="col">Selection</th>
            </tr>
            </tfoot>
            <tbody>                
                    <?php                                                                                
                    //var_dump($alltypeposts);
                    echo '<tr><td colspan="2"><h3>Built-in Posts Types</h3></td></tr>';
                    foreach ($builtinposts  as $key => $value ) {
                        
                        echo '<tr>';                        
                        echo '<td>'. $value.'['.$key. ']</td>';
                        echo '<td><label for="pt'.$key.'"><input id="pt'.$key.'" type="checkbox" name="pt'.$key.'" '.checked('on',$customtaxfilterinadmin[$key],false).' /> Enable/Disable</label></td>';                        
                        echo '</tr>';                        
                        echo '<tr><td colspan="2">';
                        $taxonomies = get_object_taxonomies($key, 'objects');
                        ?>
                        <table cellspacing="0" class="widefat post fixed">
                            <thead>
                            <tr>
                                <th style="" class="manage-column" scope="col">Taxonomy Name</th>
                                <th style="" class="manage-column" scope="col">Built-in</th>
                                <th style="" class="manage-column" scope="col">Selection</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th style="" class="manage-column" scope="col">Taxonomy Name</th>
                                <th style="" class="manage-column" scope="col">Built-in</th>
                                <th style="" class="manage-column" scope="col">Selection</th>
                            </tr>
                            </tfoot>
                            <tbody> 
                                
                               <?php  
                               foreach($taxonomies as $taxonomy){
                                   $tkey = $taxonomy->name;                                                                  
                               ?>    
                                    
                                    <tr>                                    
                                        <td><?php echo $taxonomy->labels->name; ?></td>
                                        <td><?php echo is_cbplgtaxbuiltin($taxonomy->_builtin); ?></td>
                                        <?php echo '<td><label for="tx'.$tkey.'"><input id="tx'.$tkey.'" type="checkbox" name="tx'.$tkey.'" '.checked('on',$customtaxfilterinadmin[$tkey],false).' /> Enable/Disable</label></td>';  ?>
                                    </tr>
                               <?php  }  ?>
                                
                            </tbody>
                            </table>
                        <?php                                              
                        echo '</tr>';
                    }
                    
                    echo '<tr><td colspan="2"><h3>Custom Posts Types</h3></td></tr>';
                    foreach ($customposts  as $key => $value ) {
                        
                        echo '<tr>';                        
                        echo '<td>'. $value.'['.$key. ']</td>';
                        echo '<td><label for="pt'.$key.'"><input id="pt'.$key.'" type="checkbox" name="pt'.$key.'" '.checked('on',$customtaxfilterinadmin[$key],false).' /> Enable/Disable</label></td>';                        
                        echo '</tr>';
                        echo '<tr><td colspan="2">';
                        $taxonomies = get_object_taxonomies($key, 'objects');
                        ?>
                        <table cellspacing="0" class="widefat post fixed">
                            <thead>
                            <tr>
                                <th style="" class="manage-column" scope="col">Taxonomy Name</th>
                                <th style="" class="manage-column" scope="col">Built-in</th>
                                <th style="" class="manage-column" scope="col">Selection</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th style="" class="manage-column" scope="col">Taxonomy Name</th>
                                <th style="" class="manage-column" scope="col">Built-in</th>
                                <th style="" class="manage-column" scope="col">Selection</th>
                            </tr>
                            </tfoot>
                            <tbody> 
                                
                               <?php  
                               foreach($taxonomies as $taxonomy){
                                   $tkey = $taxonomy->name;                                                                  
                               ?>    
                                    
                                    <tr>                                    
                                        <td><?php echo $taxonomy->labels->name; ?></td>
                                        <td><?php echo is_cbplgtaxbuiltin($taxonomy->_builtin); ?></td>
                                        <?php echo '<td><label for="tx'.$tkey.'"><input id="tx'.$tkey.'" type="checkbox" name="tx'.$tkey.'" '.checked('on',$customtaxfilterinadmin[$tkey],false).' /> Enable/Disable</label></td>';  ?>
                                    </tr>
                               <?php  }  ?>
                                
                            </tbody>
                            </table>
                        <?php                                              
                        echo '</tr>';
                    }
                    ?>                    
                
                <tr valign="top">
                        <td></td>
                        <td><input type="submit" name="ucustomtaxfilterinadmin" class="button-primary" value="Save Changes" ></td>
                </tr>                 
            </tbody>
        </table>        
        </form>
    </div>    
</div>
    <div style="width: 40%; float: left; display: inline;">
    <h3>Help Center</h3>
    <table cellspacing="0" class="widefat post fixed">
            <thead>
            <tr>
                <th style="" class="manage-column" scope="col">Supports & Contacts</th>
            
            </tr>
            </thead>
            
            <tbody>
                <tr>
                    <td>
                        <p>Product Name: Custom Taxonomy filter in Wordpress Admin Post Listing</p>                        
                        <p>Product Page: <a href="http://codeboxr.com/product/custom-taxonomy-filter-in-wordpress-admin-post-listing">Custom Taxonomy filter in Wordpress Admin Post Listing</a></p>
                        <p>Supports: <a href="http://codeboxr.com/contact-us.html">Contact</a></p>
                    </td>
                </tr>
            </tbody>
    </table>    
    </div>
    <?php
}

//add plugin setting page link in plugin listing page
function add_customtaxfilterinadmin_settings_link( $links ) {
  $settings_link = '<a href="options-general.php?page=customtaxfilterinadmin">Settings</a>';
  array_unshift( $links, $settings_link );
  return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'add_customtaxfilterinadmin_settings_link' );


function is_cbplgtaxbuiltin($flag = ''){
    if($flag == '1'){ return 'Yes';}
    else  return "No";
}




function todo_restrict_customtaxfilterinadmin_posts() {
    global $typenow;
    global $customtaxfilterinadmin;
    
    //var_dump($typenow);
        
    $argsb   =   array( 'public' => true, '_builtin' => false );
    $argsc  =   array( 'public' => true, '_builtin' => true );
    $post_typesb        = get_post_types($argsb);    
    $post_typesc        = get_post_types($argsc);
    $post_types         = array_merge($post_typesb, $post_typesc);        
    
    if ( in_array($typenow, $post_types) &&  $customtaxfilterinadmin[$typenow] == 'on') {
        $filter = get_object_taxonomies($typenow);                   
        foreach ($filter as $tax_slug) {
            if($customtaxfilterinadmin[$tax_slug] == 'on'){
                $tax_obj = get_taxonomy($tax_slug);                                
                wp_dropdown_categories(array(
                    'show_option_all' => __('Show All '.$tax_obj->label ),
                    'taxonomy' => $tax_slug,
                    'name' => $tax_obj->name,
                    'orderby' => 'name',
                    'selected' => $_GET[$tax_obj->query_var],
                    'hierarchical' => $tax_obj->hierarchical,
                    'show_count' => true,
                    'hide_empty' => false
                ));                
            }//end if                                        
        }//end foreach
    }
}


function customtaxfilterinadmin_convert_restrict($query) {
    global $pagenow;
    global $typenow;
    global $customtaxfilterinadmin;
    //var_dump($pagenow);
    
    //var_dump($typenow);
    
    if ($pagenow    == 'edit.php' && $customtaxfilterinadmin[$typenow]== 'on') {
        $filters = get_object_taxonomies($typenow);
        //var_dump($tax_slug);
        
        foreach ($filters as $tax_slug) {
            if($customtaxfilterinadmin[$tax_slug] == 'on'){
                //var_dump($tax_slug);
                $var = &$query->query_vars[$tax_slug];
                if ( isset($var) ) {
                    $term = get_term_by('id',$var,$tax_slug);
                    $var = $term->slug;
                }
            }//end if        
        }//end foreach
        
    }
}

function customtaxfilterinadmin_is_tax_on_post_search($query) {
    global $pagenow;
    $qv = &$query->query_vars;
    if ($pagenow == 'edit.php' && isset($qv['taxonomy']) && isset($qv['s'])) {
        $query->is_tax = true;
    }
}

add_action( 'restrict_manage_posts', 'todo_restrict_customtaxfilterinadmin_posts' );
add_filter('parse_query','customtaxfilterinadmin_convert_restrict');
add_filter('parse_query','customtaxfilterinadmin_is_tax_on_post_search');