<?php
//#section#[header]
// Module Declaration
$moduleID = 407;

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
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \SYS\Comm\db\dbConnection;

$pageContent = new MContent($moduleID);
if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();

	// Check title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
				
		// Header
		$err_header = $errFormNtf->addHeader("Title");
		$errFormNtf->addDescription($err_header, "err.required");
	}
	
	// Check hashtag
	if (empty($_POST['hashtag']))
	{
		$has_error = TRUE;
				
		// Header
		$err_header = $errFormNtf->addHeader("Hashtag");
		$errFormNtf->addDescription($err_header, "err.required");
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	
	// Create roadmap item
	$dbc = new dbConnection();
	$q = $pageContent->getQuery("add_roadmap_item");
	$attr = array();
	$attr['title'] = $_POST['title'];
	$attr['description'] = $_POST['description'];
	$attr['hashtag'] = str_replace(" ", "_", trim($_POST['hashtag']));
	$attr['date_expected'] = $_POST['date_expected'];
	$attr['time'] = time();
	$result = $dbc->execute($q, $attr);
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err = $errFormNtf->addHeader("Roadmap item error");
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating roadmap..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Refresh the explorer
	$succFormNtf->addReportAction("roadmap.list.reload");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}
//#section_end#
?>