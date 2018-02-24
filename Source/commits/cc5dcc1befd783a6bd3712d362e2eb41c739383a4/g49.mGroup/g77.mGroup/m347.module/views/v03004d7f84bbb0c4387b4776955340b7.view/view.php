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
importer::import("API", "Literals");
importer::import("DEV", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Literals\literal;
use \DEV\Literals\translator;

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$projectID = engine::getVar('id');
$literalScope = engine::getVar('scope');
$literalName = engine::getVar('name');
$literalLocale = engine::getVar('locale');

if (engine::isPost())
{
	// Check Action
	if ($_POST['action'] == "translate")
		translator::translate($projectID, $literalScope, $literalName, $_POST['translation'], $literalLocale);
	else if ($_POST['action'] == "reset")
	{
		// Remove literal value
		literal::removeValue($projectID, $literalScope, $literalName, $literalLocale);
	}
	else if ($_POST['action'] == "lock")
	{
		// Lock translation
		translator::lock($_POST['translation_id']);
	}
	
	// Reload translations
	$pageContent->addReportAction("translations.reload");
	return $pageContent->getReport($reportHolder);
}
//#section_end#
?>