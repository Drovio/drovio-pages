<?php
//#section#[header]
// Module Declaration
$moduleID = 260;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "myTeamManager", TRUE);


// Team info editor
$teamInfo = HTML::select(".teamManager .teamInfo .tcontainer")->item(0);
$form = new simpleForm("teamInfoEditor");
$teamEditorForm = $form->build("", TRUE, TRUE)->engageModule($moduleID, "updateTeamInfo")->get();
DOM::append($teamInfo, $teamEditorForm);

// Team name
$title = moduleLiteral::get($moduleID, "lbl_teamName");
$input = $form->getInput($type = "text", $name = "name", $value = team::getTeamName(), $class = "", $autofocus = TRUE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE);

// Load team members
$teamMembers = HTML::select(".teamManager .teamMembers .tcontainer")->item(0);
$moduleContainer = $pageContent->getModuleContainer($moduleID, $viewName = "teamMembers", $attr, $startup = TRUE, $containerID = "", $loading = FALSE);
DOM::append($teamMembers, $moduleContainer);

// Return output
return $pageContent->getReport();
//#section_end#
?>