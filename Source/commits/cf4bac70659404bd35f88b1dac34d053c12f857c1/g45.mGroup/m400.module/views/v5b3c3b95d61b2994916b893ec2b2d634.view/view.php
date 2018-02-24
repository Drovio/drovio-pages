<?php
//#section#[header]
// Module Declaration
$moduleID = 400;

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
importer::import("API", "Security");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Security\akeys\apiKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the application view content
$pageContent->build("", "keyInfoContainer", TRUE);

// Get app id and key
$applicationID = engine::getVar("app_id");
$akey = engine::getVar("akey");
if (engine::isPost())
{
	// Create error notification
	$errorFormNtf = new formErrorNotification();
	$errorFormNtf->build();
	
	// Validate form and create new key
	if (!simpleForm::validate())
	{
		$errorMessage = $errorFormNtf->getMessage("error", "err.save_error");
		$errorFormNtf->append($errorMessage);
		return $errorFormNtf->getReport();
	}
	
	// Regenerate key and get new key to reference
	$keyType = engine::getVar("key_type");
	$akey = apiKey::regenerateKey($akey);
	
	// Check status
	if (!$akey)
		return $errorFormNtf->getReport();
	
	// Set action to refresh keys
	$pageContent->addReportAction($name = "team.keys.list.reload", $value = $applicationID);
}

// Get key information (it doesn't matter which class I use)
$keyInfo = apiKey::info($akey);

$holder = HTML::select(".keyInfo .kr.akey .value")->item(0);
HTML::innerHTML($holder, $keyInfo['akey']);

$holder = HTML::select(".keyInfo .kr.type .value")->item(0);
HTML::innerHTML($holder, $keyInfo['type_name']);

$holder = HTML::select(".keyInfo .kr.date .value")->item(0);
HTML::innerHTML($holder, date("M d, Y, H:i:s", $keyInfo['time_created']));

// Check for extra information
if (!empty($keyInfo['previous_akey']))
{
	$holder = HTML::select(".keyInfo .kr.previous_akey .value")->item(0);
	HTML::innerHTML($holder, $keyInfo['previous_akey']);

	$holder = HTML::select(".keyInfo .kr.exdate .value")->item(0);
	HTML::innerHTML($holder, date("M d, Y, H:i:s", $keyInfo['time_expires']));
}
else
{
	$kr = HTML::select(".keyInfo .kr.previous_akey")->item(0);
	HTML::remove($kr);
	$kr = HTML::select(".keyInfo .kr.exdate")->item(0);
	HTML::remove($kr);
}



// Set key actions
$actionsContainer = HTML::select(".keyInfo .keyActions")->item(0);

// Remove key form
$form = new simpleForm();
$removeForm = $form->build("", FALSE)->engageModule($moduleID, "removeKey")->get();
HTML::addClass($removeForm, "actionForm");
HTML::append($actionsContainer, $removeForm);

// key value hidden input
$input = $form->getInput($type = "hidden", $name = "app_id", $value = $keyInfo['project_id'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "akey", $value = $akey, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// key type hidden input
$input = $form->getInput($type = "hidden", $name = "key_type", $value = $keyInfo['type_id'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Submit button
$title = $pageContent->getLiteral("lbl_removeKey");
$removeKey = $form->getSubmitButton($title);
HTML::addClass($removeKey, "key_action remove");
$form->append($removeKey);


// Regenerate key form
$form = new simpleForm();
$regenerateForm = $form->build("", FALSE)->engageModule($moduleID, "keyInfo")->get();
HTML::addClass($regenerateForm, "actionForm");
HTML::append($actionsContainer, $regenerateForm);

// key value hidden input
$input = $form->getInput($type = "hidden", $name = "app_id", $value = $keyInfo['project_id'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "akey", $value = $akey, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// key type hidden input
$input = $form->getInput($type = "hidden", $name = "key_type", $value = $keyInfo['type_id'], $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Submit button
$title = $pageContent->getLiteral("lbl_regenerateKey");
$removeKey = $form->getSubmitButton($title);
HTML::addClass($removeKey, "key_action regenerate");
$form->append($removeKey);



// Return output
return $pageContent->getReport("#keyInfoContainer", MContent::REPLACE_METHOD);
//#section_end#
?>