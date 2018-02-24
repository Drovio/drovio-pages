<?php
//#section#[header]
// Module Declaration
$moduleID = 172;

// Inner Module Codes
$innerModules = array();
$innerModules['viewEditor'] = 173;
$innerModules['appEditor'] = 135;
$innerModules['sourceEditor'] = 174;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("DEV", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Apps\application;
use \DEV\Apps\appSettings;

// Get application
$appID = engine::getVar('id');
$devApp = new application($appID);
$appSettings = new appSettings($appID);

if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Get all settings
	$postSettings = $_POST['settings'];
	$allSettings = $appSettings->get();
	foreach ($allSettings as $name => $value)
	{
		if (isset($postSettings[$name]))
			$appSettings->set($name, $postSettings[$name]);
		else
			$appSettings->set($name, NULL);
	}
		
	// Update new application settings
	foreach ($postSettings as $key => $value)
		$appSettings->set($key, $value);
	
	$result = TRUE;

	// If there is an error in application settings
	if (!$result)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_startupView");
		$err = $errFormNtf->addErrorHeader("libName_h", $err_header);
		$errFormNtf->addErrorDescription($err, "libName_desc", DOM::create("span", "Error updating application..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_appSettingsTitle");
$frame->build($title)->engageModule($moduleID);
$form = $frame->getFormFactory();

// Application id
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID, $class = "", $autofocus = FALSE);
$frame->append($input);

// Application Startup View
$allViews = $devApp->getAllViews();
$resource = array();
foreach ($allViews as $folderName => $views)
	foreach ($views as $viewName)
	{
		$fullName = (empty($folderName) ? "" : $folderName."/").$viewName;
		$resource[$fullName] = $fullName;
	}
$title = moduleLiteral::get($moduleID, "lbl_startupView");
$selectedValue = $appSettings->get("STARTUP_VIEW");
$input = $form->getResourceSelect($name = "settings[STARTUP_VIEW]", $multiple = FALSE, $class = "", $resource, $selectedValue);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($formRow);

$title = moduleLiteral::get($moduleID, "lbl_enpKeys");
$selectedValue = $appSettings->get("ALLOW_KEYS");
$input = $form->getInput($type = "checkbox", $name = "settings[ALLOW_KEYS]", $value = "", $class = "", $autofocus = FALSE);
if ($selectedValue)
	DOM::attr($input, "checked", TRUE);
$formRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($formRow);


// Return the report
return $frame->getFrame();
//#section_end#
?>