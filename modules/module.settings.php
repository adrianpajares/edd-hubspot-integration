<?php

/* ADD SETTINGS TO MISC TAB */
add_filter( 'edd_settings_extensions', 'edd_hubspot_add_settings' );
function edd_hubspot_add_settings( $settings ) {
  
  
	$edd_hubspot_settings = array(
		array(
			'id'   => 'edd_hubspot_header',
			'name' => '<strong>' . __( 'HubSpot Integration', 'edd' ) . '</strong>',
			'desc' => __( 'Configure HubSpot Integration Settings', 'edd' ),
			'type' => 'header',
		),
		array(
			'id'   => 'edd_hubspot_portal_id',
			'name' =>  __( 'HubSpot Portal ID', 'edd' ),
			'desc' => __( '<br><i>Your Hub Portal ID can be found in the footer of your hubspot account.</i>', 'edd' ),
			'type' => 'text',
			'default' => ''
		),
		array(
			'id'   => 'edd_hubspot_api_key',
			'name' =>  __( 'HubSpot API Key', 'edd' ),
			'desc' => __( '<br><i>Get your HubSpot API Key at https://app.hubspot.com/keys/get.</i>', 'edd' ),
			'type' => 'text',
			'default' => ''
		),
		array(
			'id'   => 'edd_hubspot_list_name_prefix',
			'name' =>  __( 'List Prefix', 'edd' ),
			'desc' => __( '<br><i>This prefix will automatically be added to generated lists.</i>', 'edd' ),
			'type' => 'text',
			'default' => '[edd] '
		)
	);
	

	return array_merge( $settings, $edd_hubspot_settings );
}
