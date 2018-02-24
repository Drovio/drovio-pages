<?php
//#section#[header]
// Module Declaration
$moduleID = 400;

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
importer::import("API", "Security");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Security\akeys\apiKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;

// Create Module Content
$pageContent = new MContent();
$actionFactory = $pageContent->getActionFactory();

// Get application id
$applicationID = engine::getVar("app_id");

if (engine::isPost())
{
	// Create error notification
	$errorFormNtf = new formErrorNotification();
	$errorFormNtf->build();
	
	// Validate form and create new key
	if (!simpleForm::validate())
	{
		$errorMessage = $errorFormNtf->getMessage("error", "err.save_error");
		$errorFormNtf->append($errorMessage);
		return $errorFormNtf->getReport();
	}
	
	// Remove given key
	$akey = engine::getVar("akey");
	$keyType = engine::getVar("key_type");
	$status = apiKey::remove($akey);
	
	// Check status
	if (!$status)
		return $errorFormNtf->getReport();
	
	// Reload key list
	$pageContent->addReportAction($name = "team.keys.list.reload", $value = $applicationID);
	
	// Return output
	return $pageContent->getReport();
}

// Return output
return $pageContent->getReport();
//#section_end#
?>