<?php


/* ADD SUBSCRIBER TO HUBSPOT ON CHECKOUT */

add_filter('edd_purchase_data_before_gateway','edd_hubspot_integration_checkout');	
function edd_hubspot_integration_checkout($data)
{	

	/* get hubspot api key */
	$edd_settings = get_option('edd_settings');

	if (!isset($edd_settings['edd_hubspot_api_key']))
		return $data;
	
	//print_r($edd_settings);
	
	require_once EDD_HUBSPOT_PATH.'includes/haPiHP-master/class.lists.php';
	require_once EDD_HUBSPOT_PATH.'includes/haPiHP-master/class.contacts.php';
	require_once EDD_HUBSPOT_PATH.'includes/haPiHP-master/class.exception.php';
	
	$user_data = $data['user_info'];
	$cart_data = $data['cart_details'];
	
	$contacts = new HubSpot_Contacts($edd_settings['edd_hubspot_api_key'] , $edd_settings['edd_hubspot_portal_id']);
	$lists = new HubSpot_Lists($edd_settings['edd_hubspot_api_key'] ,  $edd_settings['edd_hubspot_portal_id'] );

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
						'lastname'=> $user_data['last_name']
						);
		
		$lead_data = apply_filters('edd_hubspot_lead_data',$lead_data);
		
		$createdContact = $contacts->create_contact($lead_data);

		$contact_id = $createdContact->{'vid'};
	}
	
	echo "contact id: $contact_id <br>";
	
	/* loop through cart and add lead to item lists */
	foreach ($cart_data as $item)
	{
		/* check to see if hubspot list id exists for download already */
		$hubspot_list_id = get_post_meta( $item['id'] , 'edd_hubspot_list_id' , true );

		/* make sure list exists */
		$list_check = $lists->get_list($hubspot_list_id);

		if (count($list_check->lists)<1)
			$hubspot_list_id = null;

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

			$hubspot_list_id = $new_list->{'listId'};
			
			
			update_post_meta( $item['id'] , 'edd_hubspot_list_id' , $hubspot_list_id );
		}
		
		/* add contact to list */
		$contacts_to_add = array($contact_id);
        $added_contacts = $lists->add_contacts_to_list( $contacts_to_add , $hubspot_list_id );

	}

	return $data;

}