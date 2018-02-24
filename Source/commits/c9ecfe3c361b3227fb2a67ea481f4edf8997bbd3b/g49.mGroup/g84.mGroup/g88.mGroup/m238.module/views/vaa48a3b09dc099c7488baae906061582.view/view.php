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
use \UI\Presentation\frames\dialogFrame;
use \DEV\Core\sql\sqlDomain;
use \DEV\Core\coreProject;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();

	// Check Domain
	if (empty($_POST['domain']) && !isset($_POST['root']))
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_domain");
		$err_header = $errFormNtf->addHeader($header);
		$errFormNtf->addDescription($err_header, "err.required");
	}
	
	// Check Title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_queryTitle");
		$err_header = $errFormNtf->addHeader($header);
		$errFormNtf->addDescription($err_header, "err.required");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	
	$sqlDomain = new sqlDomain();
	if ($_POST['root'] == "on")
		$result = $sqlDomain->create($_POST['title']);
	else
		$result = $sqlDomain->create($_POST['title'], $_POST['domain']);
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_queryTitle");
		$err = $errFormNtf->addHeader(, $err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating domain..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build Frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_createDomain");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createDomain");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
	
// Root Domain
$title = moduleLiteral::get($moduleID, "lbl_root");
$input = $form->getInput($type = "checkbox", $name = "root", $value = "", $class = "", $autofocus = TRUE);
$inprow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inprow);

// Domain
$domains = sqlDomain::getList(TRUE);
$domainInput = array();
foreach ($domains as $domain)
	$domainInput[$domain] = str_replace(".", " > ", $domain);
	
$title = moduleLiteral::get($moduleID, "lbl_domain");
$input = $form->getResourceSelect($name = "domain", $multiple = FALSE, $class = "", $domainInput, $selectedValue = "");
$inprow= $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inprow);

// Title
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE);
$inprow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inprow);

// Return frame
return  $frame->getFrame();
//#section_end#
?>