<?php
//#section#[header]
// Module Declaration
$moduleID = 377;

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
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\team;
use \API\Profile\teamSettings;
use \API\Resources\filesystem\fileManager;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

// Get team data
$teamID = engine::getVar('tid');
$teamInfo = team::info($teamID);

// Initialize team settings
$ts = new teamSettings($teamID);

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	if (empty($_POST['name']))
	{
		$hasError = TRUE;
		
		// Header
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	if ($hasError)
		return $errFormNtf->getReport();
		
	// Update team information
	$status = team::updateInfo($_POST['name'], $_POST['description'], $_POST['uname'], $teamID);
	
	// Update team profile image
	if (!empty($_FILES['profile_image']))
	{
		$image = fileManager::get($_FILES['profile_image']['tmp_name']);
		team::updateProfileImage($image, $teamID);
	}
	
	// Update team settings
	$settings = array();
	$settings[] = "public_profile";
	$settings[] = "website_url";
	foreach ($settings as $sName)
		if (isset($_POST['settings'][$sName]))
			$ts->set($sName, $_POST['settings'][$sName]);
		else
			$ts->set($sName, NULL);
	
	if (!$status)
	{
		// Header
		$err_header = DOM::create("div", "Team information");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
		return $errFormNtf->getReport();
	}
	
	// Add reload action
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = DOM::create("h2", "Team information updated successfully added.");
	$succFormNtf->append($errorMessage);
	
	$succFormNtf->addReportAction("team.info.reload");
	return $succFormNtf->getReport($reset = FALSE);
	
}

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "teamInfoEditorContainer", TRUE);

// Build form
$form = new simpleForm();
$editorForm = $form->build($action = "", $defaultButtons = TRUE, $async = TRUE, $fileUpload = TRUE)->engageModule($moduleID, "editInfo")->get();
$formContainer = HTML::select(".teamInfoEditor .formContainer")->item(0);
DOM::append($formContainer, $editorForm);

// Team id
$input = $form->getInput($type = "hidden", $name = "tid", $value = $teamID, $class = "");
$form->append($input);

// Team name
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "name", $value = $teamInfo['name'], $class = "");
$form->insertRow($title, $input, $required = TRUE);

// Team description
$title = literal::dictionary("description");
$input = $form->getTextarea($name = "description", $value = $teamInfo['description'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->insertRow($title, $input, $required = FALSE);

// Team unique name
$title = moduleLiteral::get($moduleID, "lbl_info_uname");
$notes = moduleLiteral::get($moduleID, "lbl_info_uname_ph", array(), FALSE);
$input = $form->getInput($type = "text", $name = "uname", $value = $teamInfo['uname'], $class = "");
$form->insertRow($title, $input, $required = TRUE, $notes);

// Team profile image
$title = moduleLiteral::get($moduleID, "lbl_team_profile_image");
$input = $form->getFileInput($name = "profile_image", $class = "", $required = FALSE, $accept = ".png");
$form->insertRow($title, $input, $required = FALSE);


// Team settings
$settings = $ts->get();

// Team website url
$title = moduleLiteral::get($moduleID, "lbl_info_website_url");
$input = $form->getInput($type = "text", $name = "settings[website_url]", $value = $settings['WEBSITE_URL'], $class = "");
$form->insertRow($title, $input, $required = FALSE);

// Team public profile
$title = moduleLiteral::get($moduleID, "lbl_info_public_profile");
$input = $form->getInput($type = "checkbox", $name = "settings[public_profile]", $value = "1", $class = "");
if ($settings['PUBLIC_PROFILE'] == 1)
	DOM::attr($input, "checked", TRUE);
$form->insertRow($title, $input, $required = FALSE);

// Add switch action
$pageContent->addReportAction("team.info.edit");

// Return output
return $pageContent->getReport();
//#section_end#
?>