Ext.define('AnswerQuest.controller.QuestionController', {
    extend: 'Ext.app.Controller',

    refs: [{
        ref: 'questionwrapper',
        selector: '#questionwrapper'
    }],

    init: function() {
        // console.log('the first controller is live');

		// this.store = Ext.create('Ext.data.Store', {
		// 	fields: ['question']
		// });

        this.control({
            '#questionwrapper': {
                activate: this.initQuestions
            },

			'button': {
				tap: this.submitAnswer
			}
        });
    },

    initQuestions: function() {
		//console.log('initializing question wrapper, feetching questions', this.getQuestionwrapper());
		
		// Get a question
		// this.store.load();
		this.getNextQuestion();
		// render it
		
    },

	submitAnswer: function() {
		//store the question somewhere
		//console.log('submitting answer to server');
		
		var selected = Ext.getCmp('optionslist').el.down('.x-selected');
		
		if(!selected)
		{
			alert('Please select an answer!');
			return;
		}
		
		this.getQuestionwrapper().el.mask('Submitting&hellip;');
		Ext.Ajax.request({
		    url: '/answer',
			method: 'post',
		    params: {
		        qid: AQ_Series.Questions[0].ID
				,oid: selected.id.substr(7)
		    },
		    scope: this
		    ,success: function(response){
		        var r = Ext.decode(response.responseText);
		       	var total = 0;
		       	
		       	Ext.each(Ext.pluck(r.data, 'Count'), function(count) {
		       		total += parseInt(count);
		       	});
		        this.getQuestionwrapper().el.unmask();
				//console.log('returned from server: %o', r);
		        // process server response here
		        this.getQuestionwrapper().setActiveItem({xtype: 'resultspanel', data: {
		        	options: r.data
		        	,totalVotes: total
		        }})
		    }
		});
		
		//callback to the next question
		
	},
	
	getNextQuestion: function(question) {
		// this.getQuestionwrapper().add({
		// 	xtype: 'questionpanel'
		// });
	}
});