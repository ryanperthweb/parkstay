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


class j00005define_template_paths
	{
	function j00005define_template_paths( $componentArgs )
		{
		// Must be in all minicomponents. Minicomponents with templates that can contain editable text should run $this->template_touch() else just return
		$MiniComponents = jomres_singleton_abstract::getInstance( 'mcHandler' );
		if ( $MiniComponents->template_touch )
			{
			$this->template_touchable = false;

			return;
			}

		if ( !defined( 'JOMRES_TEMPLATEPATH_FRONTEND' ) )
			{
			if ( !using_bootstrap() ) define( 'JOMRES_TEMPLATEPATH_FRONTEND', JOMRESPATH_BASE . JRDS . "templates" . JRDS . "jquery_ui" . JRDS . "frontend" );
			else
			define( 'JOMRES_TEMPLATEPATH_FRONTEND', JOMRESPATH_BASE . JRDS . "templates" . JRDS . "bootstrap" . JRDS . "frontend" );
			}
		if ( !defined( 'JOMRES_TEMPLATEPATH_BACKEND' ) )
			{
			if ( !using_bootstrap() ) define( 'JOMRES_TEMPLATEPATH_BACKEND', JOMRESPATH_BASE . JRDS . "templates" . JRDS . "jquery_ui" . JRDS . "backend" );
			else
			define( 'JOMRES_TEMPLATEPATH_BACKEND', JOMRESPATH_BASE . JRDS . "templates" . JRDS . "bootstrap" . JRDS . "backend" );
			}
		if ( !defined( 'JOMRES_TEMPLATEPATH_ADMINISTRATOR' ) )
			{
			if ( !using_bootstrap() ) define( 'JOMRES_TEMPLATEPATH_ADMINISTRATOR', JOMRESPATH_BASE . JRDS . "templates" . JRDS . "jquery_ui" . JRDS . "administrator" );
			else
			define( 'JOMRES_TEMPLATEPATH_ADMINISTRATOR', JOMRESPATH_BASE . JRDS . "templates" . JRDS . "bootstrap" . JRDS . "administrator" );
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