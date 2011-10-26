<?php

class QuestionAnswer extends ActiveRecord
{
	// ActiveRecord configuration
	static public $tableName = 'question_answers';
	static public $singularNoun = 'question answer';
	static public $pluralNoun = 'question answers';
	
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
		,'OptionID' => array(
			'type' => 'uint'
			,'index' => true
		)
	);
	
	
	static public $relationships = array(
		'Question' => array(
			'type' => 'one-one'
			,'class' => 'Question'
		)
		,'Option' => array(
			'type' => 'one-one'
			,'class' => 'Option'
		)
	);
	
}