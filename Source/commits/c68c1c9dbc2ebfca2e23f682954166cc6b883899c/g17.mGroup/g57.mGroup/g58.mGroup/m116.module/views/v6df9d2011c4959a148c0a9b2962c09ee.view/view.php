<?php
//#section#[header]
// Module Declaration
$moduleID = 116;

// Inner Module Codes
$innerModules = array();
$innerModules['templateObject'] = 117;

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

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
		
	// Check templateId
	$empty = (is_null($_POST['templateId']) || empty($_POST['templateId']));
	if ($empty)
	{
		$has_error = TRUE;
				
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

	$id = $_POST['templateId'];

	// Try to load Object
	$templateManager = new templateManager();
	$success = $templateManager->deploy($id);
	
	$formNotification = new formNotification();
	if ($success)
	{	 		
		// SUCCESS NOTIFICATION
		$formNotification->build("success");
		
		// Description
		$message = "OK : ".$customErrMsg_desc;
		$formNotification->appendCustomMessage($message);
	}
	else
	{
		// ERROR NOTIFICATION
		$formNotification->build("error");
		
		// Description
		$message = "AN ERROR OCCURED : ".$customErrMsg_desc;
		$formNotification->appendCustomMessage($message);
	}
	return $formNotification->getReport(FALSE); 
}


$HTMLContent = new HTMLContent();
// Create form
$sForm = new simpleForm();
$ff = new formFactory();
$sForm->build($moduleID, "publisher", $controls = FALSE);
// Append Form
$HTMLContent->buildElement($sForm->get());

// Template Id [Hidden]
$input = $sForm->getInput("hidden", "templateId", $_GET['templateId'], $class = "", $autofocus = FALSE);
$sForm->append($input);

// Try to load Object
$templateManager = new templateManager();
$infoArray = $templateManager->getTemplateInfo($templateID);

// Description
$prompt = DOM::create('div');
$sForm->append($prompt);
$text = moduleLiteral::get($moduleID, "txt_prompt");
DOM::append($prompt, $text);

$controls = DOM::create('div');
$sForm->append($controls);

// Save
$title = moduleLiteral::get($moduleID, "btn_publish");
$button = $ff->getSubmitButton($title, $id = "");
DOM::append($controls, $button);

// Return output
return $HTMLContent->getReport();
//#section_end#
?>