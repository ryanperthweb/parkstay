<?php
/**
 * Plugin
 * @author Vince Wooll <sales@jomres.net>
 * @version Jomres 4 
* @package Jomres
* @copyright	2005-2010 Vince Wooll
* Jomres (tm) PHP files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly, however all images, css and javascript which are copyright Vince Wooll are not GPL licensed and are not freely distributable. 
**/

// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( '' );
// ################################################################

class j10002list_local_events
	{
	function j10002list_local_events()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return 
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=false; return;
			}
		$htmlFuncs =jomres_getSingleton('html_functions');
		$this->cpanelButton=$htmlFuncs->cpanelButton(JOMRES_SITEPAGE_URL_ADMIN.'&task=list_local_events', 'events.png', jr_gettext( "_JRPORTAL_LOCAL_EVENTS_TITLE" , _JRPORTAL_LOCAL_EVENTS_TITLE ,false,false),"/jomres/images/jomresimages/small/",jr_gettext( "_JOMRES_CUSTOMCODE_MENUCATEGORIES_PORTAL" , "portal functionality" ,false,false));
		}
	
	
	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return $this->cpanelButton;
		}	
	}