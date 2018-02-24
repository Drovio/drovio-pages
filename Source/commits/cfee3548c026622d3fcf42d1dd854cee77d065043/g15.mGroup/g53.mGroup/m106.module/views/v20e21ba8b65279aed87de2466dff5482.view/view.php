<?php
//#section#[header]
// Module Declaration
$moduleID = 106;

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
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\profiler\tester;
use \API\Developer\components\sdk\sdkPackage;
use \API\Developer\components\sdk\sdkLibrary;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\formReport\formNotification;

//__________ SERVER CONTROL __________//
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$status = FALSE;
	
	if (tester::status())
	{
		// Get activated packages
		$packages = $_POST['pkg'];
		$activated = array();
		
		// Format package list
		if (!empty($packages))
			foreach ($packages as $pkg => $value)
			{
				$parts = explode("_", $pkg);
				$activated[$parts[0]][] = $parts[1];
			}
		
		// Activate Pagkages
		$status = sdkPackage::setTesterPackages($activated);
		
	}
	
	// Create notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}
//#section_end#
?>