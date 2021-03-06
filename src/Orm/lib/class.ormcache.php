<?php
/**
 * Contains the class wich provide an global interface for all the caching systems
 *
 * @since 0.2.0
 * @author Bess
 * @package Orm
 **/


/**
 *
 *  Static classe used to provide list of signature for all the caching systems
 *
 *  Example : 
 *  <code>
 *  	//Call to cmsms to get the database connector
 *  	$db = cmsms()->GetDb();
 *      $cacheInstance = OrmCache::getInstance();
 *  	
 *  	//Defines a new Customer entity
 *  	$entity = MyAutoload::getInstance('myModule', 'customer');
 *  	
 *  	//Select all Customers
 *  	$querySelect = 'Select * FROM '.$entity->getDbname();
 *  	
 *  	//If the caching system already know the answer : we return the result immediately
 *  	if($cacheInstance->isCache($querySelect)) {
 *  		return $cacheInstance->getCache($querySelect);
 *  	}
 *  	
 *  	//So we need to execute the query
 *  	$result = $db->Execute($querySelect);
 *  	if ($result === false){die("Database error!");}
 *  	
 *  	$entitys = array();
 *  	while ($row = $result->FetchRow()) {
 *  		$entitys[] = OrmCore::rowToEntity($entity, $row);
 *  	}
 *  	
 *  	//Don't forget to push the result into the caching system for the next call
 *  	$cacheInstance->setCache($querySelect, null, $entitys);
 
 *  	return $entitys;
 *	</code>
 *
 * @since 0.2.0
 * @author Bess
 * @package Orm
 **/
abstract class OrmCache {	

	public static $NONE = 0;
	public static $SCRIPT = 1;
		
	/**
	 * Private constructor
	 */
	protected function __construct() {}
	
	/**
	 * Will return a implementation of the cache.
	 *
	 * @param mixed not requiered : the type of cache. By default it will take the type of cache defined as default into CmsMadeSimple
	 *
	 * @return an instance of Cache.
	 **/
	public function getInstance($typeCache = null){
		
		if($typeCache == null){
			$orm = cmsms()->GetModuleOperations()->get_module_instance('Orm');
			$typeCache = $orm->GetPreference('cacheType', OrmCache::$NONE);
		}
		
		switch($typeCache){
			case OrmCache::$NONE:
				return OrmCacheNone::getInstance();
				break;
			case OrmCache::$SCRIPT:
				return OrmCacheScript::getInstance();
				break;
			default:
				OrmTrace::error("Type of Cache #{$typeCache} is not a valid Type of Cache");
				exit -1;
		}
	}
			
	/**
	 *	Set the cache for a sql request, its parameters and of course the result
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 * @param object the result
	 */
	public abstract function setCache($sql, $params = null, $value);
	
	/**
	 * Querying the cache for a sql request and its parameters
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 *
	 * @return object the result
	 */
	public abstract function getCache($sql, $params = null);

	/**
	 * Return true if a cache exist for a sql request and its parameters
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 *
	 * @return boolean true if the cache exists
	 */	
	public abstract function isCache($sql, $params = null);

	/**
	 * Empty the cache. Very important if between 2 querying, the system may insert/delete/update some data in the database
	 *  In the Orm system, we always drop the cache in the insert/delete/update function.
	 */	
	public abstract function clearCache();

	/**
	 * Return a unique hash for a sql request and its parameters
	 *  this hash is used to Defines a unique entry into the cache
     * 
	 * @param string the sql query
	 * @param array the parameters into a array. May be null
	 *
	 * @return string the hash
	 */	
	public function hash($sql, $params = null) {
		if($params == null){
			$params = "";
		}
		
		$p = serialize($params);

		return md5($sql.$p);
	}
}

?>