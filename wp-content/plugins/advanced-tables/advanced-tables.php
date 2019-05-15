<?php
/*
Plugin Name: Advanced Tables
Plugin URI: http://codecanyon.net/item/advanced-tables-excelstyle-table-editor/11354430
Description: Excel-style table editor. Create a tables and use them in your posts - just insert shortcode in the right place.
Tags: plugin, table, tables, schedule, pricing, pricing table, shortcode, responsive
Version: 1.2.3
Author: Eugene Holin
Author URI: http://lp-tricks.com/
Text Domain: lptw_advanced_tables_domain
*/

/* Load plugin textdomain */
add_action( 'plugins_loaded', 'lptw_advanced_tables_textdomain' );
function lptw_advanced_tables_textdomain() {
  load_plugin_textdomain( 'lptw_advanced_tables_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/* load js and css styles */
add_action( 'wp_enqueue_scripts', 'lptw_advanced_tables_register_scripts' );
function lptw_advanced_tables_register_scripts() {
	wp_register_style( 'lptw-advanced-tables-style', plugins_url( 'css/advanced-tables.css', __FILE__ ) );
	wp_enqueue_style( 'lptw-advanced-tables-style' );

    wp_register_script( 'advanced-tables-frontend', plugins_url( 'js/advanced-tables-frontend.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-accordion'), true, false );
    wp_enqueue_script( 'advanced-tables-frontend' );

    wp_register_script( 'table-sorter', plugins_url( 'js/jquery.tablesorter.min.js', __FILE__ ), array('jquery'), '2.0.5', false );
    wp_enqueue_script( 'table-sorter' );

    wp_register_script( 'stacktable', plugins_url( 'js/stacktable.js', __FILE__ ), array('jquery'), true, false );
    wp_enqueue_script( 'stacktable' );

	wp_register_style( 'stacktable-style', plugins_url( 'css/stacktable.css', __FILE__ ) );
	wp_enqueue_style( 'stacktable-style' );

    wp_register_script( 'responsive-tabs', plugins_url( 'js/jquery.responsiveTabs.min.js', __FILE__ ), array('jquery'), '1.4.5', false );
    wp_enqueue_script( 'responsive-tabs' );

    //wp_enqueue_script( 'jquery-ui-core' );
    //wp_enqueue_script( 'jquery-ui-tabs' );
    //wp_enqueue_script( 'jquery-ui-accordion' );
}

/* ------------------------------- Backend ------------------------------------ */

/* load js and css styles in admin area */
function lptw_advanced_tables_specific_enqueue($hook_suffix) {
    $screen = get_current_screen();
    $post_type = $screen->id;
    if( ('post.php' == $hook_suffix || 'post-new.php' == $hook_suffix) && $post_type == 'table' ) {
	    wp_register_script( 'lptw-advanced-tables-script', plugins_url( 'js/advanced-tables.js', __FILE__ ), array( 'jquery', 'jquery-ui-resizable' ), true, false );
        wp_localize_script( 'lptw-advanced-tables-script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
        wp_enqueue_script('lptw-advanced-tables-script');

    	wp_register_script( 'handsontable', plugins_url( 'js/handsontable.full.min.js', __FILE__ ), array('jquery'), true, false );
        wp_enqueue_script( 'handsontable' );

    	wp_register_style( 'handsontable-style', plugins_url( 'css/handsontable.full.min.css', __FILE__ ) );
    	wp_enqueue_style( 'handsontable-style' );

    	wp_register_style( 'lptw-advanced-tables-backend-style', plugins_url( 'css/advanced-tables-backend.css', __FILE__ ) );
    	wp_enqueue_style( 'lptw-advanced-tables-backend-style' );

    	wp_register_style( 'jquery-ui-core-style', plugins_url( 'css/jquery-ui.min.css', __FILE__ ) );
    	wp_enqueue_style( 'jquery-ui-core-style' );

    	wp_register_style( 'jquery-ui-theme-style', plugins_url( 'css/jquery-ui.theme.min.css', __FILE__ ) );
    	wp_enqueue_style( 'jquery-ui-theme-style' );
    }
    elseif ( $post_type == 'table_page_shortcode_builder' ) {
	    wp_register_script( 'lptw-advanced-tables-shortcode-builder', plugins_url( 'js/advanced-tables-shortcode-builder.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '0.1', false );
        wp_enqueue_script('lptw-advanced-tables-shortcode-builder');

    	wp_register_style( 'lptw-advanced-tables-backend-style', plugins_url( 'css/advanced-tables-backend.css', __FILE__ ) );
    	wp_enqueue_style( 'lptw-advanced-tables-backend-style' );
    }
}
add_action( 'admin_enqueue_scripts', 'lptw_advanced_tables_specific_enqueue' );

/* declare custom post */
function lptw_custom_post_table() {
  $labels = array(
    'name'               => _x( 'Tables', 'post type general name', 'lptw_advanced_tables_domain' ),
    'singular_name'      => _x( 'Table', 'post type singular name', 'lptw_advanced_tables_domain' ),
    'add_new'            => _x( 'Add New', 'class', 'lptw_advanced_tables_domain' ),
    'add_new_item'       => __( 'Add New Table', 'lptw_advanced_tables_domain' ),
    'edit_item'          => __( 'Edit Table', 'lptw_advanced_tables_domain' ),
    'new_item'           => __( 'New Table', 'lptw_advanced_tables_domain' ),
    'all_items'          => __( 'All Tables', 'lptw_advanced_tables_domain' ),
    'view_item'          => __( 'View Table', 'lptw_advanced_tables_domain' ),
    'search_items'       => __( 'Search Tables', 'lptw_advanced_tables_domain' ),
    'not_found'          => __( 'No Tables found', 'lptw_advanced_tables_domain' ),
    'not_found_in_trash' => __( 'No Tables found in the Trash', 'lptw_advanced_tables_domain' ),
    'parent_item_colon'  => '',
    'menu_name'          => __( 'Tables', 'lptw_advanced_tables_domain' )
  );
  $args = array(
    'labels'        => $labels,
    'description'   => __( 'Create a tables and use them in your posts', 'lptw_advanced_tables_domain' ),
    'menu_icon'     => 'dashicons-grid-view',
    'public'        => true,
    'show_in_nav_menus' => false,
    'exclude_from_search' => true,
    'supports'      => array( 'title', 'editor', 'page-attributes', 'thumbnail' ),
    'has_archive'   => true,
  );
  register_post_type( 'table', $args );
}
add_action( 'init', 'lptw_custom_post_table' );

/* register custom post type and flush rewrite rules for custom post type url */
function lptw_flush_rewrites() {
	lptw_custom_post_table();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'lptw_flush_rewrites' );

/* flush rewrite rules after plugin deactivating */
function lptw_flush_rewrites_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'lptw_flush_rewrites_deactivate');

/* functions for encode/decode unicode table data */
function lptw_replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}
function lptw_unicode_decode($str) {
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'lptw_replace_unicode_escape_sequence', $str);
}

/* add metabox with table style */
add_action( 'add_meta_boxes', 'lptw_advanced_table_style' );
function lptw_advanced_table_style () {
    add_meta_box(
        'lptw_advanced_table_style',
        __( 'Table style', 'lptw_advanced_tables_domain' ),
        'lptw_advanced_table_style_box_content',
        'table',
        'side',
        'default'
    );
}
function lptw_advanced_table_style_box_content ( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'lptw_advanced_table_box_content_nonce' );

    $style = get_post_meta( $post->ID, 'lptw_table_style', true );

    echo '<p>
			<label for="table_style">'.__( 'Table style:', 'lptw_advanced_tables_domain' ).'</label>
			<select name="table_style" id="table_style" class="widefat">
				<option value="default"'.selected( $style, 'default', false ).'>Default</option>
				<option value="material-red"'.selected( $style, 'material-red', false ).'>Material Red</option>
				<option value="material-pink"'.selected( $style, 'material-pink', false ).'>Material Pink</option>
				<option value="material-purple"'.selected( $style, 'material-purple', false ).'>Material Purple</option>
				<option value="material-deep-purple"'.selected( $style, 'material-deep-purple', false ).'>Material Deep Purple</option>
				<option value="material-indigo"'.selected( $style, 'material-indigo', false ).'>Material Indigo</option>
				<option value="material-blue"'.selected( $style, 'material-blue', false ).'>Material Blue</option>
				<option value="material-light-blue"'.selected( $style, 'material-light-blue', false ).'>Material Light Blue</option>
				<option value="material-cyan"'.selected( $style, 'material-cyan', false ).'>Material Cyan</option>
				<option value="material-teal"'.selected( $style, 'material-teal', false ).'>Material Teal</option>
				<option value="material-green"'.selected( $style, 'material-green', false ).'>Material Green</option>
				<option value="material-light-green"'.selected( $style, 'material-light-green', false ).'>Material Light Green</option>
				<option value="material-lime"'.selected( $style, 'material-lime', false ).'>Material Lime</option>
				<option value="material-yellow"'.selected( $style, 'material-yellow', false ).'>Material Yellow</option>
				<option value="material-amber"'.selected( $style, 'material-amber', false ).'>Material Amber</option>
				<option value="material-deep-orange"'.selected( $style, 'material-deep-orange', false ).'>Material Deep Orange</option>
				<option value="material-brown"'.selected( $style, 'material-brown', false ).'>Material Brown</option>
				<option value="material-grey"'.selected( $style, 'material-grey', false ).'>Material Grey</option>
				<option value="material-blue-grey"'.selected( $style, 'material-blue-grey', false ).'>Material Blue Grey</option>
				<option value="schedule-flat-red"'.selected( $style, 'schedule-flat-red', false ).'>Schedule Flat Red</option>
				<option value="schedule-flat-pink"'.selected( $style, 'schedule-flat-pink', false ).'>Schedule Flat Pink</option>
				<option value="schedule-flat-purple"'.selected( $style, 'schedule-flat-purple', false ).'>Schedule Flat Purple</option>
				<option value="schedule-flat-light-blue"'.selected( $style, 'schedule-flat-light-blue', false ).'>Schedule Flat Light Blue</option>
				<option value="schedule-flat-dark-blue"'.selected( $style, 'schedule-flat-dark-blue', false ).'>Schedule Flat Dark Blue</option>
				<option value="schedule-flat-green"'.selected( $style, 'schedule-flat-green', false ).'>Schedule Flat Green</option>
				<option value="schedule-flat-yellow"'.selected( $style, 'schedule-flat-yellow', false ).'>Schedule Flat Yellow</option>
				<option value="schedule-flat-orange"'.selected( $style, 'schedule-flat-orange', false ).'>Schedule Flat Orange</option>
				<option value="schedule-flat-grey"'.selected( $style, 'schedule-flat-grey', false ).'>Schedule Flat Grey</option>
			</select>
		</p>';
}

/* metabox for shortcode builder */
add_action( 'add_meta_boxes', 'lptw_advanced_table_post_shortcode' );
function lptw_advanced_table_post_shortcode () {
    add_meta_box(
        'lptw_advanced_table_post_shortcode',
        __( 'Table shortcode', 'lptw_advanced_tables_domain' ),
        'lptw_advanced_table_post_shortcode_box_content',
        'table',
        'side',
        'default'
    );
}
function lptw_advanced_table_post_shortcode_box_content ( $post ) {

    $style = get_post_meta( $post->ID, 'lptw_table_style', true );
    if (empty($style)) {$style = 'default';}

    $default_shortcode = '[lptw_table id="' . $post->ID . '" style="' . $style . '"]';

    echo '<p>
			<label for="post_shortcode_table_style">'.__( 'Table style:', 'lptw_advanced_tables_domain' ).'</label>
			<select name="post_shortcode_table_style" id="post_shortcode_table_style" class="widefat">
				<option value="default"'.selected( $style, 'default', false ).'>Default</option>
				<option value="material-red"'.selected( $style, 'material-red', false ).'>Material Red</option>
				<option value="material-pink"'.selected( $style, 'material-pink', false ).'>Material Pink</option>
				<option value="material-purple"'.selected( $style, 'material-purple', false ).'>Material Purple</option>
				<option value="material-deep-purple"'.selected( $style, 'material-deep-purple', false ).'>Material Deep Purple</option>
				<option value="material-indigo"'.selected( $style, 'material-indigo', false ).'>Material Indigo</option>
				<option value="material-blue"'.selected( $style, 'material-blue', false ).'>Material Blue</option>
				<option value="material-light-blue"'.selected( $style, 'material-light-blue', false ).'>Material Light Blue</option>
				<option value="material-cyan"'.selected( $style, 'material-cyan', false ).'>Material Cyan</option>
				<option value="material-teal"'.selected( $style, 'material-teal', false ).'>Material Teal</option>
				<option value="material-green"'.selected( $style, 'material-green', false ).'>Material Green</option>
				<option value="material-light-green"'.selected( $style, 'material-light-green', false ).'>Material Light Green</option>
				<option value="material-lime"'.selected( $style, 'material-lime', false ).'>Material Lime</option>
				<option value="material-yellow"'.selected( $style, 'material-yellow', false ).'>Material Yellow</option>
				<option value="material-amber"'.selected( $style, 'material-amber', false ).'>Material Amber</option>
				<option value="material-deep-orange"'.selected( $style, 'material-deep-orange', false ).'>Material Deep Orange</option>
				<option value="material-brown"'.selected( $style, 'material-brown', false ).'>Material Brown</option>
				<option value="material-grey"'.selected( $style, 'material-grey', false ).'>Material Grey</option>
				<option value="material-blue-grey"'.selected( $style, 'material-blue-grey', false ).'>Material Blue Grey</option>
				<option value="schedule-flat-red"'.selected( $style, 'schedule-flat-red', false ).'>Schedule Flat Red</option>
				<option value="schedule-flat-pink"'.selected( $style, 'schedule-flat-pink', false ).'>Schedule Flat Pink</option>
				<option value="schedule-flat-purple"'.selected( $style, 'schedule-flat-purple', false ).'>Schedule Flat Purple</option>
				<option value="schedule-flat-light-blue"'.selected( $style, 'schedule-flat-light-blue', false ).'>Schedule Flat Light Blue</option>
				<option value="schedule-flat-dark-blue"'.selected( $style, 'schedule-flat-dark-blue', false ).'>Schedule Flat Dark Blue</option>
				<option value="schedule-flat-green"'.selected( $style, 'schedule-flat-green', false ).'>Schedule Flat Green</option>
				<option value="schedule-flat-yellow"'.selected( $style, 'schedule-flat-yellow', false ).'>Schedule Flat Yellow</option>
				<option value="schedule-flat-orange"'.selected( $style, 'schedule-flat-orange', false ).'>Schedule Flat Orange</option>
				<option value="schedule-flat-grey"'.selected( $style, 'schedule-flat-grey', false ).'>Schedule Flat Grey</option>
			</select>
		</p>';
    echo '<p><textarea rows="2" name="table_shortcode" id="table_shortcode" class="widefat">'.esc_html($default_shortcode).'</textarea></p>';
    echo '<p><a href="#" class="button button-default button-large" id="generate_shortcode">Generate Shortcode</a></p>';
}

/* Move all "advanced" metaboxes above the default editor */
/* the main "advanced" metabox - table editor */
add_action('edit_form_after_title', 'lptw_advanced_table_move_boxes_up');
function lptw_advanced_table_move_boxes_up () {
    global $post, $wp_meta_boxes;
    $screen = get_current_screen();
    $post_type = $screen->id;
    if( $post_type == 'table' ) {
        do_meta_boxes( 'table', 'normal', $post );
        unset($wp_meta_boxes[get_post_type($post)]['normal']);
    }
}

/* add price table editor metabox */
add_action( 'add_meta_boxes', 'lptw_advanced_table' );
function lptw_advanced_table () {
    add_meta_box(
        'lptw_advanced_table',
        __( 'Table content', 'lptw_advanced_tables_domain' ),
        'lptw_advanced_table_box_content',
        'table',
        'normal',
        'default'
    );
}
/* output price editor in admin area */
function lptw_advanced_table_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'lptw_advanced_table_box_content_nonce' );
    $table_json_data = get_post_meta( $post->ID, 'lptw_table_json', true );
    $table_json_meta = get_post_meta( $post->ID, 'lptw_meta_json', true );
    $table_json_totals = get_post_meta( $post->ID, 'lptw_table_totals', true );
    $cells_meta_json = get_post_meta( $post->ID, 'lptw_cells_meta_json', true );
    $first_row_header = get_post_meta( $post->ID, 'first_row_header', true );
    $table_sorter = get_post_meta( $post->ID, 'table_sorter', true );
    $table_responsive = get_post_meta( $post->ID, 'table_responsive', true );
    $responsive_type = get_post_meta( $post->ID, 'responsive_type', true );
    $totals_caption = get_post_meta( $post->ID, 'totals_caption', true );

    if ($first_row_header == 'on') {$checked_fr = 'checked="checked"';}
    else {$checked_fr = '';}
    if ($table_responsive == 'on') {$checked_tr = 'checked="checked"';}
    else {$checked_tr = '';}
    if ($table_sorter == 'on') {$checked_ts = 'checked="checked"';}
    else {$checked_ts = '';}
    if ($totals_caption == 'on') {$checked_tc = 'checked="checked"';}
    else {$checked_tc = '';}

    if ($table_json_meta == 'null' || $table_json_meta == '') {$table_json_meta = 'true';}
    else {
        $table_json_meta = str_replace('"', "", $table_json_meta);
        $table_json_meta = str_replace("'", "", $table_json_meta);
    }

    $cells_meta = json_decode($cells_meta_json);
    $cells_js_str = '';
    $i = 0;
    if (!empty($cells_meta)) {
        foreach ($cells_meta as $cell_row) {
            $cell_str = '{';
            foreach ($cell_row as $cell_key => $cell_value) {
                if ($cell_key == 'row') {$cell_str .= 'row:'.$cell_value.', ';}
                if ($cell_key == 'col') {$cell_str .= 'col:'.$cell_value.', ';}
                if ($cell_key == 'className') {$cell_str .= 'className:"'.$cell_value.'"';}
            }
            $cell_str .= '}';
            $cells_js_str .= $cell_str;
            $i++;
            if ($i < count($cells_meta)) {$cells_js_str .= ',';}
        }
        $cells_js_str = '[' . $cells_js_str . ']';
    } else {$cells_js_str = 'null';}

	echo '<div class="lptw-checkbox-container">';
    echo '<div class="lptw-input-wrapper"><label class="lptw-checkbox-label" for="table_responsive"><input class="checkbox" type="checkbox" '.$checked_tr.' id="table_responsive" name="table_responsive" />&nbsp;'.__( 'Responsive table', 'lptw_advanced_tables_domain' ).'</label></div>';
    echo '<div class="lptw-input-wrapper"><label class="lptw-checkbox-label" for="responsive_type">'.__( 'Responsive Layout type', 'lptw_advanced_tables_domain' ).':&nbsp;
        <select id="responsive_type" name="responsive_type">
            <option '.selected( $responsive_type, "table", false).' value="table">Table style</option>
            <option '.selected( $responsive_type, "columns", false).' value="columns">Agenda style</option>
            <option '.selected( $responsive_type, "cards", false).' value="cards">Cards style</option>
        </select></label></div>';
    echo '</div>';
	echo '<div class="lptw-checkbox-container">';
    echo '<div class="lptw-input-wrapper"><label class="lptw-checkbox-label" for="first_row_header"><input class="checkbox" type="checkbox" '.$checked_fr.' id="first_row_header" name="first_row_header" />&nbsp;'.__( 'Display first row as header', 'lptw_advanced_tables_domain' ).'</label></div>';
	echo '<div class="lptw-input-wrapper"><label class="lptw-checkbox-label" for="table_sorter"><input class="checkbox" type="checkbox" '.$checked_ts.' id="table_sorter" name="table_sorter" />&nbsp;'.__( 'Sort table in web site', 'lptw_advanced_tables_domain' ).'</label></div>';
	echo '<div class="lptw-input-wrapper"><label class="lptw-checkbox-label" for="totals_caption"><input class="checkbox" type="checkbox" '.$checked_tc.' id="totals_caption" name="totals_caption" />&nbsp;'.__( 'Show caption for Totals', 'lptw_advanced_tables_domain' ).'</label></div>';
    echo '</div>';
    echo '<div id="lptw-media-button-container"><a id="insert-media-button" class="button insert-media" href="#"><span class="dashicons dashicons-admin-media"></span>&nbsp;'.__( 'Add Media to the selected cell', 'lptw_advanced_tables_domain' ).'</a>&nbsp;<span id="insert-media-button-message"></span></div>';
    echo '<div id="lptw-table-data" class="handsontable"></div>';
    echo '<div id="lptw-table-message"></div>';
    echo '<div id="lptw-table-storage" style="display:none;"></div>';
    $ajax_url = admin_url( 'admin-ajax.php' );
    $table_json_data = str_replace('null','\"\"', $table_json_data);
    //$table_json_data = preg_replace('/[^\x00-\x09]+/', '', $table_json_data);
    //$table_json_data = preg_replace('/[^\x0B-\x0C]+/', '', $table_json_data);
    //$table_json_data = preg_replace('/[^\x00-\x09]+/', '', $table_json_data);

    $is_unescaped_quotes = preg_match('/(?<!\\\)"/', $table_json_data);

            /* special for http://qrcenter.me with magic_quotes_gpc = On */
            if (strpos($table_json_data, '="') !== false) {
                $table_json_data = preg_replace('/="(.*?)"/i', '=&quot;$1&quot;', $table_json_data);
                $table_json_data = preg_replace("/='/i", '&#39;', $table_json_data);
            }


    //if (strpos($table_json_data, '&quot;') !== false ) {
    if ($is_unescaped_quotes == 0) {
        $table_json_data = stripcslashes($table_json_data);
        $table_json_data = str_replace('&quot;', '\"', $table_json_data);
        $table_json_data = str_replace('&#39;', "\'", $table_json_data);
    } else if ($is_unescaped_quotes == 1) {
        $table_json_data = preg_replace('/\\\"/i', '&quot;', $table_json_data);
        $table_json_data = preg_replace("/\\\'/i", '&#39;', $table_json_data);
    }
    //$table_json_data = html_entity_decode ($table_json_data, ENT_QUOTES);

    echo '
