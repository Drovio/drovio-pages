<?php
//#section#[header]
// Module Declaration
$moduleID = 291;

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
importer::import("API", "Resources");
importer::import("DEV", "Documentation");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\core\manifests;
use \API\Resources\filesystem\directory;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\toggler;
use \UI\Presentation\dataGridList;
use \UI\Presentation\notification;
use \DEV\Documentation\classDocumentor;

// Get manual attributes
$objectDomain = $_GET['domain'];
$objectLibrary = $_GET['lib'];
$objectPackage = $_GET['pkg'];
$objectNamespace = trim($_GET['ns']);
$objectNamespace = trim($objectNamespace, "/");
$objectName = $_GET['oname'];
$objectName = (empty($objectName) ? $objectNamespace : $objectName);

// Normalize variables and get object full path
$objectNamespace = str_replace("_", "/", $objectNamespace);
$objectNamespace = str_replace("::", "/", $objectNamespace);
$objectPath = "/".$objectLibrary."/".$objectPackage."/".$objectNamespace;

// Get protected libraries
$coreManifests = manifests::getManifests();
$corePermissions = array();
foreach ($coreManifests as $mfID => $mfInfo)
	if (!$mfInfo['info']['private'] && in_array($objectPackage, $mfInfo['packages'][$objectLibrary]))
		$corePermissions[$mfID] = $mfInfo['info'];
				
				
// Initialize page content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build content
$pageContent->build("", "classReferenceContainer", TRUE);


// Initialize class documentor
$classMan = new classDocumentor();
$docRoot = "/System/Resources/Documentation/";
$referenceFilePath = $docRoot.$objectDomain."/".$objectPath."/".$objectName.".php.xml";

