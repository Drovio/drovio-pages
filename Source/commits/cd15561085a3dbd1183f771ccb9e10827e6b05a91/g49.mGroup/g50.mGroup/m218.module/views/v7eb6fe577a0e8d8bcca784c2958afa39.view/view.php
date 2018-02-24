<?php
//#section#[header]
// Module Declaration
$moduleID = 218;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("DEV", "Documentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\directory;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\toggler;
use \UI\Presentation\dataGridList;
use \UI\Presentation\notification;
use \DEV\Documentation\classDocumentor;


// Get object information
$objectDomain = $_GET['domain'];
$library = $_GET['lib'];
$package = $_GET['pkg'];
$namespace = $_GET['ns'];
$objectName = $_GET['oid'];

// Normalize
$namespace = str_replace("_", "/", $namespace);
$namespace = str_replace("::", "/", $namespace);
$objectPath = "/".$library."/".$package."/".$namespace;

// Initialize data
$docRoot = "/System/Resources/Documentation/";
$manualFilePath = $docRoot.$objectDomain."/".$objectPath."/".$objectName.".php.xml";
$classMan = new classDocumentor();

// Load manual
$error_man = FALSE;
try
{
	$classMan->loadFile($manualFilePath);
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
		$manualFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
		$parentClassMan = new classDocumentor();
		
		try
		{
			$parentClassMan->loadFile($manualFilePath);
			$parentClassInfo = $parentClassMan->getInfo();
			$extends = $parentClassInfo['extends'];
		}
		catch (Exception $ex)
		{
			$extends = NULL;
		}
	}
}

// Build content
$content = new MContent($moduleID);
$container = $content->build("", "sdkManual", TRUE)->get();

if ($error_man)
{
	// Create deprecated notification
	$ntf = new notification();
	$depNtf = $ntf->build("error", $header = TRUE, $footer = FALSE, $timeout = FALSE);
	
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
	return $content->getReport("#docViewer");
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

// Class Info
$version = DOM::create("span", (empty($classInfo['version']) ? "0.0-0" : $classInfo['version'])." (V)");
$box = HTML::select(".boxInfo .version")->item(0);
DOM::append($box, $version);

$date = DOM::create("span", date("F j, Y, G:i (T)", $classInfo['datecreated'])." (C)");
$box = HTML::select(".boxInfo .created")->item(0);
DOM::append($box, $date);

$date = DOM::create("span", date("F j, Y, G:i (T)", $classInfo['daterevised'])." (M)");
$box = HTML::select(".boxInfo .modified")->item(0);
DOM::append($box, $date);

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

// Class throws
if (!empty($classInfo['throws'])) 
{ 
	$throws = DOM::create("div", "", "", "classThrows section");
	DOM::append($classInfo, $throws);
	
	$title = DOM::create("span", "Throws: ");
	DOM::append($throws, $title);
	
	$text = DOM::create("span", $info['throws'], "", "");
	DOM::append($throws, $text);
}

if (!empty($classInfo['deprecated']))
{
	// Create deprecated notification
	$ntf = new notification();
	$depNtf = $ntf->build("warning", $header = TRUE, $footer = FALSE, $timeout = FALSE);
	
	// Add header
	$header = DOM::create("h2", "The class is deprecated.");
	$ntf->append($header);
	
	// Add message
	$message = DOM::create("p", $classInfo['deprecated']);
	$ntf->append($message);
	
	// Add notification to description
	$container = HTML::select(".classDeclContainer")->item(0);
	$depNtf = $ntf->get();
	DOM::append($container, $depNtf);
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
	foreach ($inheritance as $objectName)
	{
		// Load documentation
		$objectPath = str_replace("\\", "/", $objectName);
		$manualFilePath = $docRoot.$objectDomain."/".$objectPath.".php.xml";
		$parentClassMan = new classDocumentor();
		$parentClassMan->loadFile($manualFilePath);
		
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
			$attr['class'] = $objectName;
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
			$attr['class'] = $objectName;
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
			$attr['class'] = $objectName;
			$title = moduleLiteral::get($moduleID, "lbl_inheritedClassMethods", $attr);
			$hd = DOM::create("div", $title, "", "subHeader");
			DOM::append($methodContainer, $hd);
			buildMethodSection($moduleID, $parentClassMethods, $methodContainer);
		}
	}
}




// Return the report
return $content->getReport("#docViewer");

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
				$message = DOM::create("p", $methodInfo['deprecated']);
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
			DOM::append($methodContainer, $methodTog);
		}
}



function getMethodDeclaration($scope, $method)
{
	$methodDeclaration = DOM::create("div", "", "", "methodDeclaration decline");
	
	if (!empty($method['deprecated']))
	{
		$deprecationIdentifier = DOM::create("span", "{Deprecated}", "", "mDepr modifier");
		DOM::append($methodDeclaration, $deprecationIdentifier);
	}
	
	$scopeElement = DOM::create("span", $scope, "", "mScope modifier");
	DOM::append($methodDeclaration, $scopeElement);
	
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

	return $methodDeclaration;
}
//#section_end#
?>