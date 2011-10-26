Ext.define('AnswerQuest.views.QuestionPanel', {
    extend: 'Ext.Panel'
    ,cls: 'question-panel'
	,xtype: 'questionpanel'
	
	,config: {
		scrollable: true

		,items: [{
			xtype: 'component'
			,docked: 'top'
			,cls: 'question-text'
			,tpl: '<header>{Text}</header>'
			,data: AQ_Series.Questions[0]
		},{
			xtype: 'toolbar'
			,docked: 'bottom'
			,ui: 'light'
			,items: { text: 'Answer!', flex: 1, ui: 'forward' }
		},{
			xtype: 'component'
			,id: 'optionslist'
			,tpl: [
				'<ul class="list">'
					,'<tpl for="."><li id="option-{ID}">{Text}</li></tpl>'
				,'</ul>'
			]

			,data: AQ_Series.Questions[0].Options

			,listeners: {
				painted: function() {
					if(!this.eventsInstalled)
					{
						this.el.on('touchstart', function(ev, t) {
							Ext.fly(t).addCls('x-pressed');
						}, {delegate: 'li'});

						this.el.on('touchend', function(ev, t) {
							Ext.fly(t).removeCls('x-pressed');
						}, {delegate: 'li'});

						this.el.on('tap', function(ev, t) {
							Ext.fly(t).radioCls('x-selected');
						}, {delegate: 'li'});
						
						this.eventsInstalled = true;
					}
				}
			}
		}]
	}
	
/*
	initialize: function() {
	}
*/

});
