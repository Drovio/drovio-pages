<?php
//#section#[header]
// Module Declaration
$moduleID = 328;

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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Profile\managedAccount;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$pageContent = new MContent($moduleID);

// Build the content
$pageContent->build("", "managedAccountsContainer", TRUE);

// Get all managed accounts
$managedAccounts = managedAccount::getInstance()->getManagedAccounts();
$settingsContainer = HTML::select(".managedAccounts .settings")->item(0);
foreach ($managedAccounts as $accountInfo)
{
	// Managed account row
	$editTitle = moduleLiteral::get($moduleID, "lbl_edit");
	$srow = getRow($pageContent, $moduleID, $accountInfo['title'], $editTitle, "editAccount", $accountInfo['id']);
	DOM::append($settingsContainer, $srow);
}

// New account row
$editTitle = moduleLiteral::get($moduleID, "lbl_create");
$accountTitle = moduleLiteral::get($moduleID, "lbl_newAccount");
$srow = getRow($pageContent, $moduleID, $accountTitle, $editTitle, "newAccount");
DOM::append($settingsContainer, $srow);

// Return output
return $pageContent->getReport();


function getRow($pageContent, $moduleID, $accountTitle, $editTitle, $viewName, $accountID = NULL)
{
	// Create srow
	$srow = HTML::create("div", "", "", "srow");
	
	// Create row header
	$shd = HTML::create("div", "", "", "shd");
	DOM::append($srow, $shd);
	
	// Account title
	$aTitle = DOM::create("div", $accountTitle, "", "title");
	DOM::append($shd, $aTitle);
	
	// Edit button
	$edit = HTML::create("div", $editTitle, "", "edit");
	HTML::append($shd, $edit);
	
	// Create row body
	$sbody = HTML::create("div", "", "", "sbody");
	DOM::append($srow, $sbody);
	
	// Build module container
	$attr = array();
	$attr['aid'] = $accountID;
	$body = $pageContent->getModuleContainer($moduleID, $viewName, $attr, $startup = FALSE, $containerID = "");
	HTML::addClass($body, "sContainer");
	DOM::append($sbody, $body);
	
	return $srow;
}
//#section_end#
?>