<?php 
/*
Plugin Name: Easy Digital Downloads - HubSpot integration
Plugin URI: https://easydigitaldownloads.com/extension/hubspot-integration
Description: Hubspot lead integration collects helps automatically customer information and input it into Hubspot.com's client management database.
Version: 1.0.1
Author: Hudson Atwell
Author URI: http://www.hudsonatwell.co
*/

/* 
---------------------------------------------------------------------------------------------------------
- Define constants & include core files
---------------------------------------------------------------------------------------------------------
*/ 

define( 'EDD_HUBSPOT_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
define( 'EDD_HUBSPOT_API', 'https://easydigitaldownloads.com' );
define( 'EDD_HUBSPOT_NAME', 'Hubspot Integration' );
define( 'EDD_HUBSPOT_SLUG', plugin_basename( dirname(__FILE__) ) );
define( 'EDD_HUBSPOT_PATH', plugin_dir_path( __FILE__ ) );
define( 'EDD_HUBSPOT_FILE', __FILE__ );
define( 'EDD_HUBSPOT_VERSION_NUMBER', '1.0.1' );


/* load core files */
switch (is_admin()) :
	case true : 
		/* loads admin files */	
		include_once('modules/module.extension-setup.php');
		include_once('modules/module.settings.php');
		
		BREAK;
	case false :
		/* loads frontend files */				
		include_once('modules/module.add-subscriber.php');
		
		BREAK;
endswitch;


