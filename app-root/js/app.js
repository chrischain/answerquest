Ext.application({
    name: 'AnswerQuest',

	icon: "icon.png", // TODO: insert touch icon
	glossOnIcon: false, //TODO: check gloss
	version: "0.1",
	fullscreen: true,
	
	controllers: [
		'QuestionController',
		'PlayerController'
	],
	
	models: [
		'Contact'
	],
	
	config: {
		
	},

    launch: function() {
    	AnswerQuest.app = this;
		this.viewport = new AnswerQuest.views.Viewport();
		
		// load socket.io	
		//Ext.Loader.loadScriptFile('http://answerquest.mics.me:8000/socket.io/socket.io.js', this.onSocketIOReady, Ext.emptyFn, this);
    }

	/* socket.IO support */
	,onSocketIOReady: function() {
		var me = this;
	
		me.io = io.connect('http://answerquest.mics.me:8000');
		me.io.on('connect', function() {
			me.fireEvent('socketConnect', me.io);
		});
	}
	
	,isSocketConnected: function() {
		return this.io && this.io.socket && this.io.socket.connected;
	}
	
});