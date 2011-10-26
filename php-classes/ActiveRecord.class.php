<?php


class ActiveRecord
{
	// configurables
	/**
	 * Name of table
	 * @var string
	 */
	static public $tableName = 'records';
	
	/**
	 * Noun to describe singular object
	 * @var string
	 */
	static public $singularNoun = 'record';
	
	/**
	 * Noun to describe a plurality of objects
	 * @var string
	 */
	static public $pluralNoun = 'records';
	
	/**
	 * String to identify this class with in administrative interfaces
	 * @var string
	 */
	static public $classTitle = 'Untitled Class';
	
	/**
	 * Defaults values for field definitions
	 * @var array
	 */
	static public $fieldDefaults = array(
		'type' => 'string'
		,'notnull' => true
	);
	
	/**
	 * Field definitions
	 * @var array
	 */
	static public $fields = array(
		'ID' => array(
			'type' => 'integer'
			,'autoincrement' => true
			,'unsigned' => true
		)
		,'Class' => array(
			'type' => 'enum'
			,'notnull' => true
			,'values' => array()
		)
		,'Created' => array(
			'type' => 'timestamp'
			,'default' => 'CURRENT_TIMESTAMP'
		)
		,'CreatorID' => array(
			'type' => 'integer'
			,'notnull' => false
		)
	);
	
	/**
	 * Index definitions
	 * @var array
	 */
	static public $indexes = array();
	
	
	/**
	 * Relationship definitions
	 * @var array
	 */
	static public $relationships = array(
		/*
'Context' => array(
			'type' => 'context-parent'
		)
*/
		'Creator' => array(
			'type' => 'one-one'
			,'class' => 'Person'
			,'local' => 'CreatorID'
		)
	);
	
	/**
	 * Map of existing tables
	 * @var array
	 */
	static public $tables;
	
	/**
	 * Class names of possible contexts
	 * @var array
	 */
	static public $contextClasses;
	
	/**
	 * Default conditions for get* operations
	 * @var array
	 */
	static public $defaultConditions = array();
	
	// support subclassing
	static public $rootClass = null;
	static public $defaultClass = null;
	static public $subClasses = array();

	// protected members
	protected static $_classFields = array();
	protected static $_classRelationships = array();
	
	protected $_record;
	protected $_convertedValues;
	protected $_relatedObjects;
	protected $_isDirty;
	protected $_isPhantom;
	protected $_isValid;
	protected $_isNew;
	protected $_validator;
	protected $_validationErrors;
	protected $_originalValues;
	
	
	// magic methods
	/**
	 * MICS extended magic method called after class and configuration are loaded
	 */
	static function __classLoaded()
	{
		static::_defineFields();
		static::_initFields();
		
		static::_defineRelationships();
		static::_initRelationships();
	}
	
	function __construct($record = array(), $isDirty = false, $isPhantom = null)
	{
		$this->_record = static::_convertRecord($record);
		$this->_relatedObjects = array();
		$this->_isPhantom = isset($isPhantom) ? $isPhantom : empty($record);
		$this->_isDirty = $this->_isPhantom || $isDirty;
		$this->_isNew = false;
		
		$this->_isValid = true;
		$this->_validationErrors = array();
		$this->_originalValues = array();

		// authorize read access
		if(!$this->authorizeRead())
		{
			throw new UserUnauthorizedException('Read authorization denied');
		}
		
		// set Class
		if(static::_fieldExists('Class') && !$this->Class)
		{
			$this->Class = get_class($this);
		}
		
	}
	
	static protected function _convertRecord($record)
	{	
		return $record;
	}
	
	function __get($name)
	{
		return $this->getValue($name);
	}
	
	function __set($name, $value)
	{
		return $this->setValue($name, $value);
	}
	
	public function getValue($name)
	{
		switch($name)
		{
			case 'isDirty':
				return $this->_isDirty;
				
			case 'isPhantom':
				return $this->_isPhantom;
				
			case 'isValid':
				return $this->_isValid;
				
			case 'isNew':
				return $this->_isNew;
				
			case 'validationErrors':
				return array_filter($this->_validationErrors);
				
			case 'data':
				return $this->getData();
				
			case 'originalValues':
				return $this->_originalValues;
				
			default:
			{
				// handle field
				if(static::_fieldExists($name))
				{
					return $this->_getFieldValue($name);
				}
				// handle relationship
				elseif(static::_relationshipExists($name))
				{
					return $this->_getRelationshipValue($name);
				}
				// default Handle to ID if not caught by fieldExists
				elseif($name == 'Handle')
				{
					return $this->ID;
				}
				// handle a dot-path to related record field
				elseif(count($path = explode('.', $name)) >= 2 && static::_relationshipExists($path[0]))
				{
					$related = $this->_getRelationshipValue(array_shift($path));

					while(is_array($related))
					{
						$related = $related[array_shift($path)];
					}

					return is_object($related) ? $related->getValue(implode('.',$path)) : $related;
				}
				// undefined
				else
				{
					return null;
				}
			}
		}
	}
	
	public function setValue($name, $value)
	{
		// handle field
		if(static::_fieldExists($name))
		{
			$this->_setFieldValue($name, $value);
		}
		// handle relationship
		elseif(static::_relationshipExists($name))
		{
			$this->_setRelationshipValue($name, $value);
		}
		// undefined
		else
		{
			return false;
		}
	}
	
	
	// public methods
	public function authorizeRead()
	{
		return true;
	}
	
	static public function create($values = array(), $save = false)
	{
		$className = get_called_class();
		
		// create class
		$ActiveRecord = new $className();
		$ActiveRecord->setFields($values);
		
		if($save)
		{
			$ActiveRecord->save();
		}
		
		return $ActiveRecord;
	}
	
	
	public function isA($class)
	{
		return is_a($this, $class);
	}
	
	
	public function addValidationErrors($array)
	{
		foreach($array AS $field => $errorMessage)
		{
			$this->addValidationError($field, $errorMessage);
		}
	}

	public function addValidationError($field, $errorMessage)
	{
		$this->_isValid = false;
		$this->_validationErrors[$field] = $errorMessage;
	}
	
