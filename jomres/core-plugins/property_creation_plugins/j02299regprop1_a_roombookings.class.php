<?php
/**
 * Core file
 * @author Vince Wooll <sales@jomres.net>
 * @version Jomres 4 
* @package Jomres
* @copyright	2005-2011 Vince Wooll
* Jomres (tm) PHP files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly, however all images, css and javascript which are copyright Vince Wooll are not GPL licensed and are not freely distributable. 
**/


// ################################################################
defined( '_JOMRES_INITCHECK' ) or die( '' );
// ################################################################


/**
#
 *
 #
* @package Jomres
#
 */
class j02299regprop1_a_roombookings {
	/**
	#
	 * Constructor:
	#
	 */
	function j02299regprop1_a_roombookings()
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents =jomres_getSingleton('mcHandler');
		if ($MiniComponents->template_touch)
			{
			$this->template_touchable=true; return;
			}

		$this->next_step = "rental";
		$this->title = jr_gettext('_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL',_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL,FALSE);
		$this->description =jr_gettext('_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL_DESC',_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL_DESC,FALSE);
		}

	function touch_template_language()
		{
		$output=array();

		$output[]		=jr_gettext('_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL',_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL);
		$output[]		=jr_gettext('_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL_DESC',_JOMRES_PROPERTYMANAGEMENTPROCESS_RENTAL_DESC);

		foreach ($output as $o)
			{
			echo $o;
			echo "<br/>";
			}
		}
	// This must be included in every Event/Mini-component
	function getRetVals()
		{
		return array('next_step'=>$this->next_step,'title'=>$this->title, 'description'=>$this->description);
		}
	}
?>