<?php

if( ! class_exists( 'EDD_License' ) )
	include_once ( EDD_HUBSPOT_PATH . 'includes/EDD_License_Handler.php' );
		
$license = new EDD_License( EDD_HUBSPOT_FILE , 'HubSpot Integration', EDD_HUBSPOT_VERSION_NUMBER, 'Hudson Atwell' );
	