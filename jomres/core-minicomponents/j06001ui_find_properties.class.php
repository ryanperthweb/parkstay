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

class j06001ui_find_properties
	{
	function j06001ui_find_properties()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = false;

			return;
			}

		$thisJRUser = jomres_singleton_abstract::getInstance( 'jr_user' );

		$users_properties = array ();
		foreach ( $thisJRUser->authorisedPropertyDetails as $key => $val )
			{
			$users_properties[ ] = $key;
			}
		$gor = genericOr( $users_properties, 'propertys_uid' );

		$initial = jr_strtolower( jomresGetParam( $_REQUEST, 'initial', "" ) );
		$query   = "SELECT propertys_uid,property_name FROM #__jomres_propertys WHERE property_name LIKE '" . $initial . "%' AND " . $gor . "ORDER BY `property_name`";
		$result  = doSelectSql( $query );

		if ( count( $result ) > 0 )
			{
			foreach ( $result as $property )
				{
				if ( function_exists( 'jomres_decode' ) ) $pn = jomres_decode( $thisJRUser->authorisedPropertyDetails[ $property->propertys_uid ][ 'property_name' ] );
				else
				$pn = $thisJRUser->authorisedPropertyDetails[ $property->propertys_uid ][ 'property_name' ];

				$rows[ ] = array ( "URL" => jomresURL( JOMRES_SITEPAGE_URL . '&thisProperty=' . $property->propertys_uid ), "PROPERTYNAME" => $pn );
				}
			}

		$tmpl = new patTemplate();
		$tmpl->setRoot( JOMRES_TEMPLATEPATH_BACKEND );
		$tmpl->readTemplatesFromInput( 'ajax_switch_property.html' );
		$tmpl->addRows( 'pageoutput', $pageoutput );
		$tmpl->addRows( 'rows', $rows );
		echo $tmpl->getParsedTemplate();
		echo $output;
		}


	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return null;
		}
	}