<script>
    function getTableData() {
        return '.$table_json_data.';
    }

    function getTableMeta() {
        return '.$table_json_meta.';
    }

    function getTableTotals() {
        return '.$table_json_totals.';
    }

    function getCellsMeta() {
        return '.$cells_js_str.';
    }
</script>'."\n";
}

/* wp_ajax_ - only for registered users */
/* save table data */
function lptw_save_table_data_json() {
    $post_id = $_POST['post_id'];
    $lptw_table_json = $_POST['data'];

    //if (get_magic_quotes_gpc() == 1) {$lptw_table_json = str_replace("\\\\", "", $lptw_table_json);}
    $lptw_table_json = stripcslashes($lptw_table_json);
    $lptw_table_json = json_encode($lptw_table_json);
    $lptw_table_json = lptw_unicode_decode($lptw_table_json);
    $lptw_table_json = substr($lptw_table_json, 1);
    $lptw_table_json = substr($lptw_table_json, 0, -1);
    $lptw_table_json = str_replace('null','\"\"', $lptw_table_json);
    update_post_meta($post_id, 'lptw_table_json', wp_slash($lptw_table_json));
    echo 'ok';
	die();
}
add_action('wp_ajax_save_table_data', 'lptw_save_table_data_json'); // wp_ajax_{action}

