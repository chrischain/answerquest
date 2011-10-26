<?php

class Question extends ActiveRecord
{
	// ActiveRecord configuration
	static public $tableName = 'questions';
	static public $singularNoun = 'question';
	static public $pluralNoun = 'questions';
	
	// the lowest-level class in your table requires these lines,
	// they can be manipulated via config files to plug in same-table subclasses
	static public $rootClass = __CLASS__;
	static public $defaultClass = __CLASS__;
	static public $subClasses = array(__CLASS__);

	static public $fields = array(
		'SeriesID' => array(
			'type' => 'uint'
			,'index' => true
		)
		,'Text'
	);
	
	
	static public $relationships = array(
		'Options' => array(
			'type' => 'one-many'
			,'class' => 'QuestionOption'
		)
	);
	
	public function getData()
	{
		return array(
			'ID' => $this->ID
			,'Text' => $this->Text
			,'Options' => JSON::translateObjects($this->Options)
		);
	}
}