// Load class documentation
try
{
	$classMan->loadFile($referenceFilePath);
}
catch (Exception $ex)
{
	// Create document not found notification
	$ntf = new notification();
	$depNtf = $ntf->build(notification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add header
	$header = DOM::create("h2", "Documentation Error.");
	$ntf->append($header);
	
	// Add message
	$message = DOM::create("p", "The class documentation file not found.");
	$ntf->append($message);
	
	// Add notification to description
	$errorNtf = $ntf->get();
	$pageContent->append($errorNtf);
	
	// Exception content
	return $pageContent->getReport("#docViewer");
}

// Get inheritance hierarchy
$classInfo = $classMan->getInfo();
$extends = $classInfo['extends'];
$inheritance = array();
$p = directory::normalize($objectPath."/".$objectName);
$inheritance[] = str_replace("/", "\\", $p);
while (!empty($extends))
{
	// Add parent into inheritance hierarchy
	$inheritance[] = directory::normalize($extends);
	
	// Load parent's documentation
	$objectPath = str_replace("\\", "/", $extends);
	$referenceFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
	$parentClassMan = new classDocumentor();
	
	try
	{
		$parentClassMan->loadFile($referenceFilePath);
		$parentClassInfo = $parentClassMan->getInfo();
		$extends = $parentClassInfo['extends'];
	}
	catch (Exception $ex)
	{
		$extends = NULL;
	}
}

if (count($inheritance) > 1)
	unset($inheritance[0]);
		
		
// Build constants list
$gridList = new dataGridList();
$gridList->build();

$ratios = array();
$ratios[] = 0.1;
$ratios[] = 0.2;
$ratios[] = 0.4;
$ratios[] = 0.3;
$gridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "Type";
$headers[] = "Name";
$headers[] = "Description";
$headers[] = "Inherited";
$gridList->setHeaders($headers);

// Get current constants
$allClassConstants = array();
$thisClassConstants = $classMan->getConstants();
foreach ($thisClassConstants as $constant)
	$allClassConstants[$constant['name']] = $constant;
if (count($inheritance) > 0)
{
	$parentClassConstants = array();
	foreach ($inheritance as $oName)
	{
		// Load documentation
		$objectPath = str_replace("\\", "/", $oName);
		$referenceFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
		$parentClassMan = new classDocumentor();
		try
		{
			$parentClassMan->loadFile($referenceFilePath);
		}
		catch (Exception $ex)
		{
			continue;
		}
		
		// Get constants and filter
		$parentClassConstantsAll = $parentClassMan->getConstants();
		foreach ($parentClassConstantsAll as $constant)
			if (!isset($allClassConstants[$constant['name']]))
			{
				$constant['inherited'] = $oName;
				$allClassConstants[$constant['name']] = $constant;
			}
	}
}

// Sort and display constants
ksort($allClassConstants);
foreach ($allClassConstants as $constant)
{
	$contents = array();	
	$contents[] = DOM::create("span", $constant['type'], "", "cType modifier");
	$contents[] = DOM::create("span", $constant['name'], "", "cName token");
	$contents[] = DOM::create("span", $constant['description'], "", "cDesc");
	$contents[] = DOM::create("span", $constant['inherited'], "", "cDesc");
	$gridList->insertRow($contents);
}

// Append gridList
if (!empty($allClassConstants))
{
	$constantsContainer = HTML::select(".constantsContainer .sectionBody")->item(0);
	DOM::append($constantsContainer, $gridList->get());
}



// Build properties list
$gridList = new dataGridList();
$gridList->build();

$ratios = array();
$ratios[] = 0.1;
$ratios[] = 0.1;
$ratios[] = 0.2;
$ratios[] = 0.3;
$ratios[] = 0.3;
$gridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "Scope";
$headers[] = "Type";
$headers[] = "Name";
$headers[] = "Description";
$headers[] = "Inherited";
$gridList->setHeaders($headers);


// Get properties list
$allClassProperties = array();
$thisClassPropertiesAll = $classMan->getProperties();

// Get properties not private
foreach ($thisClassPropertiesAll as $scope => $properties)
	foreach ($properties as $propName => $propInfo)
		$allClassProperties[$scope][$propName] = $propInfo;

if (count($inheritance) > 0)
{
	foreach ($inheritance as $oName)
	{
		// Load documentation
		$objectPath = str_replace("\\", "/", $oName);
		$referenceFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
		$parentClassMan = new classDocumentor();
		try
		{
			$parentClassMan->loadFile($referenceFilePath);
		}
		catch (Exception $ex)
		{
			continue;
		}
		
		// Get properties and filter
		$parentClassPropertiesAll = $parentClassMan->getProperties();
		foreach ($parentClassPropertiesAll as $scope => $properties)
			foreach ($properties as $propName => $propInfo)
				if (!isset($allClassProperties[$scope][$propName]))
				{
					$propInfo['inherited'] = $oName;
					$allClassProperties[$scope][$propName] = $propInfo;
				}
	}
}
// List properties
unset($allClassProperties["private"]);
foreach ($allClassProperties as $scope => $scopeProperties)
{
	// Sort properties and list
	ksort($scopeProperties);
	foreach ($scopeProperties as $propName => $propInfo) 
	{
		$contents = array();
		$contents[] = DOM::create("span", $scope, "", "pScope modifier");
		$contents[] = DOM::create("span", $propInfo['type'], "", "pType modifier");
		$contents[] = DOM::create("span", $propInfo['name'], "", "pName token");
		$contents[] = DOM::create("span", $propInfo['description'], "", "pDesc pre");
		$contents[] = DOM::create("span", $propInfo['inherited'], "", "pDesc");
		$gridList->insertRow($contents);
	}
}

// Append gridList
if (!empty($allClassProperties))
{
	$propertiesContainer = HTML::select(".propertiesContainer .sectionBody")->item(0);
	DOM::append($propertiesContainer, $gridList->get());
}







// Build methods list
$gridList = new dataGridList();
$gridList->build();

$ratios = array();
$ratios[] = 0.1;
$ratios[] = 0.1;
$ratios[] = 0.2;
$ratios[] = 0.3;
$ratios[] = 0.3;
$gridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "Scope";
$headers[] = "Return";
$headers[] = "Flags";
$headers[] = "Name";
$headers[] = "Inherited";
$gridList->setHeaders($headers);

// Get class methods
$allClassMethods = array();
$thisClassMethodsAll = $classMan->getMethods();
foreach ($thisClassMethodsAll as $scope => $methods)
	foreach ($methods as $methodInfo)
		$allClassMethods[$scope][$methodInfo['name']] = $methodInfo;

// Add inheritance methods
if (count($inheritance) > 0)
{
	foreach ($inheritance as $oName)
	{
		// Load documentation
		$objectPath = str_replace("\\", "/", $oName);
		$referenceFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
		$parentClassMan = new classDocumentor();
		try
		{
			$parentClassMan->loadFile($referenceFilePath);
		}
		catch (Exception $ex)
		{
			continue;
		}
		
		// Get methods and filter
		$parentClassMethodsAll = $parentClassMan->getMethods();
		foreach ($parentClassMethodsAll as $scope => $methods)
			foreach ($methods as $methodInfo)
				if (!isset($allClassMethods[$scope][$methodInfo['name']]))
				{
					$methodInfo['inherited'] = $oName;
					$allClassMethods[$scope][$methodInfo['name']] = $methodInfo;
				}
	}
}

// List methods
unset($allClassMethods["private"]);
// Append gridList
if (!empty($allClassMethods))
{
	$methodContainer = HTML::select(".methodsContainer .sectionBody")->item(0);
	DOM::append($methodContainer, $gridList->get());
	
	$separator = DOM::create("div", "", "", "section__hr_center");
	DOM::append($methodContainer, $separator);
	
	$subHeader = DOM::create("div", "Class Methods in Details", "", "subHeader");
	DOM::append($methodContainer, $subHeader);
}
foreach ($allClassMethods as $scope => $scopeMethods)
{
	// Sort properties and list
	ksort($scopeMethods);
	foreach ($scopeMethods as $methodInfo) 
	{
		$contents = array();
		$contents[] = DOM::create("span", $scope, "", "pScope modifier");
		$contents[] = DOM::create("span", $methodInfo['returntype'], "", "pType modifier");
		
		// Set flags
		$flags = array();
		if ($methodInfo['static'])
			$flags[] = "STATIC";
		if ($methodInfo['deprecated'])
			$flags[] = "DEPRECATED";
		$flagContainer = DOM::create("div", implode(", ", $flags), "", "pType modifier");
		$contents[] = $flagContainer;
		
		// Create name weblink
		$contents[] = $pageContent->getWeblink($href = "#".$methodInfo['name'], $content = $methodInfo['name'], $target = "_self", $moduleID = "", $viewName = "", $attr = array(), $class = "pName token");
		$contents[] = DOM::create("span", $methodInfo['inherited'], "", "pDesc");
		$gridList->insertRow($contents);
		
		
		// Build method body and append to list
		$methodBox = buildMethodBox($scope, $methodInfo, $methodInfo['inherited']);
		DOM::append($methodContainer, $methodBox);
	}
}


// Return the report
return $pageContent->getReport("#docViewer");


function buildMethodBox($scope, $methodInfo, $inherited = "")
{
	// Build method box
	$methodBox = DOM::create("div", "", $methodInfo['name'], "methodBox");
	
	// Method signature
	$mbody = $methodInfo['name']."(";
	$parray = array();
	foreach ($methodInfo['parameters'] as $pName => $paramInfo)
		$parray[] = $pName;
	$mbody .= implode(", ", $parray).")";
	$signature = DOM::create("div", $mbody, "", "msignature");
	DOM::append($methodBox, $signature);
	
	// Modifiers
	$modifier = DOM::create("span", $methodInfo['returntype'], "", "modifier sign");
	DOM::prepend($signature, $modifier);
	if ($methodInfo['static'])
	{
		$modifier = DOM::create("span", "static", "", "modifier sign");
		DOM::prepend($signature, $modifier);
	}
	$modifier = DOM::create("span", $scope, "", "modifier sign");
	DOM::prepend($signature, $modifier);
	
	// Check if it's deprecated
	if (!empty($methodInfo['deprecated']))
	{
		// Create deprecated notification
		$ntf = new notification();
		$depNtf = $ntf->build($type = notification::WARNING, $header = TRUE, $footer = FALSE, $timeout = FALSE)->get();
		DOM::append($methodBox, $depNtf);
		
		// Add header
		$header = DOM::create("h2", "This method is deprecated.");
		$ntf->append($header);

		// Add message
		$message = DOM::create("p", $methodInfo['deprecated']);
		$ntf->append($message);
	}
	
	// If inherited
	if (!empty($inherited))
	{
		// Method description
		$subHeader = DOM::create("div", "Inherited", "", "subHeader");
		DOM::append($methodBox, $subHeader);

		$text = DOM::create("p", $inherited, "", "pre");
		DOM::append($methodBox , $text);
	}
	
	// Method description
	$subHeader = DOM::create("div", "Description", "", "subHeader");
	DOM::append($methodBox, $subHeader);
	
	$text = DOM::create("p", $methodInfo['description'], "", "pre");
	DOM::append($methodBox , $text);
	
	// Create grid list for parameters
	if (!empty($methodInfo['parameters']))
	{
		$subHeader = DOM::create("div", "Parameters", "", "subHeader");
		DOM::append($methodBox, $subHeader);
		
		$gridList = new dataGridList();
		$parametersList = $gridList->build()->get();
		DOM::append($methodBox, $parametersList);

		$ratios = array();
		$ratios[] = 0.2;
		$ratios[] = 0.2;
		$ratios[] = 0.6;
		$gridList->setColumnRatios($ratios);

		$headers = array();
		$headers[] = "Type";
		$headers[] = "Name";
		$headers[] = "Description";
		$gridList->setHeaders($headers);
		
		foreach ($methodInfo['parameters'] as $pName => $paramInfo)
		{
			$contents = array();
			$contents[] = DOM::create("span", $paramInfo['type'], "", "pType modifier");
			$contents[] = DOM::create("span", $pName, "", "pName token");
			$contents[] = DOM::create("span", $paramInfo['description'], "", "pDesc pre");
			$gridList->insertRow($contents);
		}
	}
	
	// Method return description
	if (!empty($methodInfo['returndescription']))
	{
		$subHeader = DOM::create("div", "Return", "", "subHeader");
		DOM::append($methodBox, $subHeader);

		$text = DOM::create("p", $methodInfo['returndescription'], "", "pre");
		DOM::append($methodBox , $text);
	}
	
	// Method throws
	if (!empty($methodInfo['throws']))
	{ 			
		$subHeader = DOM::create("div", "Throws", "", "subHeader");
		DOM::append($methodBox, $subHeader);

		$text = DOM::create("p", $methodInfo['throws'], "", "pre");
		DOM::append($methodBox, $text);
	}
	
	return $methodBox;
}
//#section_end#
?>