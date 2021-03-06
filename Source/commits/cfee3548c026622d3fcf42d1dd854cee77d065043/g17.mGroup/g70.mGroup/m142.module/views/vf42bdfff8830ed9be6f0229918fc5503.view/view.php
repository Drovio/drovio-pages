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
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

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
use \UI\Presentation\popups\popup;
use \UI\Presentation\notification;

use \ESS\Protocol\server\HTMLServerReport;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get POST Variables 
	$extensionID = $_POST['id'];
	
	$extension = new extension();
	// Try to Load	
	$status = $extension->load($_POST['id']);	
	// Release Extension
	$status = $extension->deploy();

	
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
		$message = "Deploy Success";
	}
	else 
	{		
		$reportNtf->build("error");		
		// Description
		$message = "Deploy Error";			
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