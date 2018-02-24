<?php
//#section#[header]
// Module Declaration
$moduleID = 223;

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
importer::import("UI", "Modules");
importer::import("INU", "Forms");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;


if($_SERVER["REQUEST_METHOD"] == "POST")
{
	//print_r($_POST);
	return;

}


$mcontent = new MContent($moduleID);
$mcontent->build("","webInfoEditor");

$formsHeader = HTML::create("h1","Edit your site's information here :","","editInfoHeader");
$mcontent->append($formsHeader );

$form = new simpleForm();
$form->build($moduleID,"webSiteEditInfoPopup",TRUE);
$editorForm = $form->get();
$mcontent->append($editorForm);

$title = "website's name";
$input = $form->getInput("text","websiteName","","",TRUE,TRUE);
$form->insertRow($title,$input,TRUE);

$title = "website's description";
$input = $form->getTextArea("websiteDescription","","textAreaWebsiteDescription",FALSE);
$form->insertRow($title,$input,TRUE);


// Return output
return $mcontent->getReport();
//#section_end#
?>