/* save table metadata - spanned rows and cols */
function lptw_save_table_meta_json() {
    $post_id = $_POST['post_id'];
    $data = $_POST['data'];

    $lptw_meta_json = json_encode($data);

    update_post_meta($post_id, 'lptw_meta_json', $lptw_meta_json);
    echo 'ok';
	die();
}
add_action('wp_ajax_save_table_meta', 'lptw_save_table_meta_json'); // wp_ajax_{action}

/* save cells metadata - aligments */
function lptw_save_cells_meta_json() {
    $post_id = $_POST['post_id'];
    $data = $_POST['data'];

    $lptw_cells_meta_json = json_encode($data);

    update_post_meta($post_id, 'lptw_cells_meta_json', $lptw_cells_meta_json);
    echo 'ok';
	die();
}
add_action('wp_ajax_save_cells_meta', 'lptw_save_cells_meta_json'); // wp_ajax_{action}

/* save post data */
function lptw_advanced_table_box_save ( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
  return;

  if ($_POST && !empty($_POST['lptw_advanced_table_box_content_nonce'])) {

      if ( !wp_verify_nonce( $_POST['lptw_advanced_table_box_content_nonce'], plugin_basename( __FILE__ ) ) )
      return;
  }

  if ( $_POST && 'table' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
    return;

    if ($_POST && !empty($_POST['totals'])) {
        $lptw_table_totals = $_POST['totals'];
        if (!empty($lptw_table_totals)) {
          $totals2json = Array();
          foreach ($lptw_table_totals as $total_id) {
              $totals2json[] = (int) $total_id;
          }
        $lptw_table_totals_json = json_encode($totals2json);
        }
    } else { $lptw_table_totals_json = ''; }
    update_post_meta( $post_id, 'lptw_table_totals', $lptw_table_totals_json );

    if ($_POST && !empty($_POST['first_row_header'])) {
        $first_row_header = $_POST['first_row_header'];
    } else { $first_row_header = ''; }
    update_post_meta( $post_id, 'first_row_header', $first_row_header );

    if ($_POST && !empty($_POST['totals_caption'])) {
        $totals_caption = $_POST['totals_caption'];
    } else { $totals_caption = ''; }
    update_post_meta( $post_id, 'totals_caption', $totals_caption );

    if ($_POST && !empty($_POST['table_sorter'])) {
        $table_sorter = $_POST['table_sorter'];
    } else { $table_sorter = ''; }
    update_post_meta( $post_id, 'table_sorter', $table_sorter );

    if ($_POST && !empty($_POST['table_responsive'])) {
        $table_responsive = $_POST['table_responsive'];
    } else { $table_responsive = ''; }
    update_post_meta( $post_id, 'table_responsive', $table_responsive );

    if ($_POST && !empty($_POST['responsive_type'])) {
        $responsive_type = $_POST['responsive_type'];
    } else { $responsive_type = ''; }
    update_post_meta( $post_id, 'responsive_type', $responsive_type );

    if ($_POST && !empty($_POST['table_style'])) {
        $table_style = $_POST['table_style'];
    } else { $table_style = ''; }
    update_post_meta( $post_id, 'lptw_table_style', $table_style );

  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
    return;
  }
}
add_action( 'save_post', 'lptw_advanced_table_box_save' );

