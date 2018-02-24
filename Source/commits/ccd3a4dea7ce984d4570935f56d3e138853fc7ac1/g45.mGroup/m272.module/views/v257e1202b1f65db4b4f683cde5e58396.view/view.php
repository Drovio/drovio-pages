<?php
//#section#[header]
// Module Declaration
$moduleID = 272;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\projectLibrary;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$appID = $_GET['appID'];
$lastAppVersion = projectLibrary::getLastProjectVersion($appID);
$pageContent->build("", "applicationUpdaterContainer", TRUE);

// Build the update actions
$form = new simpleForm();
$updaterForm = $form->build("", FALSE)->engageModule($moduleID, "updateApplication")->get();
$formContainer = HTML::select(".applicationUpdater .actions .updtr.formContainer")->item(0);
DOM::append($formContainer, $updaterForm);

// Application id
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID);
$form->append($input);

// Dismiss
$title = moduleLiteral::get($moduleID, "lbl_dismissUpdate", array(), FALSE);
$dismissBtn = $form->getButton($title, $name = "", $class = "dismiss");
$form->append($dismissBtn);

// Accept
$title = moduleLiteral::get($moduleID, "lbl_acceptUpdate", array(), FALSE);
$acceptBtn = $form->getSubmitButton($title, $id = "accept");
$form->append($acceptBtn);

// Build the new version details content
$attr = array();
$attr['version'] = $lastAppVersion;
$versionLit = moduleLiteral::get($moduleID, "lbl_newVersionTitle", $attr);
$dtitle = HTML::select(".dtitle")->item(0);
DOM::append($dtitle, $versionLit);

// Get release version info
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_version_info");
$attr = array();
$attr['pid'] = $appID;
$attr['version'] = $lastAppVersion;
$result = $dbc->execute($q, $attr);
$info = $dbc->fetch($result);
$dchangelog = HTML::select(".dchangelog")->item(0);
HTML::innerHTML($dchangelog, $info['changelog']);

// Return output
return $pageContent->getReport();
//#section_end#
?>