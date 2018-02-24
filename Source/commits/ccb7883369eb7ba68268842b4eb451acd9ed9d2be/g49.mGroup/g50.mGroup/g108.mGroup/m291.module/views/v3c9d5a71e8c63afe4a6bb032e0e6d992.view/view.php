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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Resources");
importer::import("DEV", "Core");
importer::import("DEV", "Documentation");
importer::import("ESS", "Environment");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \API\Geoloc\datetimer;
use \API\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \API\Resources\filesystem\fileManager;
use \UI\Modules\MPage;
use \UI\Presentation\togglers\toggler;
use \UI\Presentation\dataGridList;
use \UI\Presentation\notification;
use \DEV\Core\sdk\sdkPackage;
use \DEV\Documentation\classDocumentor;

// Get manual attributes
$objectDomain = $_GET['domain'];
$objectLibrary = $_GET['lib'];
$objectPackage = $_GET['pkg'];
$objectNamespace = trim($_GET['ns']);
$objectNamespace = trim($objectNamespace, "/");
$objectName = $_GET['oname'];
$objectName = (empty($objectName) ? $objectNamespace : $objectName);

// Normalize
$objectNamespace = str_replace("_", "/", $objectNamespace);
$objectNamespace = str_replace("::", "/", $objectNamespace);
$objectPath = "/".$objectLibrary."/".$objectPackage."/".$objectNamespace;

// Initialize data
$docRoot = "/System/Resources/Documentation/";
$modelRoot = "/System/Resources/Model/";
$referenceFilePath = $docRoot.$objectDomain."/".$objectPath."/".$objectName.".php.xml";
$manualFilePath = $docRoot.$objectDomain."/".$objectPath."/".$objectName.".manual";
$modelFilePath = $modelRoot.$objectDomain."/".$objectPath."/".$objectName.".xml";
$classMan = new classDocumentor();

// Load manual
$error_man = FALSE;
try
{
	$classMan->loadFile($referenceFilePath);
}
catch (Exception $ex)
{
	$error_man = TRUE;
}

// Get inheritance hierarchy
if (!$error_man)
{
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
}

// Initialize page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build page
$container = $page->build($objectName." | Manual", "sdkManual", TRUE);

if ($error_man)
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
	$page->append($errorNtf);
	
	// Exception content
	return $page->getReport("#docViewer");
}

// Set static navigation
$targetcontainer = "bodyDetailsContainer";
$targetgroup = "manGroup";
$navgroup = "manavGroup";

$items = array();
$items['synopsis'] = "docSynopsis";
$items['manual'] = "docManual";
$items['model'] = "objModel";
$items['changelog'] = "changelog";
$items['similar'] = "packageObjects";
foreach ($items as $class => $ref)
{
	// Set nav item
	$item = HTML::select(".docNavigation .navitem.".$class)->item(0);
	$page->setStaticNav($item, $ref, $targetcontainer, $targetgroup, $navgroup, $display = "none");
	
	// Set target group
	$item = HTML::select("#".$ref)->item(0);
	$page->setNavigationGroup($item, $targetgroup);
}

// Get Class Info
$classInfo = $classMan->getInfo();

// Class Name
$classNameWrapper = HTML::select(".className")->item(0);
$span = DOM::create("span", $objectName);
DOM::append($classNameWrapper, $span);

$classTitleWrapper = HTML::select(".classTitle")->item(0);
$span = DOM::create("span", $classInfo['title']);
DOM::append($classTitleWrapper, $span);

$classDescWrapper = HTML::select(".classDescription")->item(0);
$span = DOM::create("span", $classInfo['description']);
DOM::append($classDescWrapper, $span);
DOM::append($paragraph, $classDesc);

// Class inheritance (if any)
$wrapper = HTML::select(".inheritance")->item(0);
$i = 0;
$rinheritance = array_reverse($inheritance);
foreach ($rinheritance as $object)
{
	$title = DOM::create("span");
	DOM::innerHtml($title, str_repeat("  ", $i)."&rarr; ".$object."\n");
	DOM::append($wrapper, $title);
	$i++;
}

// Class Implements
if (!empty($classInfo['implements'])) 
{ 
	$wrapper = HTML::select(".classImplements")->item(0);
	$title = DOM::create("span", "implements: ".$classInfo['implements']);
	DOM::append($wrapper, $title);
}