	public function getValidationError($field)
	{
		// break apart path
		$crumbs = explode('.', $field);

		// resolve path recursively
		$cur = &$this->_validationErrors;
		while($crumb = array_shift($crumbs))
		{
			if(array_key_exists($crumb, $cur))
				$cur = &$cur[$crumb];
			else
				return null;
		}

		// return current value
		return $cur;
	}
	
	
	public function validate($deep = true)
	{
		$this->_isValid = true;
		$this->_validationErrors = array();
		
		if(!isset($this->_validator))
		{
			$this->_validator = new RecordValidator($this->_record);
		}
		
		if($deep)
		{
			// validate relationship objects
			foreach(static::$_classRelationships[get_called_class()] AS $relationship => $options)
			{
				if(empty($this->_relatedObjects[$relationship]))
				{
					continue;
				}
				
				
				if($options['type'] == 'one-one')
				{
					if($this->_relatedObjects[$relationship]->isDirty)
					{
						$this->_relatedObjects[$relationship]->validate();
						$this->_isValid = $this->_isValid && $this->_relatedObjects[$relationship]->isValid;
						$this->_validationErrors[$relationship] = $this->_relatedObjects[$relationship]->validationErrors;
					}
				}
				elseif($options['type'] == 'one-many')
				{
					foreach($this->_relatedObjects[$relationship] AS $i => $object)
					{
						if($object->isDirty)
						{
							$object->validate();
							$this->_isValid = $this->_isValid && $object->isValid;
							$this->_validationErrors[$relationship][$i] = $object->validationErrors;
						}					
					}
				}
				/*elseif($options['type'] == 'contextual')
				{
					foreach($this->_relatedObjects[$relationship] AS $i => $object)
					{
						if($object->isDirty)
						{
							$object->validate();
							$this->_isValid = $this->_isValid && $object->isValid;
							$this->_validationErrors[$relationship][$i] = $object->validationErrors;
						}					
					}
				}*/
				
			}
		}
		
		return $this->_isValid;
	}
	
	protected function finishValidation()
	{
		$this->_isValid = $this->_isValid && !$this->_validator->hasErrors();
		
		if(!$this->_isValid)
		{
			$this->_validationErrors = array_merge($this->_validationErrors, $this->_validator->getErrors());	
		}

		return $this->_isValid;
	}
	
	public function changeClass($className = false, $fieldValues = false)
	{
		if(!$className)
		{
			$className = $this->Class;
		}
	
		// return if no change needed
		if($className == get_class($this))
		{
			$ActiveRecord = $this;
		}
		else
		{
			$this->_record[static::_cn('Class')] = $className;
			$ActiveRecord = new $className($this->_record, true, $this->isPhantom);
		}
		
		if($fieldValues)
		{
			$ActiveRecord->setFields($fieldValues);
		}
		
		if(!$this->isPhantom)
			$ActiveRecord->save();
		
		return $ActiveRecord;
	}
	
	public function setFields($values)
	{
		foreach($values AS $field => $value)
		{
			$this->_setFieldValue($field, $value);
		}
	}
	
	public function setField($field, $value)
	{
		$this->_setFieldValue($field, $value);
	}
	
	public function getData()
	{
		$data = array();
		
		foreach(static::$_classFields[get_called_class()] AS $field => $options)
		{
			$data[$field] = $this->_getFieldValue($field);
		}
		
		if($this->validationErrors)
		{
			$data['validationErrors'] = $this->validationErrors;
		}
		
		return $data;
	}
	
	public function isFieldDirty($field)
	{
		return $this->isPhantom || array_key_exists($field, $this->_originalValues);
	}
	
	public function dumpData()
	{
		MICS::dump($this->getData(), get_class($this));
	}
	
	public function save($deep = true)
	{
		// set creator
		if(static::_fieldExists('CreatorID') && !$this->CreatorID && $_SESSION['User'])
		{
			$this->CreatorID = $_SESSION['User']->ID;
		}
		
		// set created
		if(static::_fieldExists('Created') && (!$this->Created || ($this->Created == 'CURRENT_TIMESTAMP')))
		{
			$this->Created = time();
		}
		
		// validate
		if(!$this->validate($deep))
		{
			throw new RecordValidationException($this, 'Cannot save invalid record');
		}
		
		// clear caches
		foreach($this->getClassFields() AS $field => $options)
		{
			if(!empty($options['unique']) || !empty($options['primary']))
			{
				$key = sprintf('%s/%s:%s', static::$tableName, $field, $this->getValue($field));
				DB::clearCachedRecord($key);
			}
		}
		
		// traverse relationships
		if($deep)
		{
			$this->_saveRelationships();
		}

		if($this->isDirty)
		{
			// prepare record values
			$recordValues = $this->_prepareRecordValues();
	
			// transform record to set array
			$set = static::_mapValuesToSet($recordValues);
			
			// create new or update existing
			if($this->_isPhantom)
			{
				DB::nonQuery(
					'INSERT INTO `%s` SET %s'
					, array(
						static::$tableName
						, join(',', $set)
					)
				);
				
				$this->_record['ID'] = DB::insertID();
				$this->_isPhantom = false;
				$this->_isNew = true;
			}
			elseif(count($set))
			{
				DB::nonQuery(
					'UPDATE `%s` SET %s WHERE `%s` = %u'
					, array(
						static::$tableName
						, join(',', $set)
						, static::_cn('ID')
						, $this->ID
					)
				);
			}
			
			// update state
			$this->_isDirty = false;
		}
		
		// traverse relationships again
		if($deep)
		{
			$this->_postSaveRelationships();
		}
	}
	
