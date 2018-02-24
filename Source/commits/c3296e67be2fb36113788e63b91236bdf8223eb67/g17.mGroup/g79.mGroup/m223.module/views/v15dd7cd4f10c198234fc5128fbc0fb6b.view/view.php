<?php
//#section#[header]
// Module Declaration
$moduleID = 223;

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
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \DEV\Websites\website;
use \DEV\Websites\settings\wsSettings;

// Initialize website id and website settings
$websiteID = engine::getVar("id");
$wsSettings = new wsSettings($websiteID);
$wsSettings->create();

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check website tite
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_websiteTitle");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Update website information
	$website = new website($websiteID);
	$status =  $website->updateInfo($_POST['title'], $_POST['description']);
	
	// If error, show notification
	if (!$status)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_websiteInfo");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error updating website information..."));
		return $errFormNtf->getReport(FALSE);
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Get website information
$website = new website($websiteID);
$websiteInfo = $website->info();

$mcontent = new MContent($moduleID);
$mcontent->build("", "webInfoEditor", TRUE);

// Create project settings form
$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "projectInfo")->get();
$formContainer = HTML::select(".webInfoEditor .projectInfo .formContainer")->item(0);
DOM::append($formContainer, $editorForm);

$input = $form->getInput("hidden", "id", $websiteID, "", $autodocus = FALSE, $required = TRUE);
$form->append($input);

$title = moduleLiteral::get($moduleID, "lbl_websiteTitle");
$input = $form->getInput("text", "title", $websiteInfo['title'], "", $autodocus = TRUE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE);

$title = moduleLiteral::get($moduleID, "lbl_websiteDescription");
$input = $form->getTextArea("description", $websiteInfo['description'], "", $autodocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE);


// Create domain settings form
$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "domainSettings")->get();
$formContainer = HTML::select(".webInfoEditor .domainSettings .formContainer")->item(0);
DOM::append($formContainer, $editorForm);

$input = $form->getInput("hidden", "id", $websiteID, "", TRUE, TRUE);
$form->append($input);

// Website url
$title = moduleLiteral::get($moduleID, "lbl_siteUrl");
$notes = moduleLiteral::get($moduleID, "notes_siteUrl");
$site_url = $wsSettings->get('site_url');
if (empty($site_url)) // compatibility
	$site_url = $wsSettings->get('url');
$input = $form->getInput($type = "text", $name = "domain[site_url]", $site_url, $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes);

// Website root folder
$title = moduleLiteral::get($moduleID, "lbl_webroot");
$notes = moduleLiteral::get($moduleID, "lbl_webroot_notes");
$web_root = $wsSettings->get('web_root');
$input = $form->getInput($type = "text", $name = "domain[web_root]", $web_root, $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes);


// Create generic settings form
$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "genericSettings")->get();
$formContainer = HTML::select(".webInfoEditor .genericSettings .formContainer")->item(0);
DOM::append($formContainer, $editorForm);

$input = $form->getInput("hidden", "id", $websiteID, "", TRUE, TRUE);
$form->append($input);

// Website favicon
$title = moduleLiteral::get($moduleID, "lbl_faviconUrl");
$notes = moduleLiteral::get($moduleID, "lbl_faviconUrl_notes");
$favicon_url = $wsSettings->get('favicon_url');
$input = $form->getInput($type = "text", $name = "favicon_url", $favicon_url, $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes);


// Return output
return $mcontent->getReport();
//#section_end#
?>