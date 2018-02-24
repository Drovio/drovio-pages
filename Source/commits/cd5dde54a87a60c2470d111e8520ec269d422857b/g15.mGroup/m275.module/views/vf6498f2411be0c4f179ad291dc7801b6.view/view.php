<?php
//#section#[header]
// Module Declaration
$moduleID = 275;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "marketManager", TRUE);


$navBar = HTML::select(".navBar")->item(0);
$rvSections = HTML::select("#rvSections")->item(0);

// Navigation attributes
$targetcontainer = "rvSections";
$targetgroup = "mrvNavGroup";
$navgroup = "mrvNav";

// Authors
$navTitle = HTML::select(".navTitle.pending")->item(0);
NavigatorProtocol::staticNav($navTitle, "rvPending", $targetcontainer, $targetgroup, $navgroup, $display = "none");

$navTitle = HTML::select(".navTitle.reviewed")->item(0);
NavigatorProtocol::staticNav($navTitle, "rvReviewed", $targetcontainer, $targetgroup, $navgroup, $display = "none");

// Add sections

// Create navigation container
$navContainer = $page->getNavigationGroup("rvPending", $targetgroup);
HTML::addClass($navContainer, "rvSectContainer");
DOM::append($rvSections, $navContainer);

$moduleContainer = $page->getModuleContainer($moduleID, "pendingProjects", array(), $startup = TRUE, $containerID = "pendingProjects");
DOM::append($navContainer, $moduleContainer);

// Create navigation container
$navContainer = $page->getNavigationGroup("rvReviewed", $targetgroup);
HTML::addClass($navContainer, "rvSectContainer");
DOM::append($rvSections, $navContainer);

$moduleContainer = $page->getModuleContainer($moduleID, "reviewedProjects", array(), $startup = TRUE, $containerID = "reviewedProjects");
DOM::append($navContainer, $moduleContainer);


// Return output
return $page->getReport();
//#section_end#
?>