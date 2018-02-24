<?php
//#section#[header]
// Module Declaration
$moduleID = 117;

// Inner Module Codes
$innerModules = array();
$innerModules['templateViewer'] = 131;

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
use \API\Resources\literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formFactory;
use \UI\Html\HTMLContent;

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
	
	// Check Title
	$empty = (is_null($_POST['title']) || empty($_POST['title']));
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
	$title = $_POST['title'];
	$description = $_POST['description'];
	$templateID = $_POST['templateId'];
	
	// Try to load Object
	$templateManager = new templateManager();
	$success  = $templateManager->updateTemplateInfo($templateID, $title, $description);
	
	// If error, show notification
	if (!$success )
	{	 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc;
		$errorNotification->appendCustomMessage($message);
		
		return $errorNotification->getReport(FALSE);
		/*
		$HTMLContent = new HTMLContent();
		$HTMLContent->addReportAction('edit.error', '');
		return $HTMLContent->getReport();
		*/
	}
		
	$HTMLContent = new HTMLContent();
	$attr['id'] = $templateID;
	$moduleContent = $HTMLContent->getModuleContainer($innerModules['templateViewer'], "templateInfo", $attr, TRUE);
	$HTMLContent->buildElement($moduleContent);
	$HTMLContent->addReportAction('edit.success', '');
	$holder = '#templateInfoOverview > .bodyContent > .viewerHolder';
	return $HTMLContent->getReport($holder);
}

$HTMLContent = new HTMLContent();
// Create form
$sForm = new simpleForm();
$ff = new formFactory();
$sForm->build($moduleID, "", $controls = FALSE);
// Append Form
$HTMLContent->buildElement($sForm->get());


// Template Id [Hidden]
$input = $sForm->getInput("hidden", "templateId", $templateID, $class = "", $autofocus = FALSE);
$sForm->append($input);

// Try to load Object
$templateManager = new templateManager();
$infoArray = $templateManager->getTemplateInfo($templateID);

// Title
$title = moduleLiteral::get($moduleID, "lbl_templateTitle"); 
$input = $sForm->getInput($type = "text", $name = "title", $value = $infoArray['title'], $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");


// Description





$controls = DOM::create('div');
$sForm->append($controls);

// Save
$title = literal::dictionary("save", FALSE);
$button = $ff->getSubmitButton($title, $id = "");
DOM::append($controls , $button);

// Dissmiss
$title = literal::dictionary("cancel", FALSE);
$button = $ff->getButton($title, $id = "");
DOM::attr($button, 'data-formDissmiss', 'delete');
DOM::append($controls , $button);


// Return output
$HTMLContent->addReportAction('edit.toggle', '');
return $HTMLContent->getReport($_GET['holder']);
//#section_end#
?>