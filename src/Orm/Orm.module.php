<?php
	/**
	* Class of cmsmadesimple's API. Used to make a link between the API of CmsMadeSimple and other modules'
	*
	* @since 0.0.1
	* @author Bess
	* @package Orm
	**/

	/**
	* The Class Orm define the module Orm and allow having all the orm functionalities into another module
	*
	* @since 0.0.1
	* @author Bess
	* @package cmsms
	*/
class Orm extends CMSModule {

	function GetName() {
		return 'Orm';
	}

	function GetFriendlyName() {
		return $this->Lang('friendlyname');
	}

	function GetVersion() {
		return '0.2.0';
	}
  
	function GetDependencies()
 	{
    	return array();
	}

	function GetHelp() {
		return $this->Lang('help');
	}

	function GetAuthor() {
		return 'Kevin Danezis (aka Bess)';
	}

	function GetAuthorEmail() {
		return 'contact at furie point be';
	}

	function GetChangeLog() {
		return $this->Lang('changelog');
	}

	function GetAdminDescription() {
		return $this->Lang('moddescription');
	}

	function MinimumCMSVersion() {
		return "1.11.0";
	}

	function IsPluginModule() {
		return false;
	}

	function HasAdmin() {
		return true;
	}

	function GetAdminSection() {
		return 'extensions';
	}

	function VisibleToAdminUser() {
		return true;
	}

	function InitializeFrontend() {
	}

	function InitializeAdmin() {
	}

	function AllowSmartyCaching() {
		return false;
	}

	function LazyLoadFrontend() {
		return false;
	}

	function LazyLoadAdmin() {
	  return false;
	}
	
	function InstallPostMessage() {
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage() {
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage() {
		return $this->Lang('really_uninstall');
	}  
	
	function DisplayErrorPage($msg) {
		echo "<h3>".$msg."</h3>";
	} 
		 
	private function GetMyModulePath() {
		return parent::GetModulePath();		
	}

	protected function __autoload() {	
		spl_autoload_register(array($this, 'autoload_classes'));
		//spl_autoload_register(array($this, 'autoload_classes_addon'));
		
		//We're listing the class declared into the directory of the child module
		$repertoire = cms_join_path($this->GetMyModulePath(),'lib');
		
		$liste['entities'] = array();
		$liste['associate'] = array();
		
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($repertoire));
		foreach($objects as $name => $object){
			if(stripos($name, 'class.entity.') !== FALSE){			
				$classname = substr($object->getFileName() , 13 ,strlen($object->getFileName()) - 4 - 13);
				$liste['entities'][] = array('filename'=>$name, 'basename'=>$object->getFileName(), 'classname'=>$classname);
			} elseif(stripos($name, 'class.assoc.') !== FALSE) {
				$classname = substr($object->getFileName() , 12 ,strlen($object->getFileName()) - 4 - 12);
				$liste['associate'][] = array('filename'=>$name, 'basename'=>$object->getFileName(), 'classname'=>$classname);
			} else {
			}
		}
		
		foreach($liste['entities'] as $element) {
			$className = $element['classname'];
			$filename = $element['filename'];
			OrmTrace::debug("importing Entity ".$className." into the module ".$this->getName());
			require_once($filename);			
			$entity = new $className();
		}
		foreach($liste['associate'] as $element) {
			$className = $element['classname'];
			$filename = $element['filename'];
			OrmTrace::debug("importing Associate Entity ".$className." into the module ".$this->getName());
			require_once($filename);
			$entity = new $className();
		}
	}
	
	public function autoload_classes($classname){
		$Orm = new Orm();
		$path = $Orm->GetMyModulePath();
		$fn = cms_join_path($path,"lib","class.".strtolower($classname).".php");
		
		if(file_exists($fn)){
			require_once($fn);
			OrmTrace::debug("importing $fn with success");
		} else {
			OrmTrace::debug("File $fn not found, we skip it");
		}
	}
	
	/**
	 * Shortcut to call all the instances for a single module
	 *
	 * @return List<OrmEntity> the entities for the current parent's namespace
	 **/
	public function getAllInstances(){
		return MyAutoload::getAllInstances(parent::GetName());
	}
	/*
	public function autoload_classes_addon($classname){
		OrmTrace::debug("&nbsp;&nbsp;&nbsp;$classname");
		
		$path = $this->GetMyModulePath();
		
		$fn = null;
	   if(stripos($classname, "HTML_FIELD") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.fieldhtml.php");
	   } else if(stripos($classname, "SEARCH_FIELD") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.searchfield.php");
	   } else if(stripos($classname, "FIELD_") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.fieldssystem.php");
	   } else if(stripos($classname, "FILTRE_") !== FALSE)
	   {
			$fn = cms_join_path($path,"class","class.add.filtre.php");
	   }
	   
	   if($fn != null)
	   {
			OrmTrace::debug( "import d'un addon du projet ".$this->getName().": $fn");
	   
			if(file_exists($fn)){
				require_once($fn);
			}
		}
	}*/
	/*
	function SearchReindex(&$module = null) {
		//On �vite de s'auto-indexer.
		if($this->getName() == 'Mmmfs')
			return;		
			
		// OrmIndexing::setSearch($module);
		OrmIndexing::SearchReindex($this->getName());
	}*/

	/**
	 * Appel�e par Search pour afficher un r�sultat
	 *//*
	function SearchResult($returnid, $entityId, $attr = '') {	
		//On ne retourne rien de Mmmfs de toute mani�re
		if($this->getName() == 'Mmmfs')
			return;	
		
		return OrmIndexing::SearchResult($this, $id, $returnid, $entityId, $attr);
	}*/

} 
?>
