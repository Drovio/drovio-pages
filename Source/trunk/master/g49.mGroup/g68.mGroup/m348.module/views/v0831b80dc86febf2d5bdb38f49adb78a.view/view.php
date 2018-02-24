<?php
//#section#[header]
// Module Declaration
$moduleID = 348;

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
importer::import("DEV", "Apps");
importer::import("UI", "Interactive");
//#section_end#
//#section#[code]
use \DEV\Apps\appManifest;
use \UI\Interactive\forms\switchButton;

if (engine::isPost())
{
	// Get application id
	$appID = engine::getVar('id');
	
	// Get application manifest
	$mf = new appManifest($appID);
	
	// Get package name and status
	$packageID = engine::getVar('mf_id');
	// This is the current value of the package, so if it is disabled -> activate, enabled -> deactivate
	$packageValue = engine::getVar('mf_status');
	
	// Update permissions
	$perm = $mf->getPermissions();
	$index = array_search($packageID, $perm);
	if ($index === FALSE)
		$perm[] = $packageID;
	else
		unset($perm[$index]);
	$mf->setPermissions($perm);
	
	// Return report status
	return switchButton::getReport($index === FALSE);
}
return FALSE;
//#section_end#
?>