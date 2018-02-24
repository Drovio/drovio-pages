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
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

if (engine::isPost())
{
	// Get application id
	$applicationID = engine::getVar("id");
	
	// Get team ids to add as private
	$teamIDs = engine::getVar("tms");
	
	$status = FALSE;
	foreach ($teamIDs as $teamID => $nothing)
	{
		// Add private team
		$dbc = new dbConnection();
		$q = module::getQuery($moduleID, "add_private_team");
		$attr = array();
		$attr['tid'] = $teamID;
		$attr['pid'] = $applicationID;
		$attr['time'] = time();
		$status = $dbc->execute($q, $attr);
	}
	
	if ($status)
	{
		$succFormNtf = new formNotification();
		$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = FALSE);
		
		// Notification Message
		$errorMessage = DOM::create("h2", "Teams successfully added as private.");
		$succFormNtf->append($errorMessage);
		
		$succFormNtf->addReportAction("boss_settings.reload");
		return $succFormNtf->getReport();
	}
}

return FALSE;
//#section_end#
?>