<?php
//#section#[header]
// Module Declaration
$moduleID = 184;

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
importer::import("API", "Resources");
importer::import("UI", "Content");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Resources\DOMParser;
use \UI\Content\JSONContent;

$dbc = new dbConnection();
$dbq = new dbQuery("1469641106", "security.privileges.user");
$attr = array();
$attr['gid'] = $_GET['userGroup'];
$result = $dbc->execute($dbq, $attr);

$userGroupModules = array();
$userGroupModules['gid'] = $_GET['userGroup'];
while ($gModule = $dbc->fetch($result))
	$userGroupModules[$gModule['id']] = $gModule['id'];

// Create JSON content
$js = new JSONContent();
return $js->getReport($userGroupModules);
//#section_end#
?>