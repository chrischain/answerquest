<?php


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(empty($_REQUEST['qid']) || !($Question = Question::getByID($_REQUEST['qid'])))
	{
		RequestHandler::throwError('Question not found');
	}
	if(empty($_REQUEST['oid']) || !($Option = QuestionOption::getByID($_REQUEST['oid'])))
	{
		RequestHandler::throwError('Option not found');
	}
	
	QuestionAnswer::create(array(
		'Question' => $Question
		,'Option' => $Option
	), true);
	
	RequestHandler::$responseMode = 'json';
	RequestHandler::respond('answerReceived', array(
		'success' => true
		,'data' => DB::allRecords(
			'SELECT a.OptionID, o.Text, COUNT(*) AS Count FROM question_answers a JOIN question_options o ON o.ID = a.OptionID WHERE a.QuestionID = %u GROUP BY OptionID'
			,$Question->ID
		)
	));
}