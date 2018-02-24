<?php
//#section#[header]
// Module Declaration
$moduleID = 395;

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
importer::import("DEV", "Websites");
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
use \DEV\Websites\templates\wsTemplate;
use \DEV\Websites\templates\wsTemplateThemeCSS;
use \DEV\Websites\website;

$websiteID = engine::getVar('id');
if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	if (empty($_POST['tname']))
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
		
	// Add css to the theme
	$themeName = $_POST['tname'];
	$parts = explode(":", $themeName);
	$templateName = $parts[0];
	$themeName = $parts[1];
	$themeJS = new wsTemplateThemeCSS($websiteID, $templateName, $themeName);
	$status = $themeJS->create($_POST['css_name']);
	
	// If there is an error in creating the library, show it
	if (!$status)
	{
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error adding theme css..."));
		return $errFormNtf->getReport();
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
$title = moduleLiteral::get($moduleID, "lbl_addThemeCSS");
$frame->build($title, "", FALSE)->engageModule($moduleID, "addCSS");
$form = $frame->getFormFactory();

// Application ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $websiteID, $class = "", $autofocus = TRUE);
$frame->append($input);

// Get all templates
$website = new website($websiteID);
$templates = $website->getTemplates();
$themeResource = array();
foreach ($templates as $templateName)
{
	// Get all template themes
	$template = new wsTemplate($websiteID, $templateName);
	$themes = $template->getThemes();
	foreach ($themes as $themeName)
		$themeResource[$templateName.":".$themeName] = $templateName." > ".$themeName;
}
ksort($themeResource);

$title = moduleLiteral::get($moduleID, "lbl_themeName");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "tname", $multiple = FALSE, $class = "", $themeResource, $selectedValue = "");
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

// View Name
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "css_name", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>