	protected function _saveRelationships()
	{
		// save relationship objects
		foreach(static::$_classRelationships[get_called_class()] AS $relationship => $options)
		{
			//MICS::dump($this->_relatedObjects[$relationship], "Saving Related: $relationship");
			if($options['type'] == 'one-one')
			{
				if(isset($this->_relatedObjects[$relationship]) && $options['local'] != 'ID')
				{
					$this->_relatedObjects[$relationship]->save();
					$this->_setFieldValue($options['local'], $this->_relatedObjects[$relationship]->getValue($options['foreign']));
				}
			}
			elseif($options['type'] == 'one-many')
			{
				if(isset($this->_relatedObjects[$relationship]) && $options['local'] != 'ID')
				{
					foreach($this->_relatedObjects[$relationship] AS $related)
					{
						if($related->isPhantom)
							$related->_setFieldValue($options['foreign'], $this->_getFieldValue($options['local']));
							
						$related->save();
					}
				}
			}
			elseif($options['type'] == 'handle')
			{
				if(isset($this->_relatedObjects[$relationship]))
				{
					$this->_setFieldValue($options['local'], $this->_relatedObjects[$relationship]->Handle);
				}
			}
			else
			{
				// TODO: Implement other methods
			}
			
		}
	}
	
	protected function _postSaveRelationships()
	{
		//die('psr');
		// save relationship objects
		foreach(static::$_classRelationships[get_called_class()] AS $relationship => $options)
		{
			if(!isset($this->_relatedObjects[$relationship]))
			{
				continue;
			}
		
			//MICS::dump($this->_relatedObjects[$relationship], "Saving Related: $relationship");
			if($options['type'] == 'handle')
			{
				$this->_relatedObjects[$relationship]->Context = $this;
				$this->_relatedObjects[$relationship]->save();
			}
			elseif($options['type'] == 'one-one' && $options['local'] == 'ID')
			{
				$this->_relatedObjects[$relationship]->setField($options['foreign'], $this->getValue($options['local']));
				$this->_relatedObjects[$relationship]->save();
			}
			elseif($options['type'] == 'one-many' && $options['local'] == 'ID')
			{
				foreach($this->_relatedObjects[$relationship] AS $related)
				{
					$related->setField($options['foreign'], $this->getValue($options['local']));
					$related->save();
				}
			}
		}
	}
	
	
	public function destroy()
	{
		return static::delete($this->ID);
	}
	
	static public function delete($id)
	{
		DB::nonQuery('DELETE FROM `%s` WHERE `%s` = %u', array(
			static::$tableName
			,static::_cn('ID')
			,$id
		));
		
		return DB::affectedRows() > 0;
	}
	
	static public function getByContextObject(ActiveRecord $Record, $options = array())
	{
		return static::getByContext($Record::$rootClass, $Record->ID, $options);
	}
	
	static public function getByContext($contextClass, $contextID, $options = array())
	{
		$options = MICS::prepareOptions($options, array(
			'conditions' => array()
			,'order' => false
		));
		
		$options['conditions']['ContextClass'] = $contextClass;
		$options['conditions']['ContextID'] = $contextID;
	
		$record = static::getRecordByWhere($options['conditions'], $options);

		$className = static::_getRecordClass($record);
		
		return $record ? new $className($record) : null;
	}
	
	static public function getByHandle($handle)
	{
		return static::getByID($handle);
	}
	
	static public function getByID($id)
	{
		$record = static::getRecordByField('ID', $id, true);
		
		return static::instantiateRecord($record);
	}
		
	static public function getByField($field, $value, $cacheIndex = false)
	{
		$record = static::getRecordByField($field, $value, $cacheIndex);
		
		return static::instantiateRecord($record);
	}
	
	static public function getRecordByField($field, $value, $cacheIndex = false)
	{
		$query = 'SELECT * FROM `%s` WHERE `%s` = "%s" LIMIT 1';
		$params = array(
			static::$tableName
			, static::_cn($field)
			, DB::escape($value)
		);
	
		if($cacheIndex)
		{
			$key = sprintf('%s/%s:%s', static::$tableName, $field, $value);
			return DB::oneRecordCached($key, $query, $params);
		}
		else
		{
			return DB::oneRecord($query, $params);
		}
			
	}
	
	static public function getByWhere($conditions, $options = array())
	{
		$record = static::getRecordByWhere($conditions, $options);
		
		return static::instantiateRecord($record);
	}
	
	static public function getRecordByWhere($conditions, $options = array())
	{
		if(!is_array($conditions))
		{
			$conditions = array($conditions);
		}
		
		$options = MICS::prepareOptions($options, array(
			'order' => false
		));

		// initialize conditions and order
		$conditions = static::_mapConditions($conditions);
		$order = $options['order'] ? static::_mapFieldOrder($options['order']) : array();
		
		return DB::oneRecord(
			'SELECT * FROM `%s` WHERE (%s) %s LIMIT 1'
			, array(
				static::$tableName
				, join(') AND (', $conditions)
				, $order ? 'ORDER BY '.join(',', $order) : ''
			)
		);	
	}
	
	static public function getByQuery($query, $params = array())
	{
		return static::instantiateRecord(DB::oneRecord($query, $params));
	}

	static public function getAllByClass($className = false, $options = array())
	{
		return static::getAllByField('Class', $className ? $className : get_called_class(), $options);
	}
	
	static public function getAllByContextObject(ActiveRecord $Record, $options = array())
	{
		return static::getAllByContext($Record::$rootClass, $Record->ID, $options);
	}

	static public function getAllByContext($contextClass, $contextID, $options = array())
	{
		$options = MICS::prepareOptions($options, array(
			'conditions' => array()
		));
		
		$options['conditions']['ContextClass'] = $contextClass;
		$options['conditions']['ContextID'] = $contextID;
	
		return static::instantiateRecords(static::getAllRecordsByWhere($options['conditions'], $options));
	}
	
	static public function getAllByField($field, $value, $options = array())
	{
		return static::getAllByWhere(array($field => $value), $options);
	}
		
	static public function getAllByWhere($conditions = array(), $options = array())
	{
		return static::instantiateRecords(static::getAllRecordsByWhere($conditions, $options));
	}
	
