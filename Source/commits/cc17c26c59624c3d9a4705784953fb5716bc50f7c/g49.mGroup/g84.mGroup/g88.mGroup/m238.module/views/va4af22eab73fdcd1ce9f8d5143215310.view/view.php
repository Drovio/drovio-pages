<?php
//#section#[header]
// Module Declaration
$moduleID = 238;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
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
use \DEV\Core\sql\sqlQuery;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Domain
	if (empty($_POST['domain']))
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_domain");
		$err_header = $formErrorNotification->addErrorHeader('domainErrorHeader', $header);
		
		// Description
		$formErrorNotification->addErrorDescription($err_header, 'domainErrorDescription', "err.required");
	}
	
	// Check Title
	if (empty($_POST['title']))
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
	$sqlQuery = new sqlQuery($_POST['domain']);
	$sqlQuery->create($_POST['title'], $_POST['description']);
		
	// SUCCESS NOTIFICATION
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport();
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


$domains = sqlDomain::getList(TRUE);
$domainInput = array();
foreach ($domains as $domain)
	$domainInput[$domain] = str_replace(".", " > ", $domain);
	
	
// Domain
$title = moduleLiteral::get($moduleID, "lbl_domain");
$input = $createQueryFormObject->getResourceSelect($name = "domain", $multiple = FALSE, $class = "", $domainInput, $selectedValue = "");
$createQueryFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Title
$title = literal::dictionary("title");
$input = $createQueryFormObject->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE);
$createQueryFormObject->insertRow($title, $input, $required = TRUE, $notes = "");

// Description
$title = literal::dictionary("description");
$input = $createQueryFormObject->getTextarea($name = "description", $value = "", $class = "");
$createQueryFormObject->insertRow($title, $input, $required = FALSE, $notes = "");

// Return frame
return  $frame->getFrame();
//#section_end#
?>