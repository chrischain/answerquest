Ext.define('AnswerQuest.views.ResultsPanel', {
    extend: 'Ext.Component'
    ,cls: 'results-panel'
	,xtype: 'resultspanel'

	,config: {
		scrollable: true
		
		,tpl: [
			'<div class="question-text"><header>Thank you!<br>{totalVotes} Votes</header></div>'
			,'<div class="chart" style="padding:1em">'
				,'<tpl for="options">'
					,'<h1>{Count}/{parent.totalVotes} - {Text}</h1>'
					,'<div class="bar-ct"><div class="bar" style="width:{[values.Count/parent.totalVotes*100}%">&nbsp;</div></div>'
				,'</tpl>'
			,'</div>'
		]
	}
	
/*
	initialize: function() {
	}
*/

});
