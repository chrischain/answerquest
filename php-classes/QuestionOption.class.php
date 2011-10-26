<?php

class QuestionOption extends ActiveRecord
{
	// ActiveRecord configuration
	static public $tableName = 'question_options';
	static public $singularNoun = 'question option';
	static public $pluralNoun = 'question options';
	
	// the lowest-level class in your table requires these lines,
	// they can be manipulated via config files to plug in same-table subclasses
	static public $rootClass = __CLASS__;
	static public $defaultClass = __CLASS__;
	static public $subClasses = array(__CLASS__);

	static public $fields = array(
		'QuestionID' => array(
			'type' => 'uint'
			,'index' => true
		)
		,'Text'
	);
	
	
	static public $relationships = array(
		'Question' => array(
			'type' => 'one-one'
			,'class' => 'Question'
		)
	);
	
}