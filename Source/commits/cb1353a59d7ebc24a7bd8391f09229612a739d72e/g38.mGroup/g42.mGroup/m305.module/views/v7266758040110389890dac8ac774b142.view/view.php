<?php
//#section#[header]
// Module Declaration
$moduleID = 305;

// Inner Module Codes
$innerModules = array();
$innerModules['frontend'] = 70;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
importer::import("BSS", "WebDocs");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \BSS\WebDocs\wDoc;

$page = new MPage($moduleID);

// Build page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "RedbackPrivacyPage", TRUE);


// Load Terms of Use Document
$wDoc = new wDoc("", "Privacy");
$documentContent = $wDoc->load($locale = NULL, $public = TRUE, $teamID = 1);

$docContainer = HTML::select(".privacyPage .docContainer")->item(0);
DOM::innerHTML($docContainer, $documentContent);


// Load navigation bar
$navBar = HTML::select(".navBar")->item(0);
$navigationBar = module::loadView($innerModules['frontend'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load footer menu
$discoverPage = HTML::select(".privacyPage")->item(0);
$footerMenu = module::loadView($innerModules['frontend'], "footerMenu");
DOM::append($discoverPage, $footerMenu);

return $page->getReport();
//#section_end#
?>