/* Shortcode Builder for the Tabs */

function lptw_tables_shortcode_builder () {
    echo '<div class="wrap"><h2>' . _x('Shortcode Builder for the Tables', 'Shortcode Builder', 'lptw_advanced_tables_domain') . '</h2>';
    echo '<table id="lptw-tabs-shortcode-builder" class="form-table">
        <tbody>
            <tr>
                <td colspan="2">
                <div class="lptw-dropzone-wrapper">
                    <h3 class="tab-header">' . _x('Your Tables', 'Shortcode Builder', 'lptw_advanced_tables_domain') . '</h3>';
    $args = array (
        'post_type' => 'table',
        'posts_per_page' => '-1',
        'orderby' => 'date',
        'order' => 'DESC'
        );

    // The Query
    $the_query = new WP_Query( $args );

    // The Loop
    if ( $the_query->have_posts() ) {
        echo '<div id="lptw-source-tables">';
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            echo '<div id="'. get_the_ID() .'" class="lptw-tab-header">' . get_the_title() . '</div>';
        }
        echo '</div>';
    } else {
        _ex('Please create at least one Table!', 'Shortcode Builder', 'lptw_advanced_tables_domain');
    }
    /* Restore original Post Data */
    wp_reset_postdata();

    echo '          </div>
                    <div class="lptw-dropzone-wrapper">
                        <h3 class="tab-header">' . _x('Tables in the Shortcode', 'Shortcode Builder', 'lptw_advanced_tables_domain') . '</h3>
                        <div id="lptw-destination-tables"></div>';
    echo '          </div>
                </td>
            </tr>
            <tr>
                <th scope="row">' . _x('Shortcode:', 'Shortcode Builder', 'lptw_advanced_tables_domain') . '</th>
                <td id="result">
                    <a href="#" class="button button-default button-large" id="lptw_generate_shortcode_tables">Generate Shortcode</a>
                    <div class="lptw-sb-row">
                        <textarea name="lptw_generate_shortcode_tables_result" id="lptw_generate_shortcode_tables_result" class="lptw-sb-result"></textarea>
                    </div>
                </td>
            </tr>
        </tbody>
        </table>';
    echo '</div>';
}
add_action('admin_menu', 'lptw_register_tables_shortcode_builder_menu');
function lptw_register_tables_shortcode_builder_menu() {
    add_submenu_page( 'edit.php?post_type=table', __('Shortcode Builder for the Tabs', 'lptw_advanced_tables_domain'), __('Shortcode Builder', 'lptw_advanced_tables_domain'), 'manage_options', 'shortcode_builder', 'lptw_tables_shortcode_builder');
}

