<?php
//#section#[header]
// Module Declaration
$moduleID = 389;

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
importer::import("DEV", "WebTemplates");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\WebTemplates\templateTheme;
use \DEV\WebTemplates\templateThemeCSS;
use \DEV\WebTemplates\templateThemeJS;

$templateID = engine::getVar('id');
if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	if (empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Create View
	$theme = new templateTheme($templateID);
	$themeName = str_replace(" ", "_", trim($_POST['name']));
	$result = $theme->create($themeName);
	
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating theme..."));
		return $errFormNtf->getReport();
	}
	
	// Add css and js
	if (!empty($_POST['js_name']))
	{
		$themeJS = new templateThemeJS($templateID, $themeName);
		$themeJS->create($_POST['js_name']);
	}
	
	if (!empty($_POST['css_name']))
	{
		$themeCSS = new templateThemeCSS($templateID, $themeName);
		$themeCSS->create($_POST['css_name']);
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_createThemeTitle");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createNewTheme");
$form = $frame->getFormFactory();

// Application ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $templateID, $class = "", $autofocus = TRUE);
$frame->append($input);

// Theme Name
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

$title = moduleLiteral::get($moduleID, "lbl_themeJSFile");
$notes = moduleLiteral::get($moduleID, "lbl_themeJSFile_notes");
$input = $form->getInput($type = "text", $name = "js_name", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($formRow);

$title = moduleLiteral::get($moduleID, "lbl_themeCSSFile");
$notes = moduleLiteral::get($moduleID, "lbl_themeCSSFile_notes");
$input = $form->getInput($type = "text", $name = "css_name", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($formRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>