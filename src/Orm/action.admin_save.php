<?php

if (!function_exists("cmsms")) exit;

if (isset($params['deleteLog'])){
	if(file_exists(OrmTRACE::getLogFile())){
		unlink(OrmTRACE::getLogFile());
	}
	OrmTrace::info("Reinitiate the log file");
} elseif (isset($params['deleteCache'])){
	OrmCache::clearCache();
	OrmTrace::info("Reinitiate the cache content");
} else if (isset($params['level']) || isset($params['cache']) ){
	$this->SetPreference('loglevel', $params['level']);
	$this->SetPreference('cacheType', $params['cache']);
	
	//We reload the cache anyway
	OrmCache::clearCache();
	OrmTrace::info("Reinitiate the cache content");
}

$this->redirect($id,'defaultadmin');


?>