/* -------------------------------- Frontend ---------------------------------- */
/* get single table template */
function lptw_get_advanced_table_single_template($single_template) {
     global $post;

     if ($post->post_type == 'table') {
          $single_template = dirname( __FILE__ ) . '/templates/table-single.php';
     }
     return $single_template;
}
add_filter( 'single_template', 'lptw_get_advanced_table_single_template' );

/* get all tables template with tabs */
function lptw_get_advanced_table_archive_template($archive_template) {
     global $post;

     if ($post->post_type == 'table') {
          $archive_template = dirname( __FILE__ ) . '/templates/archive-tables.php';
     }
     return $archive_template;
}
add_filter( 'archive_template', 'lptw_get_advanced_table_archive_template' );

/*
 * Frontend multiple tables output
 * Works on shortcode [lptw_tabs]
 */
function lptw_advanced_tabs_shortcode ( $atts ) {
    $a = shortcode_atts( array(
        'ids' => 0
    ), $atts );

    if ($a['ids'] != 0) {

        $shortcode = '<div id="lptw-tabs-wrapper">';
        $shortcode .= '<div id="lptw-tables-responsive-tabs">';
        $shortcode .= '<ul>'."\n";

        $post_array = explode(',', $a['ids']);

        $tabs = 1;
        foreach ($post_array as $post_id) {
            $tabs_headers = get_post( $post_id, 'OBJECT', 'display' );
            $shortcode .= '<li><a href="#tabs-'.$tabs.'">' . $tabs_headers->post_title . '</a></li>'."\n";
            $tabs++;
        }

        $shortcode .= '</ul>'."\n";

        reset($post_array);
        $tabs_count = 1;
        foreach ($post_array as $post_id) {
            $shortcode .= '<div id="tabs-'.$tabs_count.'">';
            $style = get_post_meta( $post_id, 'lptw_table_style', true );
            $shortcode .= lptw_advanced_table_json2html ( $post_id, $style );
            $shortcode .= '</div>';
            $tabs_count++;
        }

        $shortcode .= '</div><!-- #lptw-tabs-wrapper -->';
        $shortcode .= '</div><!-- #tabs -->';

    }
    else {
        $shortcode = 'At least one of the Table ID is required!';
    }
    return $shortcode;
}
add_shortcode( 'lptw_tabs', 'lptw_advanced_tabs_shortcode' );



