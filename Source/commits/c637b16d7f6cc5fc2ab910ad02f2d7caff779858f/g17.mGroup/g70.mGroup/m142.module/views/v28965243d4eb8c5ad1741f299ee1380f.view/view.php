<?php
//#section#[header]
// Module Declaration
$moduleID = 142;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Presentation");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\extension;
use \API\Developer\ebuilder\extComponents\extSrcObject;
use \UI\Presentation\popups\popup;
use \UI\Presentation\notification;

use \ESS\Protocol\server\HTMLServerReport;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get POST Variables 
	$extensionID = $_POST['id'];
	$code = $_POST['wideContent'];
	$imports = (isset($_POST['imports']) ? $_POST['imports'] : array());
	
	$extension = new extension();
	// Try to Load	
	$status = $extension->load($extensionID);
	// Get extPage Object
	$object = $extension->getSrcObject($_POST['pkg'], $_POST['objName'], $_POST['ns']);
	// Set Object Attributes
	$pageObject->setImports($imports);
	// Update page Object
	$status = $object->update($code);

	
	// Build popup
	$reportPopup = new popup();
	$reportPopup->timeout(TRUE);
	$reportPopup->fade(TRUE);
	$reportPopup->position('top');
	
	// Build Notification
	$reportNtf = new notification();
	if ($status)
	{
		$reportNtf->build("success");		
		// Description
		$message= $reportNtf->getMessage( "success", "success.save_success");
	}
	else 
	{
		$reportNtf->build("error");		
		// Description
		$message= $reportNtf->getMessage( "error", "err.save_error");			
	}
	
	$reportNtf->appendCustomMessage($message);
	$notification = $reportNtf->get();
	$reportPopup->build($notification);
	
	// Return Report
	//return $reportPopup->get();
	// Clear Report
	HTMLServerReport::clear();
	// Add this modulePage as content
	HTMLServerReport::addContent($notification , "popup");
	// Return Report
	return HTMLServerReport::get();
}
//#section_end#
?>