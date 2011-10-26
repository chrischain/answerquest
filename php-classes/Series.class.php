<?php

class Series extends ActiveRecord
{
	// ActiveRecord configuration
	static public $tableName = 'series';
	static public $singularNoun = 'series';
	static public $pluralNoun = 'serieses';
	
	// the lowest-level class in your table requires these lines,
	// they can be manipulated via config files to plug in same-table subclasses
	static public $rootClass = __CLASS__;
	static public $defaultClass = __CLASS__;
	static public $subClasses = array(__CLASS__);

	static public $fields = array(
		'Handle' => array(
			'unique' => true
		)
		,'Status' => array(
			'type' => 'enum'
			,'values' => array('Pending','Live','Deleted')
			,'default' => 'Live'
		)
		,'Name'
	);
	
	
	static public $relationships = array(
		'Questions' => array(
			'type' => 'one-many'
			,'class' => 'Question'
		)
	);
	
	public function getData()
	{
		return array(
			'Name' => $this->Name
			,'Questions' => JSON::translateObjects($this->Questions)
		);
	}
	
	
	static public function getByHandle($handle)
	{
		return static::getByField('Handle', $handle);
	}
	
}