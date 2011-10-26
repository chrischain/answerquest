Ext.define('AnswerQuest.views.PlayerList', {
    extend: 'Ext.Panel'
    ,cls: ''
	,xtype: 'playerlist'
	
	,config: {
		title: 'Players'
		,iconCls: 'user'
		,scrollable: true
		,items: [{
			xtype: 'component'
			,tpl: [
				'<ul class="list">'
					,'<tpl for="."><li class="listitem">{firstName} <strong>{lastName}</strong></li></tpl>'
				,'</ul>'
			]
			,data: [
			       {firstName: 'Tommy',   lastName: 'Maintz'},
			       {firstName: 'Rob',     lastName: 'Dougan'},
			       {firstName: 'Ed',      lastName: 'Spencer'},
			       {firstName: 'Jamie',   lastName: 'Avins'},
			       {firstName: 'Aaron',   lastName: 'Conran'},
			       {firstName: 'Dave',    lastName: 'Kaneda'},
			       {firstName: 'Jacky',   lastName: 'Nguyen'},
			       {firstName: 'Abraham', lastName: 'Elias'},
			       {firstName: 'Jay',     lastName: 'Robinson'},
			       {firstName: 'Nigel',   lastName: 'White'},
			       {firstName: 'Don',     lastName: 'Griffin'},
			       {firstName: 'Nico',    lastName: 'Ferrero'},
			       {firstName: 'Nicolas', lastName: 'Belmonte'},
			       {firstName: 'Jason',   lastName: 'Johnston'}
			   ]
			
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


});

