<?php
if ( ! function_exists( 'lambda_theme_activate' ) ) {
	function lambda_theme_activate() {
	
		global $wpdb, $blog_id;
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		//multisite installation
		if($blog_id != 1 && is_multisite() ){
			$table_prefix = $wpdb->base_prefix . $blog_id."_";
		} else {
			$table_prefix = $wpdb->base_prefix;
		}
		
		//creating the like post table on activating the theme
		$lambda_like_post_table_name = $table_prefix . "lambda_like_post";
		
		if($wpdb->get_var("show tables like '$lambda_like_post_table_name'") != $lambda_like_post_table_name) {
			$sql = "CREATE TABLE " . $lambda_like_post_table_name . " (
					`id` bigint(11) NOT NULL AUTO_INCREMENT,
					`post_id` int(11) NOT NULL,
					`likeit` int(4) NOT NULL,
					`date_time` datetime NOT NULL,
					`ip` varchar(20) NOT NULL,
					`user_id` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`)
			)";
		dbDelta($sql);
		}
		
			
		//creating the slider manager table on activating the theme
		$table_lambda_sliders = $table_prefix . "lambda_sliders"; 
		if($wpdb->get_var("show tables like '$table_lambda_sliders'") != $table_lambda_sliders) {
			$sql = "CREATE TABLE " . $table_lambda_sliders . " (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  option_name VARCHAR(255) NOT NULL DEFAULT  'lambda_slider_options',
				  slidertype VARCHAR(255) NOT NULL DEFAULT  'lambda_slider_type',
				  active tinyint(1) NOT NULL DEFAULT  '0',
				  PRIMARY KEY (`id`),
				  UNIQUE ( `option_name` )
			)";
		dbDelta($sql);
		}
		
		
		//creating the table manager table on activating the theme	
		$table_lambda_tables = $table_prefix . "lambda_tables"; 
		if($wpdb->get_var("show tables like '$table_lambda_tables'") != $table_lambda_tables) {
			$sql = "CREATE TABLE " . $table_lambda_tables . " (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  table_name VARCHAR(255) NOT NULL DEFAULT  'lambda_table_options',
				  PRIMARY KEY (`id`),
				  UNIQUE ( `table_name` )
			)";
		dbDelta($sql);
		}
		
				
		//create a few needed options
		add_option('lambda_media_counter');
		add_option('lambda_version');
		add_option('lambdacopyright');
		add_option('lambdacopyrightlink');

		//insert values for the created options
		update_option('lambda_version', '2.1');
		update_option('lambdacopyright', 'UnitedThemes');
		update_option('lambdacopyrightlink', 'https://www.unitedthemes.com/');

		
	}
	wp_register_theme_activation_hook('Nevada', 'lambda_theme_activate');
}

function lambda_theme_deactivate() {




}
wp_register_theme_deactivation_hook('Nevada', 'lambda_theme_deactivate');

/**
 *
 * @desc registers a theme activation hook
 * @param string $code : Code of the theme. This can be the base folder of your theme. Eg if your theme is in folder 'mytheme' then code will be 'mytheme'
 * @param callback $function : Function to call when theme gets activated.
 */
function wp_register_theme_activation_hook($code, $function) {
    $optionKey="theme_is_activated_" . $code;
    if(!get_option($optionKey)) {
        call_user_func($function);
        update_option($optionKey , 1);
    }
}

/**
 * @desc registers deactivation hook
 * @param string $code : Code of the theme. This must match the value you provided in wp_register_theme_activation_hook function as $code
 * @param callback $function : Function to call when theme gets deactivated.
 */
function wp_register_theme_deactivation_hook($code, $function) {
    // store function in code specific global
    $GLOBALS["wp_register_theme_deactivation_hook_function" . $code] = $function;

    // create a runtime function which will delete the option set while activation of this theme and will call deactivation function provided in $function
    $fn = function($theme) use ($code) {
        call_user_func($GLOBALS["wp_register_theme_deactivation_hook_function" . $code]);
        delete_option("theme_is_activated_" . $code);
    };

    // add above created function to switch_theme action hook. This hook gets called when admin changes the theme.
    // Due to wordpress core implementation this hook can only be received by currently active theme (which is going to be deactivated as admin has chosen another one.
    // Your theme can perceive this hook as a deactivation hook.
    add_action("switch_theme", $fn);
}

#-----------------------------------------------------------------
# Include the TGM_Plugin_Activation class
#-----------------------------------------------------------------
require_once('plugin-activation.php');

#-----------------------------------------------------------------
# Run plugin requirements
#-----------------------------------------------------------------
add_action( 'tgmpa_register', 'lambda_register_required_plugins' );

function lambda_register_required_plugins() {

	$plugins = array(

		array(
            'name'     				=> 'Revolution Slider',
            'slug'     				=> 'revslider',
            'source'   				=> THEME_DOCUMENT_ROOT . '/inc/lib/files/revslider.zip', 
            'required' 				=> true, 
            'version' 				=> '5.2.4', 
        ),
		
		array(
			'name'     				=> 'UnitedThemes Twitter',
			'slug'     				=> 'ut-twitter',
			'source'   				=> get_template_directory_uri() . '/functions/lib/ut-twitter.zip',
            'required' 				=> true,
			'version' 				=> '1.2', 
		)
		
	);
	 
	$config = array(
        'default_path' 		=> '',                         	/* Default absolute path to pre-packaged plugins */
        'menu'         		=> 'install-required-plugins', 	/* Menu slug */
        'has_notices'      	=> true,                       	/* Show admin notices or not */
        'is_automatic'    	=> true,					   	/* Automatically activate plugins after installation or not */
    );

	tgmpa( $plugins, $config );

}