if (!empty($classInfo['deprecated']))
{
	// Create deprecated notification
	$ntf = new notification();
	$depNtf = $ntf->build(notification::WARNING, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add header
	$title = moduleLiteral::get($moduleID, "lbl_deprecatedClass");
	$header = DOM::create("h2", $title);
	$ntf->append($header);
	
	// Add message
	$message = DOM::create("p", $classInfo['deprecated']);
	$ntf->append($message);
	
	// Add notification before navigation
	$container = HTML::select(".section.classDesc")->item(0);
	DOM::append($container, $ntf->get());
}
		
		
// Get constant list
$thisClassConstants = $classMan->getConstants();
if (count($thisClassConstants) > 0)
	buildConstantsSection($moduleID, $thisClassConstants, HTML::select(".constantsContainer .sectionBody")->item(0));
	
// Add constants to list to avoid parents to display
$constantsList = array();
foreach ($thisClassConstants as $constant)
	$constantsList[] = $constant['name'];

// Get properties list
$thisClassPropertiesAll = $classMan->getProperties();

// Get properties not private
$thisClassProperties = array();
foreach ($thisClassPropertiesAll as $scope => $properties)
	if ($scope != "private")
		$thisClassProperties[$scope] = $properties;
		
// Add properties to list to avoid parents to display
$propertiesList = array();
foreach ($thisClassProperties as $properties)
	foreach ($properties as $propName => $propInfo)
		$propertiesList[] = $propName;

if (count($classProperties) > 0)
	buildPropertiesSection($moduleID, $properties, HTML::select(".propertiesContainer .sectionBody")->item(0));


// Get class methods
$thisClassMethodsAll = $classMan->getMethods();

// Get methods not private
$thisClassMethods = array();
foreach ($thisClassMethodsAll as $scope => $methods)
	if ($scope != "private")
		$thisClassMethods[$scope] = $methods;

if (count($thisClassMethods) > 0)
	buildMethodSection($moduleID, $thisClassMethods, HTML::select(".methodsContainer .sectionBody")->item(0));
	
// Add methods to list to avoid parents to display
$methodList = array();
foreach ($thisClassMethods as $methods)
	foreach ($methods as $methodInfo)
		$methodList[] = $methodInfo['name'];

// Add inheritance methods
if (count($inheritance) > 1)
{
	unset($inheritance[0]);
	foreach ($inheritance as $oName)
	{
		// Load documentation
		$objectPath = str_replace("\\", "/", $oName);
		$referenceFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
		$parentClassMan = new classDocumentor();
		$parentClassMan->loadFile($referenceFilePath);
		
		// Get constants and filter
		$parentClassConstantsAll = $parentClassMan->getConstants();
		$parentClassConstants = array();
		foreach ($parentClassConstantsAll as $constant)
			if (!in_array($constant['name'], $constantsList))
			{
				$constantsList[] = $constant['name'];
				$parentClassConstants[] = $constant;
			}

		if (count($parentClassConstants) > 0)
		{
			$constantsContainer = HTML::select(".constantsContainer .sectionBody")->item(0);
			$attr['class'] = $oName;
			$title = moduleLiteral::get($moduleID, "lbl_inheritedClassConstants", $attr);
			$hd = DOM::create("div", $title, "", "subHeader");
			DOM::append($constantsContainer, $hd);
			buildConstantsSection($moduleID, $parentClassConstants, $constantsContainer);
		}
		
		// Get properties and filter
		$parentClassPropertiesAll = $parentClassMan->getProperties();
		$parentClassProperties = array();
		foreach ($parentClassPropertiesAll as $scope => $properties)
			if ($scope != "private")
				foreach ($properties as $propName => $propInfo)
					if (!in_array($propName, $propertiesList))
					{
						$propertiesList[] = $propName;
						$parentClassProperties[$scope][$propName] = $propInfo;
					}
					
		if (count($parentClassProperties) > 0)
		{
			$propertiesContainer = HTML::select(".propertiesContainer .sectionBody")->item(0);
			$attr['class'] = $oName;
			$title = moduleLiteral::get($moduleID, "lbl_inheritedClassProperties", $attr);
			$hd = DOM::create("div", $title, "", "subHeader");
			DOM::append($propertiesContainer, $hd);
			buildPropertiesSection($moduleID, $parentClassProperties, $propertiesContainer);
		}
		
		// Get methods and filter
		$parentClassMethodsAll = $parentClassMan->getMethods();
		// Get methods not private
		$parentClassMethods = array();
		foreach ($parentClassMethodsAll as $scope => $methods)
			if ($scope != "private")
				foreach ($methods as $methodInfo)
					if (!in_array($methodInfo['name'], $methodList))
					{
						$methodList[] = $methodInfo['name'];
						$parentClassMethods[$scope][] = $methodInfo;
					}
					
		if (count($parentClassMethods) > 0)
		{
			$methodContainer = HTML::select(".methodsContainer .sectionBody")->item(0);
			$attr['class'] = $oName;
			$title = moduleLiteral::get($moduleID, "lbl_inheritedClassMethods", $attr);
			$hd = DOM::create("div", $title, "", "subHeader");
			DOM::append($methodContainer, $hd);
			buildMethodSection($moduleID, $parentClassMethods, $methodContainer);
		}
	}
}

// Add object manual doc
$manualContainer = HTML::select("#docManual")->item(0);
$manFile = systemRoot.$manualFilePath.".".locale::get().".html";
if (!file_exists($manFile))
	$manFile = systemRoot.$manualFilePath.".".locale::getDefault().".html";
$manual = fileManager::get($manFile);
if (!empty($manual))
	HTML::innerHTML($manualContainer, $manual);

// Add object model
$modelContainer = HTML::select("#objModel")->item(0);
$objModel = fileManager::get(systemRoot.$modelFilePath);
if (!empty($objModel))
	HTML::innerHTML($modelContainer, $objModel);
	
// Class Changelog
$literal = moduleLiteral::get($moduleID, "lbl_version");
$version = DOM::create("b", (empty($classInfo['version']) ? "0.0-0" : $classInfo['version']."-".$classInfo['build']), "", "bxtl");
$box = HTML::select("#changelog .version")->item(0);
DOM::append($box, $version);
DOM::append($box, $literal);

$literal = moduleLiteral::get($moduleID, "lbl_createdAt");
$live = DOM::create("b", datetimer::live($classInfo['datecreated']), "", "bxtl");
$box = HTML::select("#changelog .created")->item(0);
DOM::append($box, $live);
DOM::append($box, $literal);

$literal = moduleLiteral::get($moduleID, "lbl_updatedAt");
$live = DOM::create("b", datetimer::live($classInfo['daterevised']), "", "bxtl");
$box = HTML::select("#changelog .updated")->item(0);
DOM::append($box, $live);
DOM::append($box, $literal);


// Objects from the same package
switch ($objectDomain)
{
	case "SDK":
		$devPackage = new sdkPackage();
}

// Get container
$sObjectContainer = HTML::select("#packageObjects")->item(0);

// Get package objects
$similarObjects = $devPackage->getPackageObjects($objectLibrary, $objectPackage);
foreach ($similarObjects as $sObject)
{
	// Skip current object
	if ($sObject['namespace'] == $objectNamespace && $sObject['name'] == $objectName)
		continue;
	
	// Create object holder
	$sHolder = DOM::create("div", "", "", "sHolder");
	DOM::append($sObjectContainer, $sHolder);
	
	// Ico
	$sIco = DOM::create("div", "", "", "ico");
	DOM::append($sHolder, $sIco);
	
	// Title
	$objectNs = str_replace("::", "/", $sObject['namespace']);
	$href = url::resolve("developer", "/docs/sdk/".$objectDomain."/".$sObject['library']."/".$sObject['package']."/".(empty($objectNs) ? "" : $objectNs."/").$sObject['name']);
	$sTitle = $page->getWeblink($href, $sObject['name'], "_self");
	HTML::addClass($sTitle, "sTitle");
	DOM::append($sHolder, $sTitle);
	
	// Add action to load manual
	$attr = array();
	$attr['domain'] = $objectDomain;
	$attr['lib'] = $sObject['library'];
	$attr['pkg'] = $sObject['package'];
	$attr['ns'] = $sObject['namespace'];
	$attr['oname'] = $sObject['name'];
	$actionFactory->setModuleAction($sTitle, $moduleID, "manualViewer", ".manualContainer", $attr);
	
	// Full namespace
	$objectNs = str_replace("::", "\\", $sObject['namespace']);
	$fullName = $sObject['library']."\\".$sObject['package']."\\".(empty($objectNs) ? "" : $objectNs."\\").$sObject['name'];
	$sFullTitle = DOM::create("div", $fullName, "", "sfTitle");
	DOM::append($sHolder, $sFullTitle);
}


// Return the report
return $page->getReport("#docViewer");




function buildConstantsSection($moduleID, $constants, $constantsContainer)
{
	$gridList = new dataGridList();
	$gridList->build();
	
	$ratios = array();
	$ratios[] = 0.1;
	$ratios[] = 0.3;
	$ratios[] = 0.6;
	$gridList->setColumnRatios($ratios);
	
	$headers = array();
	$headers[] = "Type";
	$headers[] = "Name";
	$headers[] = "Description";
	$gridList->setHeaders($headers);
	
	// __Constants
	foreach ($constants as $constant)
	{
		$contents = array();	
		
		// Type
		$title = DOM::create("span", $constant['type'], "", "cType modifier");
		$contents[] = $title;
		
		// Name
		$title = DOM::create("span", $constant['name'], "", "cName token");
		$contents[] = $title;
		
		// Description
		$desc = DOM::create("span", $constant['description'], "", "cDesc");
		$contents[] = $desc;
		
		$gridList->insertRow($contents);	
	}
	
	// Append gridList
	$constantList = $gridList->get();
	DOM::append($constantsContainer, $constantList);
}

function buildPropertiesSection($moduleID, $properties, $propertiesContainer)
{
	$gridList = new dataGridList();
	$gridList->build();
	
	$ratios = array();
	$ratios[] = 0.1;
	$ratios[] = 0.1;
	$ratios[] = 0.3;
	$ratios[] = 0.5;
	$gridList->setColumnRatios($ratios);
	
	$headers = array();
	$headers[] = "Scope";
	$headers[] = "Type";
	$headers[] = "Name";
	$headers[] = "Description";
	$gridList->setHeaders($headers);
	
	// List properties
	foreach ($properties as $scope => $scopeProperties)
		foreach ($scopeProperties as $propName => $propInfo) 
		{
			$contents = array();
			
			// Scope
			$element = DOM::create("span", $scope, "", "pScope modifier");
			$contents[] = $element;
			
			// Type
			$element = DOM::create("span", $propInfo['type'], "", "pType modifier");
			$contents[] = $element;
			
			// Name
			$element =  DOM::create("span", $propInfo['name'], "", "pName token");
			$contents[] = $element;
			
			// Description
			$element = DOM::create("span", $propInfo['description'], "", "pDesc");
			$contents[] = $element;
			
			$gridList->insertRow($contents);
		}
	
	// Append gridList
	$constantList = $gridList->get();
	DOM::append($propertiesContainer, $constantList);
}


function buildMethodSection($moduleID, $methods, $methodContainer)
{
	$normal = array();
	$deprecated = array();
	foreach ($methods as $scope => $scopeMethods)
		foreach ($scopeMethods as $methodName => $methodInfo)
		{
			// Build toggler
			$toggler = new toggler();
			$methodHead = DOM::create("div", "", "", "mHead");
			$methodBody = DOM::create("div", "", "", "mBody");
			
			$methodDeclaration = getMethodDeclaration($scope, $methodInfo);
			DOM::append($methodHead, $methodDeclaration);	
			
			
			if (!empty($methodInfo['deprecated']))
			{
				// Create deprecated notification
				$ntf = new notification();
				$depNtf = $ntf->build("warning", $header = TRUE, $footer = FALSE, $timeout = FALSE);
				
				// Add header
				$header = DOM::create("h2", "This method is deprecated.");
				$ntf->append($header);
				
				// Add message
				$message = DOM::create("p");
				DOM::innerHTML($message, $methodInfo['deprecated']);
				$ntf->append($message);
				
				// Add notification to method body
				$depNtf = $ntf->get();
				DOM::append($methodBody, $depNtf);
			}
			
			// Method Description
			$title = moduleLiteral::get($moduleID, "lbl_methodDescription");
			$header = DOM::create("h4", $title, "", "mh");
			DOM::append($methodBody, $header);
			
			$text = DOM::create("p", $methodInfo['description'], "", "pre");
			DOM::append($methodBody , $text);
			
			if (array_key_exists('parameters', $methodInfo))
			{
				$title = moduleLiteral::get($moduleID, "lbl_methodParameters");
				$header = DOM::create("h4", $title, "", "mh");
				DOM::append($methodBody, $header);
				
				// Create parameters description
				$paramDescriptions = DOM::create("div", "", "", "parameters");
				DOM::append($methodBody, $paramDescriptions);
			
				foreach ($methodInfo['parameters'] as $pName => $paramInfo)
				{
					$paramElement = DOM::create("div", "", "", "parameter");
					DOM::append($paramDescriptions, $paramElement);
					
					$paramName = DOM::create("div", $paramInfo['type'], "", "paramName modifier");
					DOM::append($paramElement, $paramName);
					
					$paramName = DOM::create("div", $pName, "", "paramType modifier");
					DOM::append($paramElement, $paramName);
					
					$paramDesc = DOM::create("div", $paramInfo['description'], "", "paramDesc");
					DOM::append($paramElement, $paramDesc);
				}
			}
			
			// Return and Throws
			if (!empty($methodInfo['returndescription']))
			{ 
				$title = moduleLiteral::get($moduleID, "lbl_methodReturns");
				$header = DOM::create("h4", $title, "", "mh");
				DOM::append($methodBody, $header);
				
				$text = DOM::create("p", $methodInfo['returndescription'], "", "pre");
				DOM::append($methodBody , $text);
			}
			if (!empty($methodInfo['throws']))
			{ 			
				$title = moduleLiteral::get($moduleID, "lbl_methodThrows");
				$header = DOM::create("h4", $title, "", "mh");
				DOM::append($methodBody, $header);
				
				$text = DOM::create("p", $methodInfo['throws'], "", "pre");
				DOM::append($methodBody, $text);
			}
							
			$toggler->build($id = "", $methodHead, $methodBody, FALSE);
			
			// Append gridList
			$methodTog = $toggler->get();
			if (!empty($methodInfo['deprecated']))
				$deprecated[] = $methodTog;
			else
				$normal[] = $methodTog;
		}
		
	foreach ($normal as $methodTog)
		DOM::append($methodContainer, $methodTog);
	foreach ($deprecated as $methodTog)
		DOM::append($methodContainer, $methodTog);
}



function getMethodDeclaration($scope, $method)
{
	$methodDeclaration = DOM::create("div", "", "", "methodDeclaration decline");
	
	$scopeElement = DOM::create("span", $scope, "", "mScope modifier");
	DOM::append($methodDeclaration, $scopeElement);
	
	if ($method['static'])
	{
		$static = DOM::create("span", "static", "", "mStatic modifier");
		DOM::append($methodDeclaration, $static);
	}
	
	$typeElement = DOM::create("span", $method['returntype'], "", "mType modifier");
	DOM::append($methodDeclaration, $typeElement);
	
	$nameElement = DOM::create("span", $method['name'], "", "mName token");
	DOM::append($methodDeclaration, $nameElement);
	
	$parenthesisStart = DOM::create("span", " (", "", "modifier");
	DOM::append($methodDeclaration, $parenthesisStart);
	
	$counter = 0;
	if (array_key_exists('parameters', $method))
		foreach ($method['parameters'] as $pName => $paramInfo)
		{
			$paramElement = DOM::create("span", "", "", "parameter");
			DOM::append($methodDeclaration, $paramElement);
			
			$paramType = DOM::create("span", ($counter != 0 ? ", " : "").$paramInfo['type'], "", "paramType modifier");
			DOM::append($paramElement, $paramType);
			
			$paramName = DOM::create("span", $pName, "", "paramName token");
			DOM::append($paramElement, $paramName);
			$counter++;
		}
	
	$parenthesisEnd = DOM::create("span", ")", "", "modifier");
	DOM::append($methodDeclaration, $parenthesisEnd);
	
	if (!empty($method['deprecated']))
	{
		$deprecationIdentifier = DOM::create("span", "Deprecated", "", "mDepr modifier");
		DOM::append($methodDeclaration, $deprecationIdentifier);
	}

	return $methodDeclaration;
}
//#section_end#
?>