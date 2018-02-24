<?php
//#section#[header]
// Module Declaration
$moduleID = 117;

// Inner Module Codes
$innerModules = array();
$innerModules['componentEditor'] = 126;

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
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\windowFrame;

use \UI\Html\HTMLContent;

// Create container
$container = DOM::create();

$templateID = $_GET['templateId'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check templateId
	// Better costum error
	$empty = (is_null($_POST['templateId']) || empty($_POST['templateId']));
	if ($empty)
	{
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Internal error Reload";
		
		//
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc;
		$errorNotification->appendCustomMessage($message);
		
		return $successNotification->getReport(FALSE);
	}
	
	// Check templateName
	$empty = (is_null($_POST['themeName']) || empty($_POST['themeName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_pageStructureName");
		$headerId = 'pageStructureName'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'pageStructureName'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
		
	
	// If error, show notification
	if ($has_error)
	{
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$themeName = $_POST['themeName'];
	$templateID = $_POST['templateId'];
	
	// Try to load Object
	$templateManager = new templateManager();
	$templateObject = $templateManager->getTemplate($templateID);
	
	// Try to Load	
	$success = $templateObject->load($templateID);	
	if (!$success )
	{
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not load template";
	}
	else
	{	
		//Try to create new layout
		$success = $templateObject->createTheme($themeName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not create theme";
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
				
		return $errorNotification->getReport(FALSE);		
	}
	
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	//return $successNotification->getReport(FALSE);
	
	$HTMLContentBuilder = new HTMLContent();
	$attr['templateId'] = $templateID;
	$attr['objectName'] = $themeName;	
	$pageStructureItem = $HTMLContentBuilder->getModuleContainer($innerModules['componentEditor'], "thItemSnippet", $attr, TRUE);
	$HTMLContentBuilder->buildElement($pageStructureItem);
	return $HTMLContentBuilder->getReport("#thBodyContent", "append");
}

// Build Frame
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "addTheme", FALSE);

$frame->build($hd);

// Create form
$sForm = new simpleForm();
$sForm->build($moduleID, "newTheme", $controls = TRUE);

// Append Form
DOM::append($container, $sForm->get());

// Template Id [Hidden]
$input = $sForm->getInput($type = "hidden", $name = "templateId", $value = $templateID, $class = "", $autofocus = FALSE);
$sForm->append($input);

// theme name
$title = moduleLiteral::get($moduleID, "lbl_pageStructureName"); 
$input = $sForm->getInput($type = "text", $name = "themeName", $value = "", $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");


// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>