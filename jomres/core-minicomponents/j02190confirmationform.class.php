<?php
/**
 * Core file
 *
 * @author Vince Wooll <sales@jomres.net>
 * @version Jomres 7
 * @package Jomres
 * @copyright    2005-2013 Vince Wooll
 * Jomres (tm) PHP, CSS & Javascript files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly.
 **/


// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( '' );
// ################################################################


/**
#
 *  Constructs and displays printable confirmation page
#
 *
 * @package Jomres
#
 */
class j02190confirmationform
	{
	/**
	#
	 * Constructor:  Constructs and displays printable confirmation page
	#
	 */
	function j02190confirmationform()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = true;

			return;
			}
		$mrConfig        = getPropertySpecificSettings();
		$defaultProperty = getDefaultProperty();
		$contract_uid    = jomresGetParam( $_REQUEST, 'contract_uid', 0 );
		$sendemail       = jomresGetParam( $_REQUEST, 'sendemail', 0 );
		$today           = date( "Y/m/d" );
		$query           = "SELECT arrival,departure,guest_uid,rate_rules,contract_total,deposit_required,tag,property_uid FROM #__jomres_contracts WHERE contract_uid = '" . (int) $contract_uid . "' AND property_uid = '" . (int) $defaultProperty . "'";
		$contractData    = doSelectSql( $query );
		foreach ( $contractData as $contract )
			{
			$arrival          = $contract->arrival;
			$departure        = $contract->departure;
			$guestUid         = $contract->guest_uid;
			$rate_rules       = $contract->rate_rules;
			$contract_total   = $contract->contract_total;
			$deposit_required = $contract->deposit_required;
			$propertyUid      = $contract->property_uid;
			$booking_number   = $contract->tag;
			}

		$current_property_details = jomres_singleton_abstract::getInstance( 'basic_property_details' );
		$current_property_details->gather_data( $defaultProperty );
		$property_policiesdisclaimers = $current_property_details->property_policies_disclaimers;
		$property_checkintimes        = $current_property_details->property_checkintimes;


		$guestDetails = getGuestDetailsForContract( $contract_uid );
		$rows         = array ();
		if ( count( $guestDetails ) > 0 )
			{
			foreach ( $guestDetails as $g )
				{
				$r               = array ();
				$r[ 'TITLE' ]    = $g[ 'title' ];
				$r[ 'QUANTITY' ] = $g[ 'qty' ];
				$rows[ ]         = $r;
				}
			}

		$room_name        = "";
		$room_number      = "";
		$room_class_abbvs = array ();
		$query            = "SELECT room_uid FROM #__jomres_room_bookings WHERE contract_uid = '" . (int) $contract_uid . "' AND `date` = '" . (string) $arrival . "'";
		$roomData         = doSelectSql( $query );

		foreach ( $roomData as $room )
			{
			$counter  = 1;
			$query    = "SELECT room_name,room_number,room_classes_uid FROM #__jomres_rooms WHERE room_uid = '" . (int) $room->room_uid . "'";
			$roomData = doSelectSql( $query );
			foreach ( $roomData as $r )
				{
				if ( strlen( $r->room_name ) > 0 ) $room_name .= $r->room_name;
				if ( strlen( $r->room_number ) > 0 ) $room_number .= $r->room_number;
				$room_classes_uid = $r->room_classes_uid;
				if ( $counter < count( $roomData ) )
					{
					if ( strlen( $r->room_number ) > 0 ) $room_number .= ",";
					if ( strlen( $r->room_name ) > 0 ) $room_name .= ",";
					}
				else
					{
					if ( strlen( $r->room_number ) > 0 ) $room_number .= ".";
					if ( strlen( $r->room_name ) > 0 ) $room_name .= ".";
					}
				$query         = "SELECT room_class_abbv FROM #__jomres_room_classes WHERE room_classes_uid = '" . (int) $room_classes_uid . "'";
				$roomClassData = doSelectSql( $query );

				foreach ( $roomClassData as $roomClass )
					{
					$room_class_abbvs[ ] = $roomClass->room_class_abbv . " ";
					}
				$counter++;
				}
			}

		$room_class_abbvs_data = array_count_values( $room_class_abbvs );
		$counter               = 1;
		foreach ( $room_class_abbvs_data as $key => $val )
			{
			$room_class_abbv .= $key . " x " . $val;
			if ( $counter < count( $room_class_abbvs_data ) ) $room_class_abbv .= ",";
			else
			$room_class_abbv .= ".";
			$counter++;
			}
		
		$jomres_media_centre_images = jomres_singleton_abstract::getInstance( 'jomres_media_centre_images' );
		$jomres_media_centre_images->get_images($defaultProperty, array('property'));

		$output[ 'PROPERTYIMAGE' ] = $jomres_media_centre_images->images ['property'][0][0]['medium'];
		$output[ 'IMAGETHUMB' ]    = $jomres_media_centre_images->images ['property'][0][0]['small'];

		$output[ 'BL_DEAR' ]   = jr_gettext( '_JOMRES_COM_CONFIRMATION_DEAR', _JOMRES_COM_CONFIRMATION_DEAR );
		$output[ 'BL_INTRO1' ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_INTRO1', _JOMRES_COM_CONFIRMATION_RESERVATION_INTRO1 );
		$output[ 'BL_INTRO2' ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_INTRO2', _JOMRES_COM_CONFIRMATION_RESERVATION_INTRO2 );
		//$output['BL_CHECKIN']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_CHECKIN',_JOMRES_COM_CONFIRMATION_RESERVATION_CHECKIN);
		$output[ 'BL_LOOKFORWARD' ]       = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_LOOKFORWARD', _JOMRES_COM_CONFIRMATION_RESERVATION_LOOKFORWARD );
		$output[ 'BL_HAVEAPLEASANTSTAY' ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_HAVEAPLEASANTSTAY', _JOMRES_COM_CONFIRMATION_RESERVATION_HAVEAPLEASANTSTAY );
		$output[ 'HCHECKINTIMES' ]        = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_CHECKINTIMES', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_CHECKINTIMES );
//		$output[ 'HPOLICIESDISCLAIMERS' ] = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POLICIESDISCLAIMERS', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_POLICIESDISCLAIMERS );
		$output[ 'CHECKINTIMES' ]         = $property_checkintimes;
		if ( empty( $output[ 'CHECKINTIMES' ] ) ) $output[ 'HCHECKINTIMES' ] = "";
		$output[ 'POLICIESDISCLAIMERS' ] = $property_policiesdisclaimers;
		if ( empty( $output[ 'POLICIESDISCLAIMERS' ] ) ) $output[ 'HPOLICIESDISCLAIMERS' ] = "";
		//$output['SP_SMALLPRINTINTRO']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINTINTRO',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINTINTRO);
//		$output['SP_BINDINGCONTRACT']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_BINDINGCONTRACT',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_BINDINGCONTRACT);
//		$output['SP_NOALLOWANCE']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_NOALLOWANCE',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_NOALLOWANCE);
//		$output['SP_CANCELLATIONCHARGES_INTRO']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_CANCELLATIONCHARGES_INTRO',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_CANCELLATIONCHARGES_INTRO);
//		$output['SP_30DAYSPLUS']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_30DAYSPLUS',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_30DAYSPLUS);
//		$output['SP_DEPOSITONLY']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_DEPOSITONLY',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_DEPOSITONLY);
//		$output['SP_1530DAYS']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_1530DAYS',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_1530DAYS);
//		$output['SP_50PERCENT']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_50PERCENT',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_50PERCENT);
//		$output['SP_ARRIVALDATETO14DAYS']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_ARRIVALDATETO14DAYS',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_ARRIVALDATETO14DAYS);
//		$output['SP_100_PERCENT']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_100_PERCENT',_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_100_PERCENT);
//		$output['SP_VAT']=jr_gettext('_JOMRES_COM_CONFIRMATION_RESERVATION_VAT',_JOMRES_COM_CONFIRMATION_RESERVATION_VAT);

		$output[ 'HNAME' ]    = jr_gettext( '_JOMRES_COM_MR_EB_ROOM_NAME', _JOMRES_COM_MR_EB_ROOM_NAME );
		$output[ 'HNUMBER' ]  = jr_gettext( '_JOMRES_COM_MR_EB_ROOM_NUMBER', _JOMRES_COM_MR_EB_ROOM_NUMBER );
		$output[ 'HARRIVAL' ] = jr_gettext( '_JOMRES_COM_MR_VIEWBOOKINGS_ARRIVAL', _JOMRES_COM_MR_VIEWBOOKINGS_ARRIVAL );
		if ( $mrConfig[ 'showdepartureinput' ] == "1" ) $output[ 'HDEPARTURE' ] = jr_gettext( '_JOMRES_COM_MR_VIEWBOOKINGS_DEPARTURE', _JOMRES_COM_MR_VIEWBOOKINGS_DEPARTURE );
		$output[ 'HDEPOSITREQUIRED' ] = jr_gettext( '_JOMRES_COM_MR_EB_PAYM_DEPOSITREQUIRED', _JOMRES_COM_MR_EB_PAYM_DEPOSITREQUIRED );
		$output[ 'HCONTRACT_TOTAL' ]  = jr_gettext( '_JOMRES_COM_MR_EB_PAYM_CONTRACT_TOTAL', _JOMRES_COM_MR_EB_PAYM_CONTRACT_TOTAL );
		$output[ 'HROOMTYPE' ]        = jr_gettext( '_JOMRES_COM_MR_EB_ROOM_CLASS_ABBV', _JOMRES_COM_MR_EB_ROOM_CLASS_ABBV );
		//$output['HPERNIGHT']=jr_gettext('_JOMRES_COM_CONFIRMATION_RATE_RULES',_JOMRES_COM_CONFIRMATION_RATE_RULES);
		$output[ 'HTELEPHONE' ]   = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE );
		$output[ 'HFAX' ]         = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX );
		$output[ 'HEMAIL' ]       = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL );
		$output[ 'HWEBSITE' ]     = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_WEBSITE', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_WEBSITE );
		$output[ 'RESV_DETAILS' ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_DETAILS', _JOMRES_COM_CONFIRMATION_RESERVATION_DETAILS );
		$output[ 'HBOOKINGNO' ]   = jr_gettext( '_JOMRES_BOOKING_NUMBER', _JOMRES_BOOKING_NUMBER );

		$propertyAddressArray = getPropertyAddressForPrint( $propertyUid );
		$propertyContactArray = $propertyAddressArray[ 1 ];
		$propertyAddyArray    = $propertyAddressArray[ 2 ];

		$output[ 'PROP_NAME' ]     = $propertyContactArray[ 0 ];
		$output[ 'PROP_STREET' ]   = $propertyContactArray[ 1 ];
		$output[ 'PROP_TOWN' ]     = $propertyContactArray[ 2 ];
		$output[ 'PROP_POSTCODE' ] = $propertyContactArray[ 3 ];
		$output[ 'PROP_REGION' ]   = $propertyContactArray[ 4 ];

		$countryname = getSimpleCountry( $propertyContactArray[ 5 ] );

		$output[ 'PROP_COUNTRY' ] = ucfirst( $countryname );
		$output[ 'PROP_TEL' ]     = $propertyAddyArray[ 0 ];
		$output[ 'PROP_FAX' ]     = $propertyAddyArray[ 1 ];
		$output[ 'PROP_EMAIL' ]   = $propertyAddyArray[ 2 ];
		$output[ 'PROP_URL' ]     = $propertyAddyArray[ 3 ];

		$guestDetailsArray = getGuestForPrint( $guestUid );

		$output[ 'GUEST_FIRSTNAME' ] = $guestDetailsArray[ 0 ];
		$output[ 'GUEST_SURNAME' ]   = $guestDetailsArray[ 1 ];
		$output[ 'GUEST_HOUSE' ]     = $guestDetailsArray[ 2 ];
		$output[ 'GUEST_STREET' ]    = $guestDetailsArray[ 3 ];
		$output[ 'GUEST_TOWN' ]      = $guestDetailsArray[ 4 ];
		$output[ 'GUEST_POSTCODE' ]  = $guestDetailsArray[ 5 ];
		if ( is_numeric( $guestDetailsArray[ 6 ] ) )
			{
			$jomres_regions        = jomres_singleton_abstract::getInstance( 'jomres_regions' );
			$output[ 'GUEST_REGION' ] = jr_gettext( "_JOMRES_CUSTOMTEXT_REGIONS_" . $guestDetailsArray[ 6 ], $jomres_regions->regions[ $guestDetailsArray[ 6 ] ][ 'regionname' ], $editable, false );
			}
		else
			$output[ 'GUEST_REGION' ] = jr_gettext( '_JOMRES_CUSTOMTEXT_PROPERTY_REGION' . $guestDetailsArray[ 6 ], $guestDetailsArray[ 6 ], $editable, false );
		$output[ 'GUEST_COUNTRY' ]   = getSimpleCountry($guestDetailsArray[ 7 ]);

		$output[ 'BOOKINGNO' ]   = $booking_number;
		$output[ 'ROOM_NAME' ]   = $room_name;
		$output[ 'ROOM_NUMBER' ] = $room_number;
		$output[ 'ARRIVAL' ]     = outputDate( $arrival );
		if ( $mrConfig[ 'showdepartureinput' ] == "1" ) $output[ 'DEPARTURE' ] = outputDate( $departure );
		$output[ 'DEPOSIT_REQUIRED' ] = output_price( $deposit_required );
		$output[ 'CONTRACT_TOTAL' ]   = output_price( $contract_total );
		$output[ 'ROOMTYPE' ]         = $room_class_abbv;
		$output[ 'PERNIGHT' ]         = output_price( $rate_rules );
		$output[ 'LIVESITE' ]         = get_showtime( 'live_site' );

		$output[ 'CSS_STYLES' ] = mailer_get_css();

		$output[ '_JOMRES_PLEASE_PRINT' ]        = jr_gettext( '_JOMRES_PLEASE_PRINT', _JOMRES_PLEASE_PRINT );
		$output[ '_JOMRES_OFFICE_USE_ONLY' ]     = jr_gettext( '_JOMRES_OFFICE_USE_ONLY', _JOMRES_OFFICE_USE_ONLY );
		$output[ '_JOMRES_SCAN_FOR_DIRECTIONS' ] = jr_gettext( '_JOMRES_SCAN_FOR_DIRECTIONS', _JOMRES_SCAN_FOR_DIRECTIONS );

		$url            = JOMRES_SITEPAGE_URL_NOSEF . "&task=editBooking&thisProperty=" . $propertyUid . "&contract_uid=" . $contract_uid;
		$qr_code_office = jomres_make_qr_code( $url );

		$url         = make_gmap_url_for_property_uid( $propertyUid );
		$qr_code_map = jomres_make_qr_code( $url );

		if ( $sendemail == 0 )
			{
			$output[ 'OFFICE_QR_CODE_IMAGE' ] = $qr_code_office[ 'relative_path' ];
			$output[ 'DIRECTIONS_QR_CODE' ]   = $qr_code_map[ 'relative_path' ];
			}
		else
			{
			$output[ 'OFFICE_QR_CODE_IMAGE' ] = "cid:office_qr_code";
			$output[ 'DIRECTIONS_QR_CODE' ]   = "cid:map_qr_code";
			}

		$pageoutput[ ] = $output;

		$tmpl = new patTemplate();
		$tmpl->setRoot( JOMRES_TEMPLATEPATH_FRONTEND );
		$tmpl->readTemplatesFromInput( 'confirmation_letter.html' );
		$tmpl->addRows( 'pageoutput', $pageoutput );
		$tmpl->addRows( 'rows', $rows );

		if ( $sendemail == 0 ) $tmpl->displayParsedTemplate();
		else
			{
			$text    = $tmpl->getParsedTemplate();
			$subject = jr_gettext( '_JOMRES_FRONT_GUEST_EMAIL_TEXT_THANKS', _JOMRES_FRONT_GUEST_EMAIL_TEXT_THANKS ) . " " . $output[ 'PROP_NAME' ];
			$query   = "SELECT email FROM #__jomres_guests WHERE guests_uid = '" . (int) $guestUid . "' LIMIT 1";
			if ( $mrConfig[ 'errorCheckingShowSQL' ] ) echo $query . "<br>";
			$userEmail = doSelectSql( $query );
			foreach ( $userEmail as $email )
				{
				$useremail = stripslashes( $email->email );
				}
			$result = false;

			if ( count( $useremail ) > 0 )
				{
				$office_qr_code = array ( "type" => "image", "image_path" => $qr_code_office[ 'absolute_path' ], "CID" => "office_qr_code" );
				$attachments[ ] = $office_qr_code;

				$map_qr_code    = array ( "type" => "image", "image_path" => $qr_code_map[ 'absolute_path' ], "CID" => "map_qr_code" );
				$attachments[ ] = $map_qr_code;

				$result = jomresMailer( $output[ 'PROP_EMAIL' ], $output[ 'PROP_NAME' ], $useremail, $subject, $text, $mode = 1, $attachments );
				$result = jomresMailer( $output[ 'PROP_EMAIL' ], $output[ 'PROP_NAME' ], $output[ 'PROP_EMAIL' ], $subject, $text, $mode = 1, $attachments ); // Let's send this to the property too, so that they have a seperate record
				}
			echo jr_gettext( '_JOMRES_CONFIRMATION_EMAIL_SENT', _JOMRES_CONFIRMATION_EMAIL_SENT );
			}
		}

	function touch_template_language()
		{
		$output = array ();

		$output[ ] = jr_gettext( '_JOMRES_CONFIRMATION_EMAIL_SENT', _JOMRES_CONFIRMATION_EMAIL_SENT );

		$output[ ] = jr_gettext( '_JOMRES_FRONT_GUEST_EMAIL_TEXT_THANKS', _JOMRES_FRONT_GUEST_EMAIL_TEXT_THANKS );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_DEAR', _JOMRES_COM_CONFIRMATION_DEAR );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_INTRO1', _JOMRES_COM_CONFIRMATION_RESERVATION_INTRO1 );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_INTRO2', _JOMRES_COM_CONFIRMATION_RESERVATION_INTRO2 );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_CHECKIN', _JOMRES_COM_CONFIRMATION_RESERVATION_CHECKIN );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_LOOKFORWARD', _JOMRES_COM_CONFIRMATION_RESERVATION_LOOKFORWARD );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_HAVEAPLEASANTSTAY', _JOMRES_COM_CONFIRMATION_RESERVATION_HAVEAPLEASANTSTAY );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINTINTRO', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINTINTRO );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_BINDINGCONTRACT', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_BINDINGCONTRACT );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_NOALLOWANCE', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_NOALLOWANCE );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_CANCELLATIONCHARGES_INTRO', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_CANCELLATIONCHARGES_INTRO );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_30DAYSPLUS', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_30DAYSPLUS );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_DEPOSITONLY', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_DEPOSITONLY );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_1530DAYS', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_1530DAYS );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_50PERCENT', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_50PERCENT );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_ARRIVALDATETO14DAYS', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_ARRIVALDATETO14DAYS );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_100_PERCENT', _JOMRES_COM_CONFIRMATION_RESERVATION_SMALLPRINT_100_PERCENT );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_VAT', _JOMRES_COM_CONFIRMATION_RESERVATION_VAT );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_EB_ROOM_NAME', _JOMRES_COM_MR_EB_ROOM_NAME );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_EB_ROOM_NUMBER', _JOMRES_COM_MR_EB_ROOM_NUMBER );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_VIEWBOOKINGS_ARRIVAL', _JOMRES_COM_MR_VIEWBOOKINGS_ARRIVAL );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_VIEWBOOKINGS_DEPARTURE', _JOMRES_COM_MR_VIEWBOOKINGS_DEPARTURE );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_EB_PAYM_DEPOSITREQUIRED', _JOMRES_COM_MR_EB_PAYM_DEPOSITREQUIRED );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_EB_PAYM_CONTRACT_TOTAL', _JOMRES_COM_MR_EB_PAYM_CONTRACT_TOTAL );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_EB_ROOM_CLASS_ABBV', _JOMRES_COM_MR_EB_ROOM_CLASS_ABBV );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_TELEPHONE );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_FAX );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_EMAIL );
		$output[ ] = jr_gettext( '_JOMRES_COM_MR_VRCT_PROPERTY_HEADER_WEBSITE', _JOMRES_COM_MR_VRCT_PROPERTY_HEADER_WEBSITE );
		$output[ ] = jr_gettext( '_JOMRES_COM_CONFIRMATION_RESERVATION_DETAILS', _JOMRES_COM_CONFIRMATION_RESERVATION_DETAILS );

		foreach ( $output as $o )
			{
			echo $o;
			echo "<br/>";
			}
		}

	/**
	#
	 * Must be included in every mini-component
	#
	 * Returns any settings the the mini-component wants to send back to the calling script. In addition to being returned to the calling script they are put into an array in the mcHandler object as eg. $mcHandler->miniComponentData[$ePoint][$eName]
	#
	 */
	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}

?>
