<?php
//#section#[header]
// Module Declaration
$moduleID = 160;

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
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Resources\filesystem\fileManager;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;

// Initialize
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

if (engine::isPost())
{
	// Update team profile image
	if (!empty($_FILES['profile_image']))
	{
		$image = fileManager::get($_FILES['profile_image']['tmp_name']);
		account::updateProfileImage($image);
	}
	
	// Return success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build Content
$pageContent->build("", "profileImageContainer", TRUE);

// Get account info and check for image url
$accountInfo = account::info();
if (isset($accountInfo['profile_image_url']))
{
	$imageContainer = HTML::select(".profileImage .imageContainer")->item(0);
	$img = DOM::create("img");
	DOM::attr($img, "src", $accountInfo['profile_image_url']);
	DOM::append($imageContainer, $img);
}

// Build form
$formContainer = HTML::select(".profileImage .formContainer")->item(0);
$form = new simpleForm("");
$imageForm = $form->build($action = "", $defaultButtons = TRUE, $async = TRUE, $fileUpload = TRUE)->engageModule($moduleID, "profileImage")->get();
DOM::append($formContainer, $imageForm);

// Team profile image
$title = moduleLiteral::get($moduleID, "lbl_account_profile_image");
$notes = moduleLiteral::get($moduleID, "lbl_account_profile_image_notes");
$input = $form->getFileInput($name = "profile_image", $class = "", $required = FALSE, $accept = ".png");
$form->insertRow($title, $input, $required = TRUE, $notes);

return $pageContent->getReport();
//#section_end#
?>