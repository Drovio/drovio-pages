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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Profile\account;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Presentation\gridSplitter;
use \UI\Forms\templates\simpleForm;
use \UI\Interactive\forms\formAutoComplete;
use \DEV\Modules\modulesProject;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the page
$page->build("", "modulesTesterPage", TRUE);



// Toolbar navigation
// Loader
$title = moduleLiteral::get($moduleID, "lbl_moduleLoader");
$subItem = $page->addToolbarNavItem("loaderNavSub", $title, $class = "selected", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID, "", ".prjContent");
NavigatorProtocol::staticNav($subItem, "", "", "", "topNav", $display = "none");


$title = moduleLiteral::get($moduleID, "lbl_testingTrunk");
$subItem = $page->addToolbarNavItem("trunkNavSub", $title, $class = "", NULL, $ribbonType = "float", $type = "obedient toggle", $pinnable = FALSE, $index = 0, FALSE);
$actionFactory->setModuleAction($subItem, $moduleID, "trunkPage", ".prjContent");
NavigatorProtocol::staticNav($subItem, "", "", "", "topNav", $display = "none");



$controlPanel = HTML::select(".testingControlPanel")->item(0);

// Form Control
$form = new simpleForm();
$controlForm = $form->build("", FALSE)->engageModule($moduleID, "testModule")->get();
DOM::append($controlPanel, $controlForm);

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Get Module Groups
$dbc = new dbConnection();
$dbq = new dbQuery("305935527", "security.privileges.tester");
$attr = array();
$attr['aid'] = account::getAccountID();
$moduleParents = $dbc->execute($dbq, $attr);

$moduleParentsDepth_resource = $dbc->toArray($moduleParents, "id", "depth");
$moduleParents_resource = $dbc->toArray($moduleParents, "id", "description");

foreach ($moduleParentsDepth_resource as $id => $depth)
{
	$tabs = "";
	if ($depth != 0)
		$tabs = str_repeat("   ", $depth)."- ";
	
	$moduleParents_resource[$id] = $tabs.$moduleParents_resource[$id];
}

$selectedModuleParent = array_keys($moduleParents_resource);
$selectedModuleParent = $selectedModuleParent[0];
$moduleGroupInput = $form->getResourceSelect($name = "moduleGroup", $multiple = FALSE, $class = "tinp", $moduleParents_resource, $selectedModuleParent);
$form->append($moduleGroupInput);

// Get Modules
$dbq = new dbQuery("666615842", "units.modules");
$attr = array();
$attr['gid'] = $selectedModuleParent;
$moduleParents = $dbc->execute($dbq, $attr);

$moduleParentsDepth_resource = $dbc->toArray($moduleParents, "id", "depth");
$moduleParents_resource = $dbc->toArray($moduleParents, "id", "title");
$moduleInput = $form->getResourceSelect($name = "moduleParent", $multiple = FALSE, $class = "tinp", $moduleParents_resource, "");
$form->append($moduleInput);

// Auto Complete
$autoComplete = new formAutoComplete();
$populate = array();
$populate[] = $moduleInput->getAttribute("id");
$autoComplete->engage($moduleGroupInput, "/ajax/modules/testerGroupModules.php", $fill = array(), $hide = array(), $populate, $mode = "lenient");


// Test button
$submitButton = $form->getSubmitButton("Test");
$form->append($submitButton);


// Return output
return $page->getReport();
//#section_end#
?>