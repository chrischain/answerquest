
// The data store containing the list of states
var quests = Ext.create('Ext.data.Store', {
    fields: ['url', 'name', 'desc'],
    data : [
        {"id":"http://www.quest.com", "name":"Quest1", "desc": "This is an example quest"}
    ]
});


Ext.define('AQ.StartPage', {
    extend: 'Ext.form.Panel',
    alias: 'widget.aq-startpage',
    width: 350,
    layout: {
        type: 'vbox',
        align: 'center',
        pack: 'center'
    },
    items: [{
        xtype: 'fieldset',
        title: 'New Quest',
        items: [
            {
                xtype: 'textfield',
                name: 'new_name'
            },
            {
                xtype: 'button',
                text: 'New Quest',
                name: 'newquest',
                formBind: true,
                handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: 'new_quest.php',
                        success: function(form, action) {
                           alert('success');
                        },
                        failure: function(form, action) {

                        }
                    });
                }
            }
        ]
    }, {
        xtype: 'fieldset',
        title: 'Existing Quest',
        items: [{
            xtype: 'combobox',
            store: quests,
            queryMode: 'local',
            displayField: 'name',
            valueField: 'name',
            autoSelect: true,
            name: 'quest_name',
            forceSelection: true
        },
        {
            xtype: 'button', text:
            'Edit Quest',
            name: 'editquest',
            formBind: true,
            handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: 'edit_quest.php',
                        success: function(form, action) {
                           alert('success');
                        },
                        failure: function(form, action) {
                            alert('failure');
                        }
                    });
                }
            }

        ]
    }]
});

