<?php

class MobileAppHandler extends RequestHandler
{
	static public $forceDomain = false;
	static public $minifierCacheTime = 3600; // 1 hour
	static public $webDeployTarget = 'web';

	static public function handleRequest()
	{
		if(static::$forceDomain && $_SERVER['HTTP_HOST'] != static::$forceDomain)
		{
			header('Location: http://'.static::$forceDomain.$_SERVER['REQUEST_URI']);
			exit();
		}
	
		$path = static::getPath();
		
		// force trailing slash
		if(count($path)==0)
		{
			MICS::redirect('/'.MICS::getApp().'/');
		}
		
		// index request
		if(empty($path[0]))
		{
			return static::handleIndexRequest();
		}
		
		// handle special resources
		if($path[0] == 'js')
			return static::handleJavascriptRequest(array_slice($path, 1));
		elseif($path[0] == 'css')
			return static::handleCssRequest(array_slice($path, 1));
		else
			return static::handleResourceRequest($path);
	}
	
	static public function handleIndexRequest()
	{
		$templateNode = Site::resolvePath(array('app-root', 'index.tpl'));
		
		if(!$templateNode)
			return static::throwNotFoundError('app-root/index.tpl not found');
			
		$template = new TemplateResource($templateNode);
		$dwoo = TemplateResponse::getInstance();
		$dwoo->addPlugin('loadJS', array(get_called_class(), 'getJSLoader'));
		$dwoo->addPlugin('loadCSS', array(get_called_class(), 'getCSSLoader'));
		$dwoo->output($template, array('deployTarget' => static::$webDeployTarget));
		exit();
	}
	
	static public function getJSLoader($paths)
	{
		if(!empty($_REQUEST['debug']))
		{
			$appRoot = Site::resolvePath('app-root');
			$return = '';
			foreach(static::getSourceNodes($paths, 'js') AS $sourceFile)
			{
				$return .= '<script type="text/javascript" src="'.implode('/',$sourceFile->getFullPath($appRoot)).'?debug=1'.(empty($_REQUEST['refresh'])?'':'&refresh=1').'"></script>'.PHP_EOL;
			}
			return $return;
		}
		else
		{
			return '<script type="text/javascript" src="js/'.$paths.(empty($_REQUEST['refresh'])?'':'?refresh=1').'"></script>';
		}
	}
	
	static public function getCSSLoader($paths)
	{
    	if(!empty($_REQUEST['debug']))
		{
			$appRoot = Site::resolvePath('app-root');
			$return = '';
			foreach(static::getSourceNodes($paths, 'css') AS $sourceFile)
			{
				$return .= '<link rel="stylesheet" href="'.implode('/',$sourceFile->getFullPath($appRoot)).'?debug=1'.(empty($_REQUEST['refresh'])?'':'&refresh=1').'" type="text/css">'.PHP_EOL;
			}
			return $return;
		}
		else
		{
			return '<link rel="stylesheet" href="css/'.$paths.(empty($_REQUEST['refresh'])?'':'?refresh=1').'" type="text/css">';
		}
	}
	
	static public function handleJavascriptRequest($paths)
	{
		$pathsStr = implode('/', $paths);
		$cacheKey = 'js:'.SHA1($pathsStr);
		
		if(!($code = Cache::fetch($cacheKey)) || !empty($_REQUEST['refresh']) || !empty($_REQUEST['debug']))
		{
			$sourceNodes = static::getSourceNodes($pathsStr, 'js');
			$code = '';
			foreach($sourceNodes AS $sourceNode)
			{
				$code .= '/*'.$sourceNode->Handle.'*/';
				
				if(empty($_REQUEST['debug']))
					$code .= JSMin::minify(file_get_contents($sourceNode->RealPath));
				else
					$code .= file_get_contents($sourceNode->RealPath);
					
				$code .= PHP_EOL;
			}
			
			if(empty($_REQUEST['debug']))
				Cache::store($cacheKey, $code, static::$minifierCacheTime);
		}
		
		// render output
		header('Content-Type: application/javascript');
		print $code;
		exit();
	}
	
	static public function handleCssRequest($paths)
	{
		$pathsStr = implode('/', $paths);
		$cacheKey = 'css:'.SHA1($pathsStr);
		if(!($code = Cache::fetch($cacheKey)) || !empty($_REQUEST['refresh']))
		{
			$sourceNodes = static::getSourceNodes($pathsStr, 'css');
			$code = '';
			foreach($sourceNodes AS $sourceNode)
			{
				//MICS::dump($sourceNode, 'condensing')
				$code .= '/*'.$sourceNode->Handle.'*/'.PHP_EOL;
				
				if(empty($_REQUEST['debug']))
					$code .= CssMin::minify(file_get_contents($sourceNode->RealPath));
				else
					$code .= file_get_contents($sourceNode->RealPath);
					
/* 				$code .= file_get_contents($sourceNode->RealPath); */
/*
				$code .= CssMin::minify(file_get_contents($sourceNode->RealPath), array(
					'RemoveEmptyAtBlocks' => false
					,'RemoveEmptyRulesets' => false
					,'Variables' => false
					,'RemoveComments' => false
				), array(
					'Variables' => false
				));
*/
				
				$code .= PHP_EOL;
			}
			
			Cache::store($cacheKey, $code, static::$minifierCacheTime);
		}
		
		// render output
		header('Content-Type: text/css');
		print $code;
		exit();
	}
	
	static public function getSourceNodes($paths, $root)
	{
		$paths = static::splitMultipath($paths);
		$sourceNodes = array();
		
		foreach($paths AS $path)
		{
			array_unshift($path, 'app-root', $root);
			list($filename) = array_slice($path, -1);
			
			if($filename == '*')
			{
				array_pop($path);
				$collection = Site::resolvePath($path);
				if(!$collection || !is_a($collection, 'SiteCollection'))
				{
					return static::throwNotFoundError('Collection "'.implode('/', $path).'" does not exist');
				}
				
				foreach(SiteFile::getTree($collection) AS $node)
					$sourceNodes[$node->ID] = $node;
			}
			else
			{
				$node = Site::resolvePath($path);
				if(!$node || !is_a($node, 'SiteFile'))
				{
					return static::throwNotFoundError('Source file "'.implode('/', $path).'" does not exist');
				}
				
				$sourceNodes[$node->ID] = $node;
			}
		}
		
		return $sourceNodes;
	}
	
	static public function splitMultipath($paths)
	{
		if(is_array($paths))
			$paths = implode('/', $paths);
			
		return array_map(function($path) {
			return array_filter(explode('/', $path));
		}, explode('+', $paths));
	}
	
	static public function handleResourceRequest($path)
	{
		array_unshift($path, 'app-root');
		$fileNode = Site::resolvePath($path);
		
		if(!$fileNode || !is_a($fileNode, 'SiteFile'))
			return static::throwNotFoundError('Resource not found');
			
		$fileNode->outputAsResponse();
		exit();
	}
}