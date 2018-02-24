<?php
//#section#[header]
// Module Declaration
$moduleID = 115;

// Inner Module Codes
$innerModules = array();
$innerModules['templateViewer'] = 131;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("ESS", "Prototype");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \UI\Html\HTMLContent;
use \UI\Html\pageComponents\htmlComponents\weblink; 


use \ESS\Protocol\client\environment\Url;

$holderId = $_GET['holder'];
$viewType = $_GET['viewType'];
$templateID = $_GET['id'];

// Create Module Page
$HTMLContent = new HTMLContent();
$globalContainer = $HTMLContent->build()->get();

// Template Plain Info
$infoContainer = DOM::create('div');
DOM::append($globalContainer, $infoContainer);

// Template Info
$templateInfoWrapper = DOM::create('div');
	$atrr = array();
	$attr['id'] = $templateID;
	$templateInfo = $HTMLContent->getModuleContainer($innerModules['templateViewer'], "templateInfo", $attr, TRUE);
	DOM::append($templateInfoWrapper, $templateInfo);
DOM::append($infoContainer, $templateInfoWrapper);


// Controls 
$controlWrapper = DOM::create('div');
	$controls = DOM::create("div");
	DOM::append($controlWrapper, $controls);
	switch ($viewType)
	{
		case 'all':
			
			break;	
		case 'project':		
			$navigationUrl = Url::resolve("developer", "/ebuilder/templates/template.php", FALSE);
			$content = literal::get("global.dictionary", "edit");
			$item = weblink::get($navigationUrl."?id=".$templateID, "_target", $content);
			DOM::append($controls, $item);
			break;
		case 'my':
			
			break;
		default:		
			break;		
	}	
DOM::append($infoContainer, $controlWrapper);


// Graphical
$ghraphicalRepresentation = DOM::create('div');
	$atrr = array();
	$attr['id'] = $templateID;
	$pageStructureItem = $HTMLContent->getModuleContainer($innerModules['templateViewer'], '', $atrr, $startup = TRUE);
	DOM::append($ghraphicalRepresentation, $pageStructureItem);
DOM::append($globalContainer, $ghraphicalRepresentation);



// Return output
return $HTMLContent->getReport("#".$holderId);
//#section_end#
?>