	static public function getAllRecordsByWhere($conditions = array(), $options = array())
	{
		$className = get_called_class();
	
		$options = MICS::prepareOptions($options, array(
			'indexField' => false
			,'order' => false
			,'limit' => false
			,'offset' => 0
			,'calcFoundRows' => !empty($options['limit'])
			,'joinRelated' => false
		));

		
		// handle joining related tables
		$join = '';
		if($options['joinRelated'])
		{
			if(is_string($options['joinRelated']))
			{
				$options['joinRelated'] = array($options['joinRelated']);
			}
			
			// prefix any conditions
			
			foreach($options['joinRelated'] AS $relationship)
			{
				if(!$rel = static::$_classRelationships[get_called_class()][$relationship])
				{
					die("joinRelated specifies a relationship that does not exist: $relationship");
				}
								
				switch($rel['type'])
				{
					case 'one-one':
					{
						$join .= sprintf(' JOIN `%1$s` AS `%2$s` ON(`%2$s`.`%3$s` = `%4$s`)', $rel['class']::$tableName, $relationship::$rootClass, $rel['foreign'], $rel['local']);
						break;
					}
					default:
					{
						die("getAllRecordsByWhere does not support relationship type $rel[type]");
					}
				}
			}
		}
		
		// initialize conditions
		if($conditions)
		{
			if(is_string($conditions))
			{
				$conditions = array($conditions);
			}
		
			$conditions = static::_mapConditions($conditions);
		}
		
		// build query
		$query = 'SELECT %1$s `%3$s`.* FROM `%2$s` AS `%3$s` %4$s WHERE (%5$s)';
		$params = array(
			$options['calcFoundRows'] ? 'SQL_CALC_FOUND_ROWS' : ''
			, static::$tableName
			, $className::$rootClass
			, $join
			, $conditions ? join(') AND (', $conditions) : '1'
		);

		if($options['order'])
		{
			$query .= ' ORDER BY ' . join(',', static::_mapFieldOrder($options['order']));
		}
		
		if($options['limit'])
		{
			$query .= sprintf(' LIMIT %u,%u', $options['offset'], $options['limit']);
		}
		
		if($options['indexField'])
		{
			return DB::table(static::_cn($options['indexField']), $query, $params);
		}
		else
		{
			return DB::allRecords($query, $params);
		}
	}
	
	static public function getAll($options = array())
	{
		return static::instantiateRecords(static::getAllRecords($options));
	}
	
	static public function getAllRecords($options = array())
	{
		$options = MICS::prepareOptions($options, array(
			'indexField' => false
			,'order' => false
			,'limit' => false
			,'offset' => 0
		));
		
		$query = 'SELECT * FROM `%s`';
		$params = array(
			static::$tableName
		);
		
		if($options['order'])
		{
			$query .= ' ORDER BY ' . join(',', static::_mapFieldOrder($options['order']));
		}
		
		if($options['limit'])
		{
			$query .= sprintf(' LIMIT %u,%u', $options['offset'], $options['limit']);
		}
		
		if($options['indexField'])
		{
			return DB::table(static::_cn($options['indexField']), $query, $params);
		}
		else
		{
			return DB::allRecords($query, $params);
		}

	}
	
	static public function getAllByQuery($query, $params = array())
	{
		return static::instantiateRecords(DB::allRecords($query, $params));
	}

	static public function getTableByQuery($keyField, $query, $params)
	{
		return static::instantiateRecords(DB::table($keyField, $query, $params));
	}

	
	
	static public function instantiateRecord($record)
	{
		$className = static::_getRecordClass($record);
		return $record ? new $className($record) : null;
	}
	
	static public function instantiateRecords($records)
	{
		foreach($records AS &$record)
		{
			$className = static::_getRecordClass($record);
			$record = new $className($record);
		}
		
		return $records;
	}
	
	static public function getUniqueHandle($text, $options = array())
	{
		// apply default options
		$options = MICS::prepareOptions($options, array(
			'handleField' => 'Handle'
			,'domainConstraints' => array()
			,'alwaysSuffix' => false
			,'format' => '%s:%u'
		));
	
		// strip bad characters
		$handle = $strippedText = preg_replace(
			array('/\s+/', '/_*[^a-zA-Z0-9\-_]+_*/')
			, array('_', '-')
			, trim($text)
		);
		
		$handle = trim($handle, '-_');
		
		$where = $options['domainConstraints'];
		
		$incarnation = 0;
		do
		{
			// TODO: check for repeat posting here?
			$incarnation++;
			
			if($options['alwaysSuffix'] || $incarnation > 1)
				$handle = sprintf($options['format'], $strippedText, $incarnation);
		}
		while(static::getByWhere(array_merge($options['domainConstraints'],array($options['handleField']=>$handle))));
		
		return $handle;
	}
	
	public static function generateRandomHandle($length = 32)
	{
		// apply default options
		$options = MICS::prepareOptions($options, array(
			'handleField' => 'Handle'
		));
	
		do
		{
			$handle = substr(md5(mt_rand(0, mt_getrandmax())), 0, $length);
		}
		while( static::getByField($options['handleField'], $handle) );
		
		return $handle;
	}
	
	// protected methods
	
	/**
	 * Called when a class is loaded to define fields before _initFields
	 */
	static protected function _defineFields()
	{
		$className = get_called_class();

		// skip if fields already defined
		if(isset(static::$_classFields[$className]))
		{
			return;
		}
		
		// merge fields from first ancestor up
		$classes = class_parents($className);
		array_unshift($classes, $className);
		
		static::$_classFields[$className] = array();
		while($class = array_pop($classes))
		{
			if(!empty($class::$fields))
			{
				static::$_classFields[$className] = array_merge(static::$_classFields[$className], $class::$fields);
			}
		}
		
	} 

	
	/**
	 * Called after _defineFields to initialize and apply defaults to the fields property
	 * Must be idempotent as it may be applied multiple times up the inheritence chain
	 */
	static protected function _initFields()
	{
		$className = get_called_class();
		$optionsMask = array(
			'type' => null
			,'length' => null
			,'primary' => null
			,'unique' => null
			,'autoincrement' => null
			,'notnull' => null
			,'unsigned' => null
			,'default' => null
			,'values' => null
		);
		
		// apply default values to field definitions
		if(!empty(static::$_classFields[$className]))
		{
			$fields = array();
			
			foreach(static::$_classFields[$className] AS $field => $options)
			{
				if(is_string($field))
				{
					if(is_array($options))
					{
						$fields[$field] = array_merge($optionsMask, static::$fieldDefaults, array('columnName' => $field), $options);
					}
					elseif(is_string($options))
					{
						$fields[$field] = array_merge($optionsMask, static::$fieldDefaults, array('columnName' => $field, 'type' => $options));
					}
					elseif($options == null)
					{
						continue;
					}
				}
				elseif(is_string($options))
				{
					$field = $options;
					$fields[$field] = array_merge($optionsMask, static::$fieldDefaults, array('columnName' => $field));
				}
				
				if($field == 'Class')
				{
					// apply Class enum values
					$fields[$field]['values'] = static::$subClasses;
				}
				
				if(!isset($fields[$field]['blankisnull']) && empty($fields[$field]['notnull']))
				{
					$fields[$field]['blankisnull'] = true;
				}
				
				if($fields[$field]['autoincrement'])
				{
					$fields[$field]['primary'] = true;
				}
				
			}
			
			static::$_classFields[$className] = $fields;
		}
	}
	

