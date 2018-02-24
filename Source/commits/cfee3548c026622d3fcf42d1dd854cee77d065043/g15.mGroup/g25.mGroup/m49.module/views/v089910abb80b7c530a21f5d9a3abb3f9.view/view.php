<?php
//#section#[header]
// Module Declaration
$moduleID = 49;

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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Developer\components\sql\dvbLib;
use \API\Developer\components\sql\dvbQuery;
use \API\Resources\literals\literal;
use \API\Resources\literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\windowFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Domain
	$empty = is_null($_POST['domain']) || empty($_POST['domain']);
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_domain");
		$err_header = $formErrorNotification->addErrorHeader('domainErrorHeader', $header);
		
		// Description
		$formErrorNotification->addErrorDescription($err_header, 'domainErrorDescription', "err.required");
	}
	
	// Check Title
	$empty = is_null($_POST['title']) || empty($_POST['title']);
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_queryTitle");
		$err_header = $formErrorNotification->addErrorHeader('queryTitleErrorHeader', $header);
		
		// Description
		$formErrorNotification->addErrorDescription($err_header, 'queryTitleErrorDescription', "err.required", $extra = "");
	}
	
	// If error, show notification
	if ($has_error)	
		return $formErrorNotification->getReport();
		
	// Create a new Query
	$dvbQuery = new dvbQuery($_POST['domain']);
	$dvbQuery->create($_POST['title'], $_POST['description']);
		
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Build Frame
$frame = new windowFrame();

// Header
$hd = moduleLiteral::get($moduleID, "lbl_createQuery", FALSE);
$frame->build($hd);

// Create form
$createQueryFormObject = new simpleForm();
$createQueryFormElement = $createQueryFormObject->build($moduleID, "createQuery", $controls = TRUE)->get();
$frame->append($createQueryFormElement);


$domains = dvbLib::getDomainList(TRUE);
$domainInput = array();
foreach ($domains as $domain)
	$domainInput[$domain] = str_replace(".", " > ", $domain);
	
	
// Domain
$title = moduleLiteral::get($moduleID, "lbl_domain");
$input = $createQueryFormObject->getResourceSelect($name = "domain", $multiple = FALSE, $class = "", $domainInput, $selectedValue = "");
$createQueryFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Title
$title = moduleLiteral::get($moduleID, "lbl_queryTitle"); 
$input = $createQueryFormObject->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE);
$createQueryFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Description
$title = moduleLiteral::get($moduleID, "lbl_queryDescription"); 
$input = $createQueryFormObject->getTextarea($name = "description", $value = "", $class = "");
$createQueryFormObject->insertRow($title, $input, $required = FALSE, $notes = "");

// Return frame
return  $frame->getFrame();
//#section_end#
?>