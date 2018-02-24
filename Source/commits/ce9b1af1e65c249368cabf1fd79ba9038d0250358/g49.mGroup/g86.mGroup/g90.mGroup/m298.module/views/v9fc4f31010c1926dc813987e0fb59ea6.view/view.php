<?php
//#section#[header]
// Module Declaration
$moduleID = 298;

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
importer::import("DEV", "WebEngine");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;
use \DEV\WebEngine\distroManager;
use \DEV\WebEngine\sdk\webLibrary;

// Get data
$distroName = $_REQUEST['dname'];

// Initialize distro manager
$dman = new distroManager();

// Build module content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "distroPackagesContainer", TRUE);

$holder = "";
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Set report holder
	$holder = ".distroPackages";
	
	// Check whether to delete the distro
	if (isset($_POST['delDistro']))
	{
		$status = $dman->remove($distroName);
		if ($status)
		{
			$succFormNtf = new formNotification();
			$succFormNtf->build($type = "success", $header = TRUE);
			
			// Notification Message
			$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
			$succFormNtf->append($errorMessage);
			$pageContent->addReportContent($succFormNtf->get(), $holder, "replace");
			return $pageContent->getReport();
		}
	}
	
	// Update distro
	$distroPackages = array();
	foreach ($_POST['dpcks'] as $name => $value)
	{
		$parts = explode(",", $name);
		$distroPackages[$parts[0]][] = $parts[1];
	}
	
	$dman->update($_POST['dname'], $distroPackages, $_POST['dnewName']);
	if ($_POST['dnewName'] != $distroName)
		$distroName = $_POST['dnewName'];
}

$form = new simpleForm();
$literalsForm = $form->build($action = "", $defaultControls = FALSE)->engageModule($moduleID, "distroPackages")->get();
$pageContent->append($literalsForm);

// Distro name
$input = $form->getInput($type = "hidden", $name = "dname", $value = $distroName, $class = "", $autofocus = FALSE);
$form->append($input);


// Literals Container
$distroContainer = DOM::create("div", "", "", "distroInnnerContainer");
$form->append($distroContainer);

$distroGridListContainer = DOM::create("div", "", "", "distroGridListContainer");
DOM::append($distroContainer, $distroGridListContainer);

$distroGridList = DOM::create("div", "", "", "distroGridList");
DOM::append($distroGridListContainer, $distroGridList);

// Get distro packages
$distros = $dman->getDistros();
$distroPackages = $distros[$distroName];

// Web Packages list
$gridList = new dataGridList();
$distroGList = $gridList->build("dglist", $editable = TRUE)->get();
DOM::append($distroGridList, $distroGList);

// Set headers
$headers = array();
$headers[] = "Library";
$headers[] = "Package";
$gridList->setHeaders($headers);

// Get web packages
$wlib = new webLibrary();
$libraries = $wlib->getList();
foreach ($libraries as $libName)
{
	// Get packages
	$packages = $wlib->getPackageList($libName);
	foreach ($packages as $packageName)
	{
		$gridRow = array();
		
		$gridRow[] = $libName;
		$gridRow[] = $packageName;
		
		$checked = in_array($packageName, $distroPackages[$libName]);
		$gridList->insertRow($gridRow, "dpcks[".$libName.",".$packageName."]", $checked);
	}
}



// Add controls row
$controls = DOM::create("div", "", "", "dControls");
DOM::append($distroContainer, $controls);

// Distro name
$input = $form->getInput($type = "text", $name = "dnewName", $value = $distroName, $class = "", $autofocus = FALSE);
DOM::append($controls, $input);

// Add delete scope checkbox
$input = $form->getInput($type = "checkbox", $name = "delDistro", $value = "", $class = "", $autofocus = FALSE);
DOM::append($controls, $input);
$inputID = DOM::attr($input, "id");

$attr['scope'] = $scope;
$title = moduleLiteral::get($moduleID, "lbl_delDistro", $attr);
$label = $form->getLabel($title, $for = $inputID, $class = "");
DOM::append($controls, $label);

// Submit Button
$title = moduleLiteral::get($moduleID, "lbl_updatePackages");
$submit = $form->getSubmitButton($title);
DOM::append($controls, $submit);

return $pageContent->getReport($holder);
//#section_end#
?>