	/**
	 * Called when a class is loaded to define relationships before _initRelationships
	 */
	static protected function _defineRelationships()
	{
		$className = get_called_class();
		
		// skip if fields already defined
		if(isset(static::$_classRelationships[$className]))
		{
			return;
		}
		
		// merge fields from first ancestor up
		$classes = class_parents($className);
		array_unshift($classes, $className);
		
		static::$_classRelationships[$className] = array();
		while($class = array_pop($classes))
		{
			if(!empty($class::$relationships))
			{
				static::$_classRelationships[$className] = array_merge(static::$_classRelationships[$className], $class::$relationships);
			}
		}
	}

	
	/**
	 * Called after _defineRelationships to initialize and apply defaults to the relationships property
	 * Must be idempotent as it may be applied multiple times up the inheritence chain
	 */
	static protected function _initRelationships()
	{
		$className = get_called_class();
		
		// apply defaults to relationship definitions
		if(!empty(static::$_classRelationships[$className]))
		{
			$relationships = array();
			
			foreach(static::$_classRelationships[$className] AS $relationship => $options)
			{
				if(!$options)
				{
					continue;
				}

				// store
				$relationships[$relationship] = static::_initRelationship($relationship, $options);
			}
			
			static::$_classRelationships[$className] = $relationships;
		}
	}
	
	
	static protected function _initRelationship($relationship, $options)
	{
		// sanity checks
		$className = get_called_class();
		
		if(is_string($options))
		{
			$options = array(
				'type' => 'one-one'
				,'class' => $options
			);
		}
		
		if(!is_string($relationship) || !is_array($options))
		{
			die('Relationship must be specified as a name => options pair');
		}
		
		// apply defaults
		if(empty($options['type']))
		{
			$options['type'] = 'one-one';
		}
		
		if($options['type'] == 'one-one')
		{
			if(empty($options['local']))
				$options['local'] = $relationship . 'ID';
				
			if(empty($options['foreign']))
				$options['foreign'] = 'ID';				
		}
		elseif($options['type'] == 'one-many')
		{
			if(empty($options['local']))
				$options['local'] = 'ID';
					
			if(empty($options['foreign']))
				$options['foreign'] = static::$rootClass . 'ID';
				
			if(!isset($options['indexField']))
				$options['indexField'] = false;
				
			if(!isset($options['conditions']))
				$options['conditions'] = array();
			elseif(is_string($options['conditions']))
				$options['conditions'] = array($options['conditions']);
				
			if(!isset($options['order']))
				$options['order'] = false;
		}
		elseif($options['type'] == 'context-children')
		{
			if(empty($options['local']))
				$options['local'] = 'ID';	
					
			if(empty($options['contextClass']))
				$options['contextClass'] = get_called_class();
				
			if(!isset($options['indexField']))
				$options['indexField'] = false;
				
			if(!isset($options['conditions']))
				$options['conditions'] = array();
				
			if(!isset($options['order']))
				$options['order'] = false;
		}
		elseif($options['type'] == 'context-child')
		{
			if(empty($options['local']))
				$options['local'] = 'ID';	
					
			if(empty($options['contextClass']))
				$options['contextClass'] = get_called_class();
				
			if(!isset($options['indexField']))
				$options['indexField'] = false;
				
			if(!isset($options['conditions']))
				$options['conditions'] = array();
				
			if(!isset($options['order']))
				$options['order'] = array('ID' => 'DESC');
		}
		elseif($options['type'] == 'context-parent')
		{
			if(empty($options['local']))
				$options['local'] = 'ContextID';	
					
			if(empty($options['foreign']))
				$options['foreign'] = 'ID';

			if(empty($options['classField']))
				$options['classField'] = 'ContextClass';

			if(empty($options['allowedClasses']))
				$options['allowedClasses'] = static::$contextClasses;
		}
		elseif($options['type'] == 'handle')
		{
			if(empty($options['local']))
				$options['local'] = 'Handle';	

			if(empty($options['class']))
				$options['class'] = 'GlobalHandle';

		}
		elseif($options['type'] == 'many-many')
		{
			if(empty($options['class']))
				die('required many-many option "class" missing');
		
			if(empty($options['linkClass']))
				die('required many-many option "linkClass" missing');
				
			if(empty($options['linkLocal']))
				$options['linkLocal'] = static::$rootClass . 'ID';
		
			if(empty($options['linkForeign']))
				$options['linkForeign'] = $options['class']::$rootClass . 'ID';
		
			if(empty($options['local']))
				$options['local'] = 'ID';	

			if(empty($options['foreign']))
				$options['foreign'] = 'ID';	

			if(!isset($options['indexField']))
				$options['indexField'] = false;
				
			if(!isset($options['conditions']))
				$options['conditions'] = array();
				
			if(!isset($options['order']))
				$options['order'] = false;
		}
				
		return $options;	
	}


	/**
	 * Returns class name for instantiating given record
	 * @param array $record record
	 * @return string class name
	 */
	static protected function _getRecordClass($record)
	{
		$static = get_called_class();
		
		if(!static::_fieldExists('Class'))
		{
			return $static;
		}
		
		$columnName = static::_cn('Class');
		
		if(!empty($record[$columnName]) && is_subclass_of($record[$columnName], $static))
		{
			return $record[$columnName];
		}
		else
		{		
			return $static;
		} 
	}
	
