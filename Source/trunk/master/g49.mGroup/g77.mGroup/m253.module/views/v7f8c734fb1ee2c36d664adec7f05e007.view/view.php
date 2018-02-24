<?php
//#section#[header]
// Module Declaration
$moduleID = 253;

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
importer::import("DEV", "Literals");
importer::import("DEV", "Projects");
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \DEV\Literals\literal;
use \DEV\Projects\project;


// Build MContent
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContainer = $pageContent->build("", "scopeExplorerContainer")->get();

// Get project id
$projectID = engine::getVar('id');
// Get selected scope
$selectedScopeName = engine::getVar("sname");

// Add navigation bar to scope explorer
$toolbar = new navigationBar();
$navBar = $toolbar->build(navigationBar::TOP, $pageContainer)->get();
$pageContent->append($navBar);

// Refresh scopes
$refreshTool = DOM::create("span", "", "", "scTool refresh");
$tool = $toolbar->insertToolbarItem($refreshTool);

// Insert create new scope tool
$createTool = DOM::create("span", "", "", "scTool create_new");
$tool = $toolbar->insertToolbarItem($createTool);
$attr = array();
$attr['id'] = $projectID;
$attr['pid'] = $projectID;
$actionFactory->setModuleAction($createTool, $moduleID, "createNewScope", ".literalEditorContainer .literalsContent", $attr);


$scopeMenu = DOM::create("ul", "", "", "scopes");
$pageContent->append($scopeMenu);
$pScopes = literal::getScopes($projectID);
foreach ($pScopes as $scope)
	$scopes[] = $scope['scope'];
	
asort($scopes);
foreach ($scopes as $scope)
{
	// Create scope item
	$li = DOM::create("li", $scope, "", "sc");
	NavigatorProtocol::staticNav($li, "", "", "", "scGroup", $display = "none");
	DOM::append($scopeMenu, $li);
	
	// Set selected
	if (!empty($selectedScopeName) && $selectedScopeName == $scope)
	{
		HTML::addClass($li, "selected");
		HTML::addClass($li, "init");
	}
	
	// Set action
	$attr = array();
	$attr['id'] = $projectID;
	$attr['scope'] = $scope;
	$actionFactory->setModuleAction($li, $moduleID, "scopeLiterals", ".literalEditorContainer .literalsContent", $attr);
}

return $pageContent->getReport();
//#section_end#
?>