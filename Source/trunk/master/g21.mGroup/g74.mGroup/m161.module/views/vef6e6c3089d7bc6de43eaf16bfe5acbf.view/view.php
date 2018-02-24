<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

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
importer::import("API", "Security");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$pageContent = new MContent($moduleID);

if (engine::isPost())
{
	// Regenerate key
	$akey = accountKey::create($_POST['gid'], $_POST['tid'], $_POST['ctx']);
	if ($akey)
	{
		$holder = ".k".$_POST['gid'].$_POST['tid'].$_POST['ctx'];
		$element = DOM::create("span", $akey);
		$pageContent->buildElement($element);
	}
	
	return $pageContent->getReport($holder);
}

return FALSE;
//#section_end#
?>