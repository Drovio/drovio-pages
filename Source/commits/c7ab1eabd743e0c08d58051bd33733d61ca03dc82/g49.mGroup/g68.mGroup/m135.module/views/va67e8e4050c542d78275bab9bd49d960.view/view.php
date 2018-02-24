<?php
//#section#[header]
// Module Declaration
$moduleID = 135;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Apps\application;
use \DEV\Apps\components\appSettings;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Initialize application
	$appID = $_POST['appID'];
	$appSettings = new appSettings($appID);
		
	// Update application settings
	$settings = $_POST['settings'];
	foreach ($settings as $key => $value)
		$appSettings->set($key, $value);
	
	$result = TRUE;

	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_libraryName");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error updating application..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_appSettingsTitle");
$frame->build($title, $moduleID, "appSettings", TRUE);

// Validate and Load application data
$appID = $_GET['appID'];

// Get application
$devApp = new application($appID);
$appSettings = new appSettings($appID);
$form = new simpleForm();

// Application id
$input = $form->getInput($type = "hidden", $name = "appID", $value = $appID, $class = "", $autofocus = FALSE);
$frame->append($input);

// Application Startup View
$views = $devApp->getViews();
$resource = array();
foreach ($views as $view)
	$resource[$view] = $view;
$title = moduleLiteral::get($moduleID, "lbl_startupView");
$selectedValue = $appSettings->get("STARTUP_VIEW");
$input = $form->getResourceSelect($name = "settings[STARTUP_VIEW]", $multiple = FALSE, $class = "", $resource, $selectedValue);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($formRow);

// Application Default Locale
$availableLocale = locale::active();
$resource = array();
foreach ($availableLocale as $key => $locale)
	$resource[$locale['locale']] = $locale['friendlyName'];
$title = moduleLiteral::get($moduleID, "lbl_defaultLocale");
$selectedValue = $appSettings->get("DEFAULT_LOCALE");
$input = $form->getResourceSelect($name = "settings[DEFAULT_LOCALE]", $multiple = FALSE, $class = "", $resource, $selectedValue);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($formRow);


// Return the report
return $frame->getFrame();
//#section_end#
?>