	static public function _fieldExists($field)
	{
		return array_key_exists($field, static::$_classFields[get_called_class()]);
	}

	static public function _relationshipExists($relationship)
	{
		return array_key_exists($relationship, static::$_classRelationships[get_called_class()]);
	}
	
	
	static public function getClassFields()
	{
		return static::$_classFields[get_called_class()];
	}
	
	static public function getFieldOptions($field, $optionKey = false)
	{
		if($optionKey)
			return static::$_classFields[get_called_class()][$field][$optionKey];
		else
			return static::$_classFields[get_called_class()][$field];
	}

	/**
	 * Returns columnName for given field
	 * @param string $field name of field
	 * @return string column name
	 */
	static public function getColumnName($field)
	{
		if(!static::_fieldExists($field))
		{
			throw new Exception('getColumnName called on nonexisting column: ' . get_called_class().'->'.$field);
		}
		
		return static::$_classFields[get_called_class()][$field]['columnName'];
	}
	
	/**
	 * Shorthand alias for _getColumnName
	 * @param string $field name of field
	 * @return string column name
	 */
	static protected function _cn($field) { return static::getColumnName($field); }

	
	/**
	 * Retrieves given field's value
	 * @param string $field Name of field
	 * @return mixed value
	 */
	protected function _getFieldValue($field, $useDefault = true)
	{
		$fieldOptions = static::$_classFields[get_called_class()][$field];
	
	
		if(isset($this->_record[$fieldOptions['columnName']]))
		{
			$value = $this->_record[$fieldOptions['columnName']];
			
			// apply type-dependent transformations
			switch($fieldOptions['type'])
			{
				case 'password':
				{
					return $value;
				}
				
				case 'timestamp':
				{
					if(!isset($this->_convertedValues[$field]))
					{
						if($value && $value != '0000-00-00 00:00:00')
							$this->_convertedValues[$field] = strtotime($value);
						else
							$this->_convertedValues[$field] = null;
					}
					
					return $this->_convertedValues[$field];
				}
				case 'serialized':
				{
					if(!isset($this->_convertedValues[$field]))
					{
						$this->_convertedValues[$field] = is_string($value) ? unserialize($value) : $value;
					}
					
					return $this->_convertedValues[$field];
				}
				case 'set':
				case 'list':
				{
					if(!isset($this->_convertedValues[$field]))
					{
						$delim = empty($fieldOptions['delimiter']) ? ',' : $fieldOptions['delimiter'];
						$this->_convertedValues[$field] = array_filter(preg_split('/\s*'.$delim.'\s*/', $value));
					}
					
					return $this->_convertedValues[$field];
				}
				
				case 'boolean':
				{
					if(!isset($this->_convertedValues[$field]))
					{
						$this->_convertedValues[$field] = (boolean)$value;
					}
					
					return $this->_convertedValues[$field];
				}
				
				default:
				{
					return $value;
				}
			}
		}
		elseif($useDefault && isset($fieldOptions['default']))
		{
			// return default
			return $fieldOptions['default'];
		}
		else
		{
			switch($fieldOptions['type'])
			{
				case 'set':
				case 'list':
				{
					return array();
				}
				default:
				{
					return null;
				}
			}
		}
	}
	
