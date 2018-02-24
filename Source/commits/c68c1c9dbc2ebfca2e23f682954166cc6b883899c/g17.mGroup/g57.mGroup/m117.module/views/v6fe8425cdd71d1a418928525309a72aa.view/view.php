<?php
//#section#[header]
// Module Declaration
$moduleID = 117;

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\templateManager;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formFactory;
use \UI\Html\HTMLContent;


$pageStructureName = $_GET['objectName'];
$templateID = $_GET['templateId'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
		
	// Check templateId
	$empty = (is_null($_POST['templateId']) || empty($_POST['templateId']));
	if ($empty)
	{
		$has_error = TRUE;
				
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Internal error Reload";
	}
	
	// Check pageStructure
	$empty = (is_null($_POST['objectName']) || empty($_POST['objectName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Internal error Reload";
	}
	
	// If error, show notification
	if ($has_error)
	{
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc;
		$errorNotification->appendCustomMessage($message);
		
		return $errorNotification->getReport(FALSE);
	}
	
	//No parametres error -> Continue
	$pageStructureName = $_POST['objectName'];
	$templateID = $_POST['templateId'];
	
	// Try to load Object
	$templateManager = new templateManager();
	$templateObject = $templateManager->getTemplate($templateID);
	
	if (is_null($templateObject))
	{
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not load template";
	}
	else
	{	
		//Try to create new layout
		$success = $templateObject->deletePageStructure($pageStructureName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not delete page structure";
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
		
		//return $errorNotification->getReport(FALSE);
		$HTMLContent = new HTMLContent();
		$HTMLContent->addReportAction('delete.error', 'pSnip_'.$_POST['objectName']);
		return $HTMLContent->getReport();
	}
	
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= "SUCCESS NOTIFICATION : ".$success;
	$successNotification->appendCustomMessage($message);
	
	$HTMLContent = new HTMLContent();
	$HTMLContent->addReportAction('delete.success', 'pSnip_'.$_POST['objectName']);
	return $HTMLContent->getReport();
	//return $successNotification->getReport(FALSE);
}

$HTMLContent = new HTMLContent();
// Create form
$sForm = new simpleForm();
$ff = new formFactory();
$sForm->build($moduleID, "deletePageStructure", $controls = FALSE);
// Append Form
$HTMLContent->buildElement($sForm->get());

// Template Id [Hidden]
$input = $sForm->getInput("hidden", "templateId", $templateID, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Page Structure Name [Hidden]
$input = $sForm->getInput("hidden", "objectName", $pageStructureName, $class = "", $autofocus = FALSE);
$sForm->append($input);

$confiramtionMsg = DOM::create('span', 'Are you sure deleting this item : '.$pageStructureName);
$sForm->append($confiramtionMsg);

$controls = DOM::create('div');
$sForm->append($controls);

// Save 
$button = $ff->getSubmitButton("Delete", $id = "");
DOM::append($controls, $button);

// Dissmiss
$button = $ff->getButton("Dismiss", $id = "");
DOM::attr($button, 'data-formDissmiss', 'delete');
DOM::append($controls, $button);


// Return output
$HTMLContent->addReportAction('delete.toggle', 'pSnip_'.$_GET['objectName']);
return $HTMLContent->getReport($_GET['holder']);
//#section_end#
?>