/*
 * Frontend table output
 * Works on shortcode [lptw_table]
 */
function lptw_advanced_tables_shortcode ( $atts ) {
    $a = shortcode_atts( array(
        'id'        => 0,
        'style'     => 'default'
    ), $atts );

    if ($a['id'] != 0) {
        $shortcode = lptw_advanced_table_json2html ($a['id'], $a['style']);
    }
    else {
        $shortcode = 'Table ID is required value!';
    }
    return $shortcode;
}
add_shortcode( 'lptw_table', 'lptw_advanced_tables_shortcode' );

/* check is cell merged */
function lptw_check_cell_meta ($meta_array, $row, $col) {
    foreach ($meta_array as $meta_string) {
        if ($row == $meta_string['row'] && $col == $meta_string['col']) {
            $result = Array($meta_string['rowspan'], $meta_string['colspan']);
        }
    }
    return $result;
}
/* check cell aligment */
function lptw_check_cell_class ($class_array, $row, $col) {
    foreach ($class_array as $class_string) {
        if ($row == $class_string['row'] && $col == $class_string['col']) {
            $result = $class_string['className'];
        }
    }
    return $result;
}
/* if cell going after merged - don't show it */
function lptw_check_cell_span ($span_array, $row, $col) {
    $result = false;
    foreach ($span_array as $span_string) {
        if ($row == $span_string['row'] && $col == $span_string['col']) {
            $result = true;
        }
    }
    return $result;
}

