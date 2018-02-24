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
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\template;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\frames\windowFrame;

// Create container
$container = DOM::create();

$templateID = $_GET['templateId'];

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Clear report
	report::clear();	
	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check templateId
	// Better costum error
	$empty = (is_null($_POST['templateId']) || empty($_POST['templateId']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_templateId");
		$headerId = 'templateId'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'templateId'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	
	
	// Check templateName
	$empty = (is_null($_POST['pageName']) || empty($_POST['pageName']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_pageName");
		$headerId = 'pageName'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'pageName'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	
	// Check layout
	$empty = (is_null($_POST['pageStructure']) || empty($_POST['pageStructure']));
	if ($empty)
	{
		$has_error = TRUE;
			
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_pageStructure");
		$headerId = 'pageStructure'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'pageStructure'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");		
	}
	
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$pageStructureName = $_POST['pageStructure'];
	$pageName = $_POST['pageName'];
	$templateID = $_POST['templateId'];
	
	$templateObject = new template();
	
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
		$success = $templateObject->addSequencePage($pageStructureName, $pageName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not create page structure";
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
	
	return $successNotification->getReport(FALSE);
}

// Build Notification
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "newSequencePage", FALSE);

$frame->build($hd);

// Create form
$addPageStructureFormObject = new simpleForm();
$addPageStructureFormElement = $addPageStructureFormObject->build($moduleID, "newSequencePage", $controls = TRUE);

// Append Form
DOM::append($container, $addPageStructureFormObject->get());

// Template Id [Hidden]
$input = $addPageStructureFormObject->getInput("hidden", "templateId", $templateID, "", FALSE);
$addPageStructureFormObject->append($input);

// pageName
$title = moduleLiteral::get($moduleID, "lbl_pageName"); 
$input = $addPageStructureFormObject->getInput($type = "text", $name = "pageName", $value = "", $class = "", $autofocus = FALSE);
$addPageStructureFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

$templateObject = new template();
$templateObject->load($templateID);
$pageStructuresArray = $templateObject->getAllStructures();
$resource = array();
foreach ($pageStructuresArray as $pageStructure)
{
	//Selector values
	$resource[$pageStructure] = $pageStructure;
}	
$title = moduleLiteral::get($moduleID, "lbl_pageStructure");
$input = $addPageStructureFormObject->getResourceSelect($name = "pageStructure", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$addPageStructureFormObject->insertRow($title, $input, $required = TRUE, $notes = "");




// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>