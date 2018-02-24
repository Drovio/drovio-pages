<?php
//#section#[header]
// Module Declaration
$moduleID = 246;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Modules");
importer::import("DEV", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\mGroup;
use \API\Model\modules\module as APIModule;
use \API\Profile\account;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \UI\Interactive\forms\formAutoComplete;
use \DEV\Modules\modulesProject;
use \DEV\Profile\developer as moduleDevProfile;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the page
$page->build("", "modulesTesterPage", TRUE);
$controlPanel = HTML::select(".testingControlPanel")->item(0);

// Form Control
$form = new simpleForm();
$controlForm = $form->build("", FALSE)->engageModule($moduleID, "testModule")->get();
DOM::append($controlPanel, $controlForm);

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

$frow = DOM::create("div", "", "", "frow");
$form->append($frow);

// Get Module Groups
$moduleGroupsRaw = mGroup::getAllGroups();
foreach ($moduleGroupsRaw as $info)
{
	// Check module in workspace
	if (!moduleDevProfile::moduleGroupInWorkspace($info['id']))
		continue;
		
	$moduleGroups[$info['id']] = $info['description'];
	$moduleGroups_depths[$info['id']] = $info['depth'];
}
foreach ($moduleGroups_depths as $id => $depth)
{
	$tabs = str_repeat(" - ", $depth);
	$moduleGroups[$id] = $tabs.$moduleGroups[$id];
}

$moduleGroupInput = $form->getResourceSelect($name = "moduleGroup", $multiple = FALSE, $class = "tinp", $moduleGroups, $selectedModuleParent);
DOM::append($frow, $moduleGroupInput);

// Get group modules
$moduleParents = APIModule::getAllModules(array_shift(array_keys($moduleGroups)));
foreach ($moduleParents as $info)
	$moduleParents_resource[$info['id']] = $info['title'];

$moduleInput = $form->getResourceSelect($name = "testerModuleID", $multiple = FALSE, $class = "tinp", $moduleParents_resource, "");
DOM::append($frow, $moduleInput);

// Auto Complete
$autoComplete = new formAutoComplete();
$populate = array();
$populate[] = $moduleInput->getAttribute("id");
$autoComplete->engage($moduleGroupInput, "/ajax/modules/testerGroupModules.php", $fill = array(), $hide = array(), $populate, $mode = "lenient");


// Test button
$submitButton = $form->getSubmitButton("Test");
HTML::addClass($submitButton, "wbutton");
$form->append($submitButton);


// Return output
return $page->getReport();
//#section_end#
?>