<?php
//#section#[header]
// Module Declaration
$moduleID = 254;

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
//---------- AUTO-GENERATED CODE ----------//
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;


// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "projectSettingsEditor");

//Create Form
$form = new simpleForm();
$wehForm = $form->build($moduleID,"WebsiteCreator",TRUE,FALSE)->get();
$pageContent->append($wehForm);

//Populate form
$title = "Project Title";
$label = $form->getLabel($title);
$input= $form->getInput("text", "pTitle", "","pageFields",TRUE,TRUE);
$form->insertRow($label, $input);

// Add a hello world dynamic content
$hw = DOM::create("p", "Hello World!");
$pageContent->append($hw);

// Return output
return $pageContent->getReport();
//#section_end#
?>