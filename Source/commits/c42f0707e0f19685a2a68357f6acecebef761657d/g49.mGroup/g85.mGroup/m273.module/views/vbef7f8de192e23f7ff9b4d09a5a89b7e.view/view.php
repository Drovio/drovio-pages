<?php
//#section#[header]
// Module Declaration
$moduleID = 273;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \UI\Presentation\frames\dialogFrame;
use \UI\Modules\MContent;
use \DEV\Modules\modulesProject;

// Get data
$literalModuleID = engine::getVar('mid');

// Build module content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "mLiteralsContainer", TRUE);

$ltList = HTML::select(".mLiteralsContainer .ltList")->item(0);
$attr = array();
$attr['mid'] = $literalModuleID;
$mContainer = $pageContent->getModuleContainer($moduleID, "ltList", $attr, $startup = FALSE, "ltListContainer", $loading = FALSE, $preload = TRUE);
DOM::append($ltList, $mContainer);

// Create windowFrame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_frameTitle");
$frame->build($title, "", FALSE)->engageModule($moduleID, "ltList");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Module ID to update literals
$input = $form->getInput($type = "hidden", $name = "mid", $value = $literalModuleID, $class = "", $autofocus = FALSE);
$form->append($input);

// Append content
$frame->append($pageContent->get());

// Return frame
return $frame->getFrame();
//#section_end#
?>