/* displays stored table data table as html */
function lptw_advanced_table_json2html ($post_id, $style) {
        $table_json_data = get_post_meta( $post_id, 'lptw_table_json', true );
        $table_json_meta = get_post_meta( $post_id, 'lptw_meta_json', true );
        $first_row_header = get_post_meta( $post_id, 'first_row_header', true );
        $table_sorter = get_post_meta( $post_id, 'table_sorter', true );
        $table_responsive = get_post_meta( $post_id, 'table_responsive', true );

        /* get responsive type and set as 'table' if empty - support old versions */
        $responsive_type = get_post_meta( $post_id, 'responsive_type', true );
        if ( $responsive_type == '' ) { $responsive_type = 'table'; }

        $totals_caption = get_post_meta( $post_id, 'totals_caption', true );
        $table_json_class = get_post_meta( $post_id, 'lptw_cells_meta_json', true );
        $table_json_totals = get_post_meta( $post_id, 'lptw_table_totals', true );

        $meta_array = json_decode($table_json_meta, true);
        $class_array = json_decode($table_json_class, true);
        $totals_array = json_decode($table_json_totals, true);

        $pos_schedule = strpos($style, 'schedule');
        if ($pos_schedule === false) { $is_schedule = 'false'; }
        else { $is_schedule = 'true'; }

        $pos_material = strpos($style, 'material');
        if ($pos_material === false) { $is_material = 'false'; }
        else { $is_material = 'true'; }


        if (!empty($meta_array)) {
            $span_array = Array();
            $ct = 0;
            foreach ($meta_array as $meta_string) {
                $rowspan = (int) $meta_string['rowspan'];
                $colspan = (int) $meta_string['colspan'];
                if ($rowspan > 1) {
                    for ($i = 1; $i < $rowspan; $i++) {
                        $span_array[$ct]['row'] = (int) $meta_string['row'] + $i;
                        $span_array[$ct]['col'] = (int) $meta_string['col'];
                        $ct++;
                    }
                }
                if ($colspan > 1) {
                    for ($t = 1; $t < $colspan; $t++) {
                        $span_array[$ct]['row'] = (int) $meta_string['row'];
                        $span_array[$ct]['col'] = (int) $meta_string['col'] + $t;
                        $ct++;
                    }
                }
            }
        } else {$span_array = Array();}

        if (!empty($table_json_data)) {
            if ($is_material == 'true') { $table_type = 'table-material'; }
            elseif ($is_schedule == 'true') { $table_type = 'table-schedule'; }

            if (strpos($table_json_data, '=\"') !== false) {
                $table_json_data = preg_replace('/\\\"/i', '&quot;', $table_json_data);
                $table_json_data = preg_replace("/\\\'/i", '&#39;', $table_json_data);
            }

            /* special for http://qrcenter.me with magic_quotes_gpc = On */
            if (strpos($table_json_data, '="') !== false) {
                $table_json_data = preg_replace('/="(.*?)"/i', '=&quot;$1&quot;', $table_json_data);
                $table_json_data = preg_replace("/='/i", '&#39;', $table_json_data);
            }

            /*
            $table_json_data = str_replace('\\\'', "&#39;", $table_json_data);
            $table_json_data = str_replace('\\\\\"', '&quot;', $table_json_data);
            */

            $table_json_data = stripcslashes($table_json_data);

            $table_arr = json_decode($table_json_data);
            $table_json_data = str_replace('&#39;', "'", $table_json_data);
            $table_json_data = str_replace('&quot;', '"', $table_json_data);

            $content = '<div class="lptw-advanced-table '.$table_type.'">';
            if ( !empty($table_arr) ) {
                $content .= '<table class="'.$style.'" id="table-'.$post_id.'"';
                if ($first_row_header == 'on') {$content .= '><thead>';}
                else {$content .= '><tbody>';}
                $row = 0;
                if (!empty($totals_array)) { $totals = Array(); }
                foreach ($table_arr as $table_row) {
                    if ($first_row_header == 'on') {$td = 'th';}
                    else {$td = 'td';}
                    $content .= '<tr>';
                    $col = 0;
                    foreach ($table_row as $table_cell) {
                        if ( lptw_check_cell_span($span_array, $row, $col) == false ) {
                            if (!empty($meta_array)) {list ($rowspan, $colspan) = lptw_check_cell_meta ($meta_array, $row, $col);}
                            if (!empty($class_array)) {$class = lptw_check_cell_class ($class_array, $row, $col);}
                            if ($is_schedule == 'true' && $col == 0) {$class .= ' first-col';}
                            if ($is_schedule == 'true' && $col > 0 && !empty($table_cell)) {$class .= ' content';}
                            $content .= '<'.$td;
                            if (!empty($class)) {$content .= ' class="'.trim($class).'"';}
                            if ($rowspan > 1) {$content .= ' rowspan="'.$rowspan.'"';}
                            if ($colspan > 1) {$content .= ' colspan="'.$colspan.'"';}
                            $content .= '>'.html_entity_decode(nl2br($table_cell), ENT_QUOTES).'</'.$td.'>'."\n";
                            if (!empty($totals_array) && in_array($col, $totals_array)) {
                                if (is_numeric($table_cell)) { $totals[$col] = $totals[$col] + $table_cell; }
                                }
                        }
                        $col++;
                    }
                    $content .= '</tr>';
                    if ($first_row_header == 'on') {$content .= '</thead><tbody>';}
                    $first_row_header = '';
                    $row++;
                }
                if (!empty($totals)) {
                    $content .= '<tfoot>';
                    if ($totals_caption == 'on') {$content .= '<tr class="totals-caption"><td colspan="'.$col.'">'._x('Totals:', 'Frontend table', 'lptw_advanced_tables_domain').'</td></tr>';}
                    $content .= '<tr>';
                    for ($ct = 0; $ct < $col; $ct++) {
                        $content .= '<td class="totals">'.$totals[$ct].'</td>';
                    }
                    $content .= '</tr></tfoot>';
                }
                $content .= '</tbody></table>';
            } else {
                echo '<p>A problem with a table data is occurs, maybe you or somebody else delete it from database. Or something get wrong. Please delete the table in WordPress Dashboard and create it again. Or contact the plugin developer.</p>'."\n";
                echo '<pre>';
                var_dump($table_json_data);
                echo '</pre>';
            }
            $content .= '</div>';

            if ($table_sorter == 'on') {$content .= '<script>
                jQuery(document).ready(function($) {
                    $("#table-'.$post_id.'").tablesorter();
                });
                </script>';
            }

            if ($table_responsive == 'on') {
                switch ($responsive_type) {
                    case 'table':
                        $content .= '<script>
                        jQuery(document).ready(function($) {
                            $("#table-'.$post_id.'").stacktable({ myClass:"stacktable small-only" });
                        });
                        </script>';
                    break;
                    case 'columns':
                        $content .= '<script>
                        jQuery(document).ready(function($) {
                            $("#table-'.$post_id.'").stackcolumns({ myClass:"stacktable small-only" });
                        });
                        </script>';
                    break;
                    case 'simple':
                        $content .= '<script>
                        jQuery(document).ready(function($) {
                        $("#table-'.$post_id.'").stacktable({hideOriginal:true});
                        });
                        </script>';
                    break;
                    case 'cards':
                        $content .= '<script>
                        jQuery(document).ready(function($) {
                        $("#table-'.$post_id.'").cardtable({ myClass:"stacktable small-only" });
                        });
                        </script>';
                    break;
                }
            }
        }
    return $content;
}

?>