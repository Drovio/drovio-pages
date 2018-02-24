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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\windowFrame;
use \DEV\Core\sql\sqlDomain;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();

	// Check Domain
	$empty = (is_null($_POST['domain']) || empty($_POST['domain'])) && !isset($_POST['root']);
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
		$formErrorNotification->addErrorDescription($err_header, 'queryTitleErrorDescription', "err.required");
	}
	
	// If error, show notification
	if ($has_error)
		return $formErrorNotification->getReport();
	
	
	$sqlDomain = new sqlDomain();
	if ($_POST['root'] == "on")
		$result = $sqlDomain->create($_POST['title']);
	else
		$result = $sqlDomain->create($_POST['title'], $_POST['domain']);
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_queryTitle");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error creating domain..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build Frame
$frame = new windowFrame();

// Frame header
$hd = moduleLiteral::get($moduleID, "lbl_createDomain", FALSE);
$frame->build($hd);

// Create form
$createDomainFormObject = new simpleForm();
$createDomainFormElement = $createDomainFormObject->build($moduleID, "createDomain", $controls = TRUE)->get();
$frame->append($createDomainFormElement);

$domains = sqlDomain::getList(TRUE);
$domainInput = array();
foreach ($domains as $domain)
	$domainInput[$domain] = str_replace(".", " > ", $domain);
	
	
// Root Domain
$title = moduleLiteral::get($moduleID, "lbl_root");
$input = $createDomainFormObject->getInput($type = "checkbox", $name = "root", $value = "", $class = "", $autofocus = TRUE);
$createDomainFormObject->insertRow($title, $input, $required = TRUE, $notes = "");
	
// Domain
$title = moduleLiteral::get($moduleID, "lbl_domain");
$input = $createDomainFormObject->getResourceSelect($name = "domain", $multiple = FALSE, $class = "", $domainInput, $selectedValue = "");
$createDomainFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Title
$title = moduleLiteral::get($moduleID, "lbl_title"); 
$input = $createDomainFormObject->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE);
$createDomainFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Return frame
return  $frame->getFrame();
//#section_end#
?>