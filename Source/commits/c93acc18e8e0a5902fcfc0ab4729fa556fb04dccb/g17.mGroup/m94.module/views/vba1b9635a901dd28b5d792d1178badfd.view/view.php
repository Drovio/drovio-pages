<?php
//#section#[header]
// Module Declaration
$moduleID = 94;

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
importer::import("API", "Literals");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;

// Build Module Page
$page = new MPage($moduleID);
$form = new simpleForm();
$pageTitle = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($pageTitle, "rbFrontend", TRUE);

$wehForm = $form->build($moduleID,"WebsiteCreator",FALSE,FALSE)->get();



$titleCont = HTML::select(".titleCont")->item(0);

$bizCont = HTML::select(".bizCont")->item(0);

$registerContainer = HTML::select(".registerContainer")->item(0);
HTML::append( $registerContainer, $wehForm);

//HTML::append($registerContainer,$titleCont);
//HTML::append($registerContainer,$urlCont);
 


  




//Populate
$title = "Website Title";
$pageTitle = $form->getInput("text", "wName", "","pageFields",TRUE,TRUE);
DOM::attr($pageTitle,"placeholder",$title);
HTML::append ($titleCont,$pageTitle);
//$form->insertRow("",$pageTitle,TRUE);

/*$url = "Website URL";
$pageUrl = $form->getInput("text", "wUrl", "","pageFields",FALSE, FALSE);
DOM::attr($pageUrl,"placeholder",$url);
HTML::append( $urlCont,$pageUrl );*/

$sbBn = $form->getSubmitButton("Go!!","sbBn");
//HTML::append($registerContainer, $sbBn);
//$form->append($sbBn);
HTML::append ($titleCont ,$sbBn);


$form->append($titleCont);

$form->append($bizCont);





return $page->getReport();








/*use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \UI\Html\HTMLModulePage;

// Build Module Page
$page = new HTMLModulePage();
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($pageTitle, "frontendPage", TRUE);

$text = moduleLiteral::get($moduleID, "lbl_pageTitle");
$textContainer = HTML::select("h1.logTitle")->item(0);
DOM::append($textContainer, $text);


$text = moduleLiteral::get($moduleID, "lbl_subtitle");
$textContainer = HTML::select("h3.subTitle")->item(0);
DOM::append($textContainer, $text);

return $page->getReport();*/
//#section_end#
?>