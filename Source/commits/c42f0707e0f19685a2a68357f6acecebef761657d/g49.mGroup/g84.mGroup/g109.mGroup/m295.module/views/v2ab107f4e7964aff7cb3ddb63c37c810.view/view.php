<?php
//#section#[header]
// Module Declaration
$moduleID = 295;

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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\sdk\privileges;
use \DEV\Core\coreProject;

// Initialize Core SDK Privileges
$corePrivileges = new privileges();

if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Validate form post
	if (!simpleForm::validate())
	{
		// Add form post error header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalidate"));
		
		return $errFormNtf->getReport();
	}
	
	// Get packages and modify to accept by the privileges class
	$packages = $_POST['pkg'];
	$openPackages = array();
	foreach ($packages as $pkgValue => $nothing)
	{
		$parts = explode(",", $pkgValue);
		$openPackages[$parts[0]][] = $parts[1];
	}
	
	// Update packages
	$corePrivileges->setPackages($openPackages);
	
	// Return notification
	$successNotification = new formNotification();
	$successNotification->build(formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}


$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();


$page->build("", "corePrivilegesPage", TRUE);
$formContainer = HTML::select(".corePrivileges .formContainer")->item(0);

// SDK Privileges
$form = new simpleForm();
$corePrivilegesForm = $form->build()->engageModule($moduleID)->get();
DOM::append($formContainer, $corePrivilegesForm);

// Project ID
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Add sdk packages
$dtGridList = new dataGridList();
$glist = $dtGridList->build("sdkPackages", TRUE)->get();
$form->append($glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$headers[] = "In Production";

$dtGridList->setHeaders($headers);

// Get All Packages
$sdkLib = new sdkLibrary();
$libraries = $sdkLib->getList();
$packages = array();
$openPackages = $corePrivileges->getPackages();
$openPackages_production = importer::getOpenPackageList();
foreach ($libraries as $library)
{
	$packages = $sdkLib->getPackageList($library);
	foreach ($packages as $package)
	{
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $library;
		$gridRow[] = $package;
		
		$inProduction = in_array($package, $openPackages_production[$library]);
		$gridRow[] = ($inProduction ? "TRUE" : "");
		
		$open = in_array($package, $openPackages[$library]);
		$dtGridList->insertRow($gridRow, "pkg[".$library.','.$package.']', $open);
	}
}


// Return the report
return $page->getReport();
//#section_end#
?>