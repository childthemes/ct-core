<?php
/**
 * Integration Plugin with Child Themes.
 *
 * @link       http://childthemes.net/
 * @author     Rizal Fauzie <fauzie@childthemes.net>
 *
 * @since      1.0.0
 * @package    CT_Core
 * @subpackage CT_Core/includes/integrations
 */

class CT_Core_Siteorigin_Panels_Template extends CT_Core_Integrations {

  /**
	 * Class Constructor.
	 *
	 * @since  1.0.0
	 */
  public function __construct() {
    $this->integration = 'siteorigin-panels';
    parent::__construct();
  }

  /**
	 * Action and Filter hooks.
	 */
	public function init() {



	}

}
