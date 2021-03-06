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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\extension;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\windowFrame;

$viewName = $_GET['name'];
$extensionID = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Clear report
	report::clear();
	
	//No parametres error -> Continue
	$viewName = $_POST['name'];
	$extensionID = $_POST['id'];
	
	$extensionObject = new extension();
	// Try to Load	
	$success = $extensionObject->load($extensionID);
	if (!$success )
	{
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not load extension";
	}
	else
	{	
		//Try to create new layout
		$success = $extensionObject->deleteView($viewName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not //delete view";
		}
	}
	
	// If error, show notification
	if (!$success )
	{	 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc;
		$errorNotification->appendCustomMessage($message);
		
		return $errorNotification ->getReport(FALSE);
	}
	
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= "SUCCESS NOTIFICATION : ".$success;
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Create container
$container = DOM::create();

// Build Notification
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "deleteView", FALSE);

$frame->build($hd);

// Create form
$deleteStructureFormObject = new simpleForm();
$deleteStructureFormElement = $deleteStructureFormObject->build($moduleID, "deleteView", $controls = TRUE);

// Append Form
DOM::append($container, $deleteStructureFormObject->get());

// Template Id [Hidden]
$input = $deleteStructureFormObject->getInput("hidden", "id", $extensionID, $class = "", $autofocus = FALSE);
$deleteStructureFormObject->append($input);

// Page Structure Name [Hidden]
$input = $deleteStructureFormObject->getInput("hidden", "name", $viewName, $class = "", $autofocus = FALSE);
$deleteStructureFormObject->append($input);

$confiramtionMsg = DOM::create('span', 'Really ????? : '.$viewName);
$deleteStructureFormObject->append($confiramtionMsg);

// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>