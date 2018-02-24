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
importer::import("BSS", "Market");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Security\akeys\apiKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \BSS\Market\appMarket;

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the application view content
$pageContent->build("", "enterprise_appKeysContainer", TRUE);
$appKeys = HTML::select(".enpKeys")->item(0);

// Get application id
$applicationID = engine::getVar("app_id");

// Get application info
$applicationInfo = appMarket::getApplicationInfo($applicationID);
$appTitle = HTML::select("h1.hd.app_title")->item(0);
HTML::innerHTML($appTitle, $applicationInfo['title']." (".$applicationID.".".$applicationInfo['name'].")");

// Get all application keys as a team
$teamKeys = apiKey::getProjectTeamKeys($applicationID);
if (!empty($teamKeys))
{
	// Empty list
	$list = HTML::select(".enpKeys .list")->item(0);
	HTML::innerHTML($list, "");
	
	// Create data grid list
	$gridList = new dataGridList();
	$keysList = $gridList->build($id = "keysList", $checkable = FALSE, $withBorder = TRUE)->get();
	DOM::append($list, $keysList);
	
	// Set column ratios
	$ratios = array();
	$ratios['type'] = 0.2;
	$ratios['date'] = 0.2;
	$ratios['akey'] = 0.5;
	$ratios['actions'] = 0.1;
	$gridList->setColumnRatios($ratios);
	
	// Set headers
	$headers = array();
	$headers['type'] = "Type";
	$headers['date'] = "Date Created";
	$headers['akey'] = "API Key";
	$headers['actions'] = "Actions";
	$gridList->setHeaders($headers);
	
	// Show all keys
	foreach ($teamKeys as $keyInfo)
	{
		// Key row
		$row = array();
		$row['type'] = $keyInfo['type_name'];
		$row['date'] = date('M d, Y', $keyInfo['time_created']);
		$row['akey'] = $keyInfo['akey'];
		
		// Create action container
		$actionContainer = DOM::create("div", "", "", "keyActionContainer");
		
		// Edit action (show popup)
		$editKey = DOM::create("div", "", "", "act edit");
		DOM::append($actionContainer, $editKey);
		
		// Set edit action
		$attr = array();
		$attr['app_id'] = $keyInfo['project_id'];
		$attr['akey'] = $keyInfo['akey'];
		$actionFactory->setModuleAction($editKey, $moduleID, "editKeyDialog", "", $attr);
		
		// Remove key form
		$form = new simpleForm();
		$removeKeyForm = $form->build("", FALSE)->engageModule($moduleID, "removeKey")->get();
		HTML::addClass($removeKeyForm, "keyForm");
		DOM::append($actionContainer, $removeKeyForm);
		
		// key value hidden input
		$input = $form->getInput($type = "hidden", $name = "app_id", $value = $keyInfo['project_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "akey", $value = $keyInfo['akey'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// key type hidden input
		$input = $form->getInput($type = "hidden", $name = "key_type", $value = $keyInfo['type_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit button
		$removeKey = $form->getSubmitButton();
		HTML::addClass($removeKey, "act remove");
		$form->append($removeKey);
		
		$row['action'] = $actionContainer;
		
		// Insert row
		$gridList->insertRow($row);
	}
}

// Activate control action
$controls = HTML::select(".enpKeys .controls")->item(0);

// Create form for adding new public key
$form = new simpleForm();
$newKeyForm = $form->build("", FALSE)->engageModule($moduleID, "createNewKey")->get();
DOM::append($controls, $newKeyForm);

$input = $form->getInput($type = "hidden", $name = "app_id", $value = $applicationID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "key_type", $value = "public", $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Add submit button
$title = $pageContent->getLiteral("lbl_newPublicKey");
$submitButton = $form->getSubmitButton($title);
HTML::addClass($submitButton, "ctrl add_key");
$form->append($submitButton);

// Create form for adding new private key
$form = new simpleForm();
$newKeyForm = $form->build("", FALSE)->engageModule($moduleID, "createNewKey")->get();
DOM::append($controls, $newKeyForm);

$input = $form->getInput($type = "hidden", $name = "app_id", $value = $applicationID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

$input = $form->getInput($type = "hidden", $name = "key_type", $value = "private", $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Add submit button
$title = $pageContent->getLiteral("lbl_newPrivateKey");
$submitButton = $form->getSubmitButton($title);
HTML::addClass($submitButton, "ctrl add_key");
$form->append($submitButton);

// Return output
return $pageContent->getReport();
//#section_end#
?>