	/**
	 * Sets given field's value
	 * @param string $field Name of field
	 * @param mixed $value New value
	 * @return mixed value
	 */
	protected function _setFieldValue($field, $value)
	{
		// ignore overwriting meta fields
		if(in_array($field, array('Created','CreatorID')) && $this->_getFieldValue($field, false) !== null)
		{
			return false;
		}
		
		if(!static::_fieldExists($field))
		{
			// set relationship
			if(static::_relationshipExists($field))
			{
				return $this->_setRelationshipValue($field, $value);
			}
			else
			{
				return false;
			}
		}
		$fieldOptions = static::$_classFields[get_called_class()][$field];

		// no overriding autoincrements
		if($fieldOptions['autoincrement'])
		{
			return false;
		}

		// pre-process value
		$forceDirty = false;
		switch($fieldOptions['type'])
		{
			case 'clob':
			case 'string':
			{
				if(!$fieldOptions['notnull'] && $fieldOptions['blankisnull'] && ($value === '' || $value === NULL))
				{
					$value = null;
					break;
				}
			
				// normalize encoding to ASCII
				$value = @mb_convert_encoding($value, DB::$encoding, 'auto');
				
				// remove any remaining non-printable characters
				$value = preg_replace('/[^[:print:][:space:]]/', '', $value);
				
				break;
			}
			
			case 'boolean':
			{
				$value = (boolean)$value;
			}
			
			case 'decimal':
			{
				$value = preg_replace('/[^-\d.]/','', $value);
				break;
			}
				
			case 'int':
			case 'uint':
			case 'integer':
			{
				$value = preg_replace('/\D/','', $value);
				
				if(!$fieldOptions['notnull'] && $value === '')
				{
					$value = NULL;
				}
				
				break;
			}
			
			case 'timestamp':
			{
				if(is_numeric($value))
				{
					$value = date('Y-m-d H:i:s', $value);
				}
				elseif(is_string($value))
				{
					// trim any extra crap, or leave as-is if it doesn't fit the pattern
					$value = preg_replace('/^(\d{4})\D?(\d{2})\D?(\d{2})T?(\d{2})\D?(\d{2})\D?(\d{2})/', '$1-$2-$3 $4:$5:$6', $value);
				}
				break;
			}
			
			case 'date':
			{	
				if(is_numeric($value))
				{
					$value = date('Y-m-d', $value);
				}
				elseif(is_string($value))
				{
					// trim time and any extra crap, or leave as-is if it doesn't fit the pattern
					$value = preg_replace('/^(\d{4})\D?(\d{2})\D?(\d{2}).*/', '$1-$2-$3', $value);
				}
				elseif(is_array($value) && count(array_filter($value)))
				{
					// collapse array date to string
					$value = sprintf(
						'%04u-%02u-%02u'
						,is_numeric($value['yyyy']) ? $value['yyyy'] : 0
						,is_numeric($value['mm']) ? $value['mm'] : 0
						,is_numeric($value['dd']) ? $value['dd'] : 0
					);
				}
				else
				{
					$value = null;
				}
				break;
			}
			
			// these types are converted to strings from another PHP type on save
			case 'serialized':
			{
				$this->_convertedValues[$field] = $value;
				$value = serialize($value);
				break;
			}
			case 'set':
			case 'list':
			{
				if(!is_array($value))
				{
					$delim = empty($fieldOptions['delimiter']) ? ',' : $fieldOptions['delimiter'];
					$value = array_filter(preg_split('/\s*'.$delim.'\s*/', $value));
				}
			
				$this->_convertedValues[$field] = $value;
				$forceDirty = true;
				break;
			}

		}
		
		if($forceDirty || ($this->_record[$field] !== $value))
		{
			//if($this->_record['Class'] == 'CMS_Page') MICS::dump($value, "$field is dirty");
			$columnName = static::_cn($field);
			if(isset($this->_record[$columnName]))
			{
				$this->_originalValues[$field] = $this->_record[$columnName];
			}
			$this->_record[$columnName] = $value;
			$this->_isDirty = true;
			
			// unset invalidated relationships
			if(!empty($fieldOptions['relationships']))
			{
				foreach($fieldOptions['relationships'] AS $relationship => $isCached)
				{
					if($isCached)
					{
						unset($this->_relatedObjects[$relationship]);
					}
				}
			}
			
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Retrieves given relationships' value
	 * @param string $relationship Name of relationship
	 * @return mixed value
	 */
	protected function _getRelationshipValue($relationship)
	{
		if(!isset($this->_relatedObjects[$relationship]))
		{
			$rel = static::$_classRelationships[get_called_class()][$relationship];

			if($rel['type'] == 'one-one')
			{
				if($value = $this->_getFieldValue($rel['local']))
				{
					$this->_relatedObjects[$relationship] = $rel['class']::getByField($rel['foreign'], $value);
				
					// hook relationship for invalidation
					static::$_classFields[get_called_class()][$rel['local']]['relationships'][$relationship] = true;
				}
				else
				{
					$this->_relatedObjects[$relationship] = null;
				}
			}
			elseif($rel['type'] == 'one-many')
			{
				if(!empty($rel['indexField']) && !$rel['class']::_fieldExists($rel['indexField']))
				{
					$rel['indexField'] = false;
				}
				
				$this->_relatedObjects[$relationship] = $rel['class']::getAllByWhere(
					array_merge($rel['conditions'], array(
						$rel['foreign'] => $this->_getFieldValue($rel['local'])
					))
					, array(
						'indexField' => $rel['indexField']
						,'order' => $rel['order']
						,'conditions' => $rel['conditions']
					)
				);
				
				
				// hook relationship for invalidation
				static::$_classFields[get_called_class()][$rel['local']]['relationships'][$relationship] = true;
			}
			elseif($rel['type'] == 'context-children')
			{
				if(!empty($rel['indexField']) && !$rel['class']::_fieldExists($rel['indexField']))
				{
					$rel['indexField'] = false;
				}
				
				$conditions = array_merge($rel['conditions'], array(
					'ContextClass' => $rel['contextClass']
					,'ContextID' => $this->_getFieldValue($rel['local'])
				));
			
				$this->_relatedObjects[$relationship] = $rel['class']::getAllByWhere(
					$conditions
					, array(
						'indexField' => $rel['indexField']
						,'order' => $rel['order']
					)
				);
				
				// hook relationship for invalidation
				static::$_classFields[get_called_class()][$rel['local']]['relationships'][$relationship] = true;
			}
			elseif($rel['type'] == 'context-child')
			{
				$conditions = array_merge($rel['conditions'], array(
					'ContextClass' => $rel['contextClass']
					,'ContextID' => $this->_getFieldValue($rel['local'])
				));
			
				$this->_relatedObjects[$relationship] = $rel['class']::getByWhere(
					$conditions
					, array(
						'order' => $rel['order']
					)
				);
			}
			elseif($rel['type'] == 'context-parent')
			{
				$className = $this->_getFieldValue($rel['classField']);
				$this->_relatedObjects[$relationship] = $className ? $className::getByID($this->_getFieldValue($rel['local'])) : null;
				
				// hook both relationships for invalidation
				static::$_classFields[get_called_class()][$rel['classField']]['relationships'][$relationship] = true;
				static::$_classFields[get_called_class()][$rel['local']]['relationships'][$relationship] = true;
			}
			elseif($rel['type'] == 'handle')
			{
				if($handle = $this->_getFieldValue($rel['local']))
				{
					$this->_relatedObjects[$relationship] = $rel['class']::getByHandle($handle);
				
					// hook relationship for invalidation
					static::$_classFields[get_called_class()][$rel['local']]['relationships'][$relationship] = true;
				}
				else
				{
					$this->_relatedObjects[$relationship] = null;
				}
			}
			elseif($rel['type'] == 'many-many')
			{				
				if(!empty($rel['indexField']) && !$rel['class']::_fieldExists($rel['indexField']))
				{
					$rel['indexField'] = false;
				}
				
				// TODO: support indexField, conditions, and order
				
				$this->_relatedObjects[$relationship] = $rel['class']::getAllByQuery(
					'SELECT Related.* FROM `%s` Link JOIN `%s` Related ON (Related.`%s` = Link.%s) WHERE Link.`%s` = %u AND %s'
					, array(
						$rel['linkClass']::$tableName
						,$rel['class']::$tableName
						,$rel['foreign']
						,$rel['linkForeign']
						,$rel['linkLocal']
						,$this->_getFieldValue($rel['local'])
						,$rel['conditions'] ? join(' AND ', $rel['conditions']) : '1'
					)
				);
				
				// hook relationship for invalidation
				static::$_classFields[get_called_class()][$rel['local']]['relationships'][$relationship] = true;
			}
		}
		
		return $this->_relatedObjects[$relationship];
	}
	
	
	protected function _setRelationshipValue($relationship, $value)
	{
		$rel = static::$_classRelationships[get_called_class()][$relationship];
				
		if($rel['type'] ==  'one-one')
		{
			if($value !== null && !is_a($value,'ActiveRecord'))
			{
				return false;
			}
			
			if($rel['local'] != 'ID')
			{
				$this->_setFieldValue($rel['local'], $value ? $value->getValue($rel['foreign']) : null);
			}
		}
		elseif($rel['type'] ==  'context-parent')
		{
			if($value !== null && !is_a($value,'ActiveRecord'))
			{
				return false;
			}

			if(empty($value))
			{
				// set Class and ID
				$this->_setFieldValue($rel['classField'], null);
				$this->_setFieldValue($rel['local'], null);
			}
			else
			{
				$contextClass = get_class($value);
				
				// set Class and ID
				$this->_setFieldValue($rel['classField'], $contextClass::$rootClass);
				$this->_setFieldValue($rel['local'], $value->__get($rel['foreign']));
			}

		}
		elseif($rel['type'] == 'one-many' && is_array($value))
		{
			$set = array();
			
			foreach($value AS $related)
			{
				if(!$related || !is_a($related,'ActiveRecord')) continue;
				
				$related->_setFieldValue($rel['foreign'], $this->_getFieldValue($rel['local']));
				$set[] = $related;
			}
			
			// so any invalid values are removed
			$value = $set;
		}
		elseif($rel['type'] ==  'handle')
		{
			if($value !== null && !is_a($value,'ActiveRecord'))
			{
				return false;
			}
			
			$this->_setFieldValue($rel['local'], $value ? $value->Handle : null);
		}
		else
		{
			return false;
		}

		$this->_relatedObjects[$relationship] = $value;
		$this->_isDirty = true;
	}
	
	public function appendRelated($relationship, $values)
	{
		$rel = static::$_classRelationships[get_called_class()][$relationship];
		
		if($rel['type'] != 'one-many')
		{
			throw new Exception('Can only append to one-many relationship');
		}
		
		if(!is_array($values))
		{
			$values = array($values);
		}
		
		foreach($values AS $relatedObject)
		{
			if(!$relatedObject || !is_a($relatedObject,'ActiveRecord')) continue;
			
			$relatedObject->_setFieldValue($rel['foreign'], $this->_getFieldValue($rel['local']));
			$this->_relatedObjects[$relationship][] = $relatedObject;
			$this->_isDirty = true;
		}
	}

	protected function _prepareRecordValues()
	{
		$record = array();

		foreach(static::$_classFields[get_called_class()] AS $field => $options)
		{
			$columnName = static::_cn($field);
			
			if(array_key_exists($columnName, $this->_record))
			{
				$value = $this->_record[$columnName];
				
				if(!$value && !empty($options['blankisnull']))
				{
					$value = null;
				}
			}
			elseif(isset($options['default']))
			{
				$value = $options['default'];
			}
			else
			{
				continue;
			}

			if( ($options['type'] == 'date') && ($value == '0000-00-00') && !empty($options['blankisnull']))
			{
				$value = null;
			}
			if( ($options['type'] == 'timestamp'))
			{
				if(is_numeric($value))
				{
					$value = date('Y-m-d H:i:s', $value);
				}
				elseif($value == '0000-00-00 00:00:00')
				{
					$value = null;
				}
			}

			if( ($options['type'] == 'serialized') && !is_string($value))
			{
				$value = serialize($value);
			}
			
			if( ($options['type'] == 'list') && is_array($value))
			{
				$delim = empty($options['delimiter']) ? ',' : $options['delimiter'];
				$value = implode($delim, $value);
			}
			
			$record[$field] = $value;
		}

		return $record;
	}
	
	static protected function _mapValuesToSet($recordValues)
	{
		$set = array();

		foreach($recordValues AS $field => $value)
		{
			$fieldConfig = static::$_classFields[get_called_class()][$field];
			
			if($value === null)
				$set[] = sprintf('`%s` = NULL', $fieldConfig['columnName']);
			elseif($fieldConfig['type'] == 'timestamp' && $value == 'CURRENT_TIMESTAMP')
				$set[] = sprintf('`%s` = CURRENT_TIMESTAMP', $fieldConfig['columnName']);
			elseif($fieldConfig['type'] == 'set' && is_array($value))
				$set[] = sprintf('`%s` = "%s"', $fieldConfig['columnName'], DB::escape(join(',', $value)));
			elseif($fieldConfig['type'] == 'boolean')
				$set[] = sprintf('`%s` = %u', $fieldConfig['columnName'], $value ? 1 : 0);
			else
				$set[] = sprintf('`%s` = "%s"', $fieldConfig['columnName'], DB::escape($value));
		}

		return $set;
	}
	
	static protected function _mapFieldOrder($order)
	{
		if(is_string($order))
		{
			return array($order);
		}
		elseif(is_array($order))
		{
			$r = array();
			
			foreach($order AS $key => $value)
			{
				if(is_string($key))
				{
					$columnName = static::_cn($key);
					$direction = strtoupper($value)=='DESC' ? 'DESC' : 'ASC';
				}
				else
				{
					$columnName = static::_cn($value);
					$direction = 'ASC';
				}
				
				$r[] = sprintf('`%s` %s', $columnName, $direction);
			}
			
			return $r;
		}
	}
	
	static public function mapConditions($conditions)
	{
		return static::_mapConditions($conditions);
	}
	
	static protected function _mapConditions($conditions)
	{
		foreach($conditions AS $field => &$condition)
		{
		
			if(is_string($field))
			{
				$fieldOptions = static::$_classFields[get_called_class()][$field];
			
				if($condition === null || ($condition == '' && $fieldOptions['blankisnull']))
				{
					$condition = sprintf('`%s` IS NULL', static::_cn($field));
				}
				else
				{
					$condition = sprintf('`%s` = "%s"', static::_cn($field), DB::escape($condition));
				}
			}
		}
		
		return $conditions;
	}
	
	
	public function getNoun($count = 1)
	{
		return ($count == 1) ? static::$singularNoun : static::$pluralNoun;
	}
	
	public function getRootClass()
	{
		return static::$rootClass;
	}
}
