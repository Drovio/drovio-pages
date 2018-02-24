<?php
//#section#[header]
// Module Declaration
$moduleID = 304;

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
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\moduleLiteral;
use \API\Geoloc\datetimer;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Interactive\forms\switchButton;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;

// Get application id
$applicationID = engine::getVar("id");

if (engine::isPost())
{
	// Initialize
	$dbc = new dbConnection();
	
	// Get audience type
	$type = $_POST['type'];
	if ($type == "apc")
		$q = module::getQuery($moduleID, "update_apc_audience");
	else if ($type == "enp")
		$q = module::getQuery($moduleID, "update_enp_audience");
	
	// Set status
	$newStatus = (empty($_POST['active']) || $_POST['active'] == 0 ? 1 : 0);
		
	// Execute query
	$attr = array();
	$attr['id'] = $applicationID;
	$attr['active'] = $newStatus;
	$result = $dbc->execute($q, $attr);
	
	// Return switch button status
	return switchButton::getReport($newStatus);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>