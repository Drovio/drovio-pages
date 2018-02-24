<?php
//#section#[header]
// Module Declaration
$moduleID = 344;

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
importer::import("DEV", "Core");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \DEV\Core\sdk\sdkLibrary;
use \DEV\Core\manifests;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get manifest info
$mfID = engine::getVar("mfid");
$mfManager = new manifests();

if (engine::isPost())
{
	// Update manifest packages
	$packageList = array();
	foreach ($_POST['pkg'] as $pkgValue => $nothing)
	{
		$parts = explode(",", $pkgValue);
		$packageList[$parts[0]][] = $parts[1];
	}
	$mfManager->update($mfID, $packageList, $newName = $_POST['mfname']);
	
	// Set manifest enabled
	$enabled = (isset($_POST['enabled']) ? TRUE : FALSE);
	$mfManager->setEnabled($mfID, $enabled);
	
	// Return notification
	$successNotification = new formNotification();
	$successNotification->build(formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Build the module content
$pageContent->build("", "mfEditorContainer", TRUE);
$infoContainer = HTML::select(".mfEditor .info")->item(0);

// Check if there is manifest title and description
$mfInfo = $mfManager->info($mfID);
$mfTitle = literal::get("sdk.manifest", "mf_".$mfInfo['info']['name']."_title");
if (!empty($mfTitle))
{
	$hTitle = DOM::create("h2", $mfTitle, "", "hd title");
	DOM::append($infoContainer, $hTitle);
}

$mfDesc = literal::get("sdk.manifest", "mf_".$mfInfo['info']['name']."_desc");
if (!empty($mfDesc))
{
	$hDesc = DOM::create("h4", $mfDesc, "", "hd desc");
	DOM::append($infoContainer, $hDesc);
}


// Create form
$form = new simpleForm();
$mfEditor = $form->build()->engageModule($moduleID, "manifestEditor")->get();
$formContainer = HTML::select(".mfEditorContainer .formContainer")->item(0);
DOM::append($formContainer, $mfEditor);

// Manifest id
$input = $form->getInput($type = "hidden", $name = "mfid", $value = $mfID, $class = "", $autofocus = "", $required = TRUE);
$form->append($input);

// Manifest name
$title = moduleLiteral::get($moduleID, "lbl_mfName");
$input = $form->getInput($type = "text", $name = "mfname", $value = $mfInfo['info']['name'], $class = "", $autofocus = "", $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Manifest enabled status
$title = moduleLiteral::get($moduleID, "lbl_mfEnabled");
$input = $form->getInput($type = "checkbox", $name = "enabled", $value = ($mfInfo['info']['enabled'] == 1), $class = "", $autofocus = "", $required = FALSE);
$form->insertRow($title, $input, $required = FALSE, $notes = "");


// Add sdk packages
$dtGridList = new dataGridList();
$glist = $dtGridList->build("sdkPackages", TRUE)->get();
$form->append($glist);

$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$dtGridList->setHeaders($headers);

// Get All Packages
$sdkLib = new sdkLibrary();
$libraries = $sdkLib->getList();
asort($libraries);
foreach ($libraries as $library)
{
	$packages = $sdkLib->getPackageList($library);
	asort($packages);
	foreach ($packages as $package)
	{
		// Grid List Contents
		$gridRow = array();
		$gridRow[] = $library;
		$gridRow[] = $package;
		
		$selected = in_array($package, $mfInfo['packages'][$library]);
		$dtGridList->insertRow($gridRow, "pkg[".$library.','.$package.']', $selected);
	}
}

// Return output
return $pageContent->getReport();
//#section_end#
?>