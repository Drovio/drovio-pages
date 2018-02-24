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
use \UI\Forms\formReport\formNotification;
use \DEV\Websites\settings\wsSettings;
use \DEV\Websites\settings\metaSettings;
use \DEV\Websites\settings\wsRobots;

// Initialize website id and website settings
$websiteID = engine::getVar("id");
$wsSettings = new wsSettings($websiteID);
$wsSettings->create();
$metaSettings = new metaSettings($websiteID);
$metaSettings->create();
$wsRobots = new wsRobots($websiteID);

if (engine::isPost())
{
	// Set meta information
	$metaSettings->set("meta_description", $_POST['description']);
	$metaSettings->set("meta_keywords", $_POST['keywords']);
	
	// Build success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}



$mcontent = new MContent($moduleID);
$mcontent->build("", "wsMetaInfoEditor", TRUE);


// Main meta information form
$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "metaInformation")->get();
$content = HTML::select(".mainMeta .formContainer")->item(0);
DOM::append($content, $editorForm);

// Website id
$input = $form->getInput("hidden", "id", $websiteID);
$form->append($input);

// Website default meta description
$title = moduleLiteral::get($moduleID, "lbl_meta_description");
$metaDescription = $metaSettings->get("meta_description");
$input = $form->getTextArea("description", $metaDescription, "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE);

// Website default meta keywords
$title = moduleLiteral::get($moduleID, "lbl_meta_keywords");
$metaKeywords = $metaSettings->get("meta_keywords");
$input = $form->getTextArea("keywords", $metaKeywords, "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE);


// Open graph meta information form
$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "openGraph")->get();
$content = HTML::select(".openGraph .formContainer")->item(0);
DOM::append($content, $editorForm);

// Website id
$input = $form->getInput("hidden", "id", $websiteID);
$form->append($input);

// Open graph enabled
$title = moduleLiteral::get($moduleID, "lbl_meta_og_enabled");
$notes = moduleLiteral::get($moduleID, "lbl_meta_og_enabled_notes");
$input = $form->getInput($type = "checkbox", $name = "enabled", $value, $class = "", $autofocus = FALSE, $required = FALSE);
$value = $metaSettings->get("meta_og_enabled");
if ($value)
	HTML::attr($input, "checked", TRUE);
$form->insertRow($title, $input, $required = FALSE, $notes);

// Open graph site name
$title = moduleLiteral::get($moduleID, "lbl_meta_og_sitename");
$value = $metaSettings->get("meta_og_sitename");
$input = $form->getInput($type = "text", $name = "site_name", $value, $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE);

// Open graph site type
$title = moduleLiteral::get($moduleID, "lbl_meta_og_type");
$value = $metaSettings->get("meta_og_type");
$input = $form->getInput($type = "text", $name = "type", $value, $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE);

// Open graph site image
$title = moduleLiteral::get($moduleID, "lbl_meta_og_image");
$value = $metaSettings->get("meta_og_image");
$input = $form->getInput($type = "text", $name = "image", $value, $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE);


// Website robots form
$form = new simpleForm();
$editorForm = $form->build()->engageModule($moduleID, "updateRobots")->get();
$content = HTML::select(".robots .formContainer")->item(0);
DOM::append($content, $editorForm);

// Website id
$input = $form->getInput("hidden", "id", $websiteID);
$form->append($input);

// Website robots
$title = moduleLiteral::get($moduleID, "lbl_ws_robots");
$input = $form->getTextArea("robots", $wsRobots->get(), "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE);

// Return output
return $mcontent->getReport();
//#section_end#
?>