<?php
/* 
 * Redback PHP Header Document
 *
 * Title: RedBack PHP Domain Configuration
 * Description: Traces and checks all paths relative to http://www.redback.io
 * Author: RedBack Developing Team
 * DateCreated: 14/01/2012
 * DateRevised: 13/02/2015
 *
 */
 
// Initialize domain variables
$__pageDomain = "{domain_name}";
$__domainPath = "{domain_path}";

// Define system root globally as a constant
define("systemRoot", str_replace($__domainPath, "", $_SERVER['DOCUMENT_ROOT']));
require_once(systemRoot.'/_globalSettings.inc');
?>