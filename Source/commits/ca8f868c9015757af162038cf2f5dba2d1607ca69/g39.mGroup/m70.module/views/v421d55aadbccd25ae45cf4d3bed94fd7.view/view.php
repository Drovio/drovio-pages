<?php
//#section#[header]
// Module Declaration
$moduleID = 70;

// Inner Module Codes
$innerModules = array();
$innerModules['dSub'] = 366;
$innerModules['loginPopup'] = 319;

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
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Modules\MPage;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "rbFrontend", TRUE);

// Set login action to the button
$loginButton = HTML::select(".fsection.main .wbutton.team")->item(0);
if (!account::validate())
	$actionFactory->setModuleAction($loginButton, $innerModules['loginPopup'], "", "", $attr = array(), $loading = TRUE);
else
	$actionFactory->setModuleAction($loginButton, $innerModules['dSub'], "createTeam");


// Load footer menu
$frontendPage = HTML::select(".frontendPage")->item(0);
$footerMenu = $page->loadView($moduleID, "socialMenu");
DOM::append($frontendPage, $footerMenu);

return $page->getReport();
//#section_end#
?>