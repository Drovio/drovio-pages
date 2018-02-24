<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("API", "Security");
importer::import("UI", "Content");
//#section_end#
//#section#[code]
use \API\Resources\DOMParser;
use \API\Security\privileges;
use \UI\Content\JSONContent;

$groupID = engine::getVar('userGroup');
$groupModules = privileges::getPermissionGroupModules($groupID);

$userGroupModules = array();
$userGroupModules['gid'] = $_GET['userGroup'];
foreach ($groupModules as $module_id)
	$userGroupModules['modules'][$module_id] = $module_id;

// Create JSON content
$js = new JSONContent();
return $js->getReport($userGroupModules);
//#section_end#
?>