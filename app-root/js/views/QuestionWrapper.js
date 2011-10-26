Ext.define('AnswerQuest.views.QuestionWrapper', {
    extend: 'Ext.Panel',
	xtype: 'questionwrapper',

    // requires: 'Ext.MessageBox',
	config: {
		title: 'Questions',
		iconCls: 'home',
		
		layout: {
			type: 'card'
		},
	    items: [{
			xtype: 'questionpanel'
		}]
	},
	
	// initialize: function() {
	// 	alert('doing something here');
	// },

    speak: function() {
        Ext.Msg.alert();
    }
});
