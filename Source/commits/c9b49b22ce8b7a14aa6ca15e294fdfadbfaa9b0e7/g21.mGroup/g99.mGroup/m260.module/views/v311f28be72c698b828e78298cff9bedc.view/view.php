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
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Profile\team;
use \API\Literals\moduleLiteral;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	if (empty($_POST['name']))
	{
		$hasError = TRUE;
		
		// Header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
	}
	if ($hasError)
		return $errFormNtf->getReport();
	
	// Update team name
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "update_team_name");
	$attr = array();
	$attr['name'] = $_POST['name'];
	$attr['tid'] = team::getTeamID();
	$result = $dbc->execute($q, $attr);
	
	if (!$result)
	{
		// Header
		$err_header = DOM::create("div", "Error Team Name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalid"));
		return $errFormNtf->getReport();
	}
	
	// Success notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	
	return $succFormNtf->getReport(FALSE);
}

return FALSE;
//#section_end#
?>