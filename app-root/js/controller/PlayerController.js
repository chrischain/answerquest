Ext.define('AnswerQuest.controller.PlayerController', {
    extend: 'Ext.app.Controller',

    refs: [{
        ref: 'playerList',
        selector: '#playerlist'
    }],

    init: function() {
        // console.log('the first controller is live');

		// this.store = Ext.create('Ext.data.Store', {
		// 	fields: ['question']
		// });

        this.control({
            '#playerlist': {
                activate: this.loadPlayers
            }
        });
    },

    loadPlayers: function() {
		console.log('fetching a list of players');
		
		
    }

});