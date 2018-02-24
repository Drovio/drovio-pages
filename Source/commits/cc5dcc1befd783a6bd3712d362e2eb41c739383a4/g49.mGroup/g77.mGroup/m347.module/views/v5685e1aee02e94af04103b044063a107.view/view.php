<?php
//#section#[header]
// Module Declaration
$moduleID = 347;

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
importer::import("ESS", "Protocol");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \UI\Modules\MContent;
use \DEV\Literals\literal;

// Get project id and literal scope
$projectID = engine::getVar('id');
$literalScope = engine::getVar('scope');

// Build MContent
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "scopeLiterals");

// Get scopes
$litMenu = DOM::create("ul", "", "", "literals");
$pageContent->append($litMenu);
$scLiterals = literal::get($projectID, $literalScope);
asort($scLiterals);
foreach ($scLiterals as $name => $literal)
{
	// Create scope item
	$li = DOM::create("li", $name, "", "lt");
	NavigatorProtocol::staticNav($li, "", "", "", "ltGroup", $display = "none");
	DOM::append($litMenu, $li);
	
	// Set action
	$attr = array();
	$attr['id'] = $projectID;
	$attr['scope'] = $literalScope;
	$attr['name'] = $name;
	$actionFactory->setModuleAction($li, $moduleID, "ltrContainer", ".transContent", $attr, $loading = TRUE);
}

// Switch to literals and add handler
$pageContent->addReportAction("translations.switchto.literals", $literalScope);

return $pageContent->getReport();
//#section_end#
?>