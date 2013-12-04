<?php


/* ADD SUBSCRIBER TO HUBSPOT ON CHECKOUT */

add_action('edd_complete_purchase','edd_hubspot_integration_checkout');	
function edd_hubspot_integration_checkout($payment_id)
{	

	/* get hubspot api key */
	$edd_settings = get_option('edd_settings');

	if (!isset($edd_settings['edd_hubspot_api_key']))
		return $purchase_data;
	
	
	
	require_once EDD_HUBSPOT_PATH.'includes/haPiHP-master/class.properties.php';
	require_once EDD_HUBSPOT_PATH.'includes/haPiHP-master/class.lists.php';
	require_once EDD_HUBSPOT_PATH.'includes/haPiHP-master/class.contacts.php';
	require_once EDD_HUBSPOT_PATH.'includes/haPiHP-master/class.exception.php';
	
	$payment_meta = edd_get_payment_meta( $payment_id );
	
	$user_data = unserialize( $payment_meta['user_info'] );
	$cart_data = unserialize( $payment_meta['cart_details'] );
	

	
	$properties = new HubSpot_Properties($edd_settings['edd_hubspot_api_key'] , $edd_settings['edd_hubspot_portal_id']);
	$contacts = new HubSpot_Contacts($edd_settings['edd_hubspot_api_key'] , $edd_settings['edd_hubspot_portal_id']);
	$lists = new HubSpot_Lists($edd_settings['edd_hubspot_api_key'] ,  $edd_settings['edd_hubspot_portal_id'] );

	/* create property in hubspot if it hasn't been created yet. */
	$property_exists = get_option('edd_hubspot_property_added_'.$edd_settings['edd_hubspot_api_key']);
	
	if (!$property_exists)
	{
		$property_info  = array(
				'label'=>'EDD Customer',
				'name'=>'edd_checkout',
				'description'=>'Easy Digital Downloads Customer',
				'groupName'=>'conversioninformation',
				'type'=>'string',
				'fieldType'=>'text',
				'formField'=>'false',
				'displayOrder'=>0 
			  );
	   
		$property_info = apply_filters('edd_hubspot_property_info',$property_info);
	   
		$new_prop = $properties->create_property('edd_checkout',$property_info);

		if (property_exists( $new_prop, 'name') )
		{
			update_option('edd_hubspot_property_added_'.$edd_settings['edd_hubspot_api_key'] ,true);
		}
	}

	/*check if contact exists */
    $contact = $contacts->get_contact_by_email($user_data['email']);
    if (isset($contact->vid))
	{
		/* get contact id */
		$contact_id = $contact->vid;
    }
	else
	{		
		
		/* create contact */
		$lead_data = array('email'=> $user_data['email'],
						'firstname'=> $user_data['first_name'],
						'lastname'=> $user_data['last_name'],
						'edd_checkout' => 'true'
						);
		
		$lead_data = apply_filters('edd_hubspot_lead_data',$lead_data);
		
		$createdContact = $contacts->create_contact($lead_data);
		$contact_id = $createdContact->{'vid'};
	}
	
	
	/* loop through cart and add lead to item lists */
	foreach ($cart_data as $item)
	{
		/* check if list exists */
		$hubspot_list_id  = null;
		$static_lists = $lists->get_static_lists($hubspot_list_id);
		
		foreach ($static_lists->lists as $list)
		{

			if (stristr($list->name , $item['name']) )
			{
				$hubspot_list_id  = $list->listId;
				break;
			}
		}

		if (!$hubspot_list_id)
		{
			/* create a contact list */
			$list_data = array(
							'name'=> $edd_settings['edd_hubspot_list_name_prefix'].' '.$item['name'],
							'dynamic'=> false ,
							'portalId'=> $edd_settings['edd_hubspot_portal_id']
							);
			
			$list_data = apply_filters('edd_hubspot_list_data', $list_data);
			
			$new_list = $lists->create_list($list_data);

			if ( !property_exists( $new_list , 'listId') )
			{
				/*
				print_r($edd_settings);
				echo $edd_settings['edd_hubspot_api_key'];
				var_dump ($new_list);
				echo "<br>";
				*/
			}
				
			$hubspot_list_id = $new_list->{'listId'};
			
			
			update_post_meta( $item['id'] , 'edd_hubspot_list_id' , $hubspot_list_id );
		}
		
		/*
		echo "contact id : $contact_id <br>";
		echo "list id : $hubspot_list_id";exit;
		*/
		
		/* add contact to list */
		$contacts_to_add = array($contact_id);
        $added_contacts = $lists->add_contacts_to_list( $contacts_to_add , $hubspot_list_id );

	}

}