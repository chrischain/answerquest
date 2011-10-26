Ext.define('AnswerQuest.views.Viewport', {
    extend: 'Ext.TabPanel',

    requires: 'Ext.MessageBox',
	config: {
		title: 'Answer Quest',
		
		tabBarPosition: 'bottom',
		fullscreen: true,
	    items: [
			{
				xtype: 'toolbar',
				docked: 'top',
				centered: true,
				title: '<img src="img/title-logo.png" alt="AnswerQuest">'
			},
	        {
				xtype: 'questionwrapper',
				id: 'questionwrapper',
				iconCls: 'bulb'
	        },
			{
				xtype: 'playerlist',
				id: 'playerlist',
				iconCls: 'team'
	        }
	    ]
	},
	
	// initialize: function() {
	// 	alert('doing something here');
	// },

    speak: function() {
        Ext.Msg.alert()
    }
});
