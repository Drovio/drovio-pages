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
use \UI\Presentation\popups\popup;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \API\Developer\resources\layouts\ebuilderLayout;
use \API\Resources\literals\moduleLiteral;
use \API\Developer\ebuilder\templateManager;


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
	$empty = (is_null($_POST['pageStructureName']) || empty($_POST['pageStructureName']));
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
	
	
	// Check layout
	$empty = (is_null($_POST['layout']) || empty($_POST['layout']));
	if ($empty)
	{
		$has_error = TRUE;
			
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_layout");
		$headerId = 'layout'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'layout'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");		
	}
	
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$layoutName = $_POST['layout'];
	$pageStructureName = $_POST['pageStructureName'];
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
		$success = $templateObject->createPageStructure($pageStructureName, $layoutName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not create page structure";
		}
	}
	
	// If error, show notification
	if (!$success )
	{	 		
		// ERROR  NOTIFICATION
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
	$attr['objectName'] = $pageStructureName;	
	$attr['hld'] = 'pSnip_'.$themeName;
	$pageStructureItem = $HTMLContentBuilder->getModuleContainer($innerModules['componentEditor'], "psItemSnippet", $attr, TRUE, $attr['hld']);
	$HTMLContentBuilder->buildElement($pageStructureItem);
	return $HTMLContentBuilder->getReport("#psBodyContent", "append");
}

$HTMLContent = new HTMLContent();

// Create form
$sForm = new simpleForm();
$sForm->build($moduleID, "newPageStructure", $controls = TRUE);


$HTMLContent->buildElement($sForm->get());

// Template Id [Hidden]
$input = $sForm->getInput($type = "hidden", $name = "templateId", $value = $templateID, $class = "", $autofocus = FALSE);
$sForm->append($input);

// pageStructureName
$title = moduleLiteral::get($moduleID, "lbl_pageStructureName"); 
$input = $sForm->getInput($type = "text", $name = "pageStructureName", $value = "", $class = "", $autofocus = FALSE);
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");

//Construct Layout Viewer
$ebldLayoutManager = new ebuilderLayout();
$ebldLayouts = $ebldLayoutManager->getAllLayouts();
$resource = array();
foreach ($ebldLayouts  as $layout)
{
	//Selector values
	$resource[$layout] = $layout;
}	
$title = moduleLiteral::get($moduleID, "lbl_layout");
$input = $sForm->getResourceSelect($name = "layout", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$sForm->insertRow($title, $input, $required = TRUE, $notes = "");



// Build Notification
$popup = new popup();

//$popup->parent("tplContents_addPs");
//$popup->parent();
$popup->position("right|top");
//$popup->type("persistent", FALSE);


$popup->build($HTMLContent->get());
// return
return  $popup->getReport();











/*
use \API\Developer\resources\layouts\ebuilderLayout;
use \API\Developer\ebuilder\template;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\frames\windowFrame;

use \UI\Html\HTMLContent;

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
	$empty = (is_null($_POST['pageStructureName']) || empty($_POST['pageStructureName']));
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
	
	
	// Check layout
	$empty = (is_null($_POST['layout']) || empty($_POST['layout']));
	if ($empty)
	{
		$has_error = TRUE;
			
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_layout");
		$headerId = 'layout'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'layout'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");		
	}
	
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	//No parametres error -> Continue
	$layoutName = $_POST['layout'];
	$pageStructureName = $_POST['pageStructureName'];
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
		$success = $templateObject->createPageStructure($pageStructureName, $layoutName);
		if (!$success )
		{
			$customErrMsg_hd = "";
			$customErrMsg_desc = "Could not create page structure";
		}
	}
	
	// If error, show notification
	if (!$success )
	{	 		
		// ERROR  NOTIFICATION
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
	$attr['objectName'] = $pageStructureName;
	$pageStructureItem = $HTMLContentBuilder->getModuleContainer($innerModules['componentEditor'], "psItemSnippet", $attr, TRUE);
	$HTMLContentBuilder->buildElement($pageStructureItem);
	return $HTMLContentBuilder->getReport("#psBodyContent", "append");
}

// Build Notification
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "newPageStructure", FALSE);

$frame->build($hd);

// Create form
$addPageStructureFormObject = new simpleForm();
$addPageStructureFormElement = $addPageStructureFormObject->build($moduleID, "newPageStructure", $controls = TRUE);

// Append Form
DOM::append($container, $addPageStructureFormObject->get());

// Template Id [Hidden]
$input = $addPageStructureFormObject->getInput($type = "hidden", $name = "templateId", $value = $templateID, $class = "", $autofocus = FALSE);
$addPageStructureFormObject->append($input);

// pageStructureName
$title = moduleLiteral::get($moduleID, "lbl_pageStructureName"); 
$input = $addPageStructureFormObject->getInput($type = "text", $name = "pageStructureName", $value = "", $class = "", $autofocus = FALSE);
$addPageStructureFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

//Construct Layout Viewer
$ebldLayoutManager = new ebuilderLayout();
$ebldLayouts = $ebldLayoutManager->getAllLayouts();
$resource = array();
foreach ($ebldLayouts  as $layout)
{
	//Selector values
	$resource[$layout] = $layout;
}	
$title = moduleLiteral::get($moduleID, "lbl_layout");
$input = $addPageStructureFormObject->getResourceSelect($name = "layout", $multiple = FALSE, $class = "", $resource, $selectedValue = "");
$addPageStructureFormObject->insertRow($title, $input, $required = TRUE, $notes = "");




// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
*/
//#section_end#
?>