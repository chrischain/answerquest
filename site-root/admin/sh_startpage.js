
// The data store containing the list of states
var quests = Ext.create('Ext.data.Store', {
    fields: ['url', 'name', 'description'],
    data : [
        {"url":"http://www.quest.com", "name": "Quest1", "description": "This is an example quest"},
        {"url":"http://www.quest.com/2/", "name": "Quest2", "description": "This is an example quest"}
    ]
});


Ext.define('AQ.StartPage', {
    extend: 'Ext.form.Panel',
    alias: 'widget.aq-startpage',
    layout: {
        type: 'vbox',
        align: 'center',
        pack: 'center'
    },
    newQuest: function(name, desc) {
        var cmp = Ext.ComponentManager.get('aq-editquest');
    },
    items: [{
        xtype: 'fieldset',
        width: 350,
        title: 'New Quest',
        items: [
            {
                xtype: 'textfield',
                name: 'new_name',
                fieldLabel: 'Name',
                allowBlank: false
            },
            {
                xtype: 'textfield',
                name: 'description',
                fieldLabel: 'Description',
                allowBlank: false
            },
            {
                xtype: 'button',
                text: 'New Quest',
                name: 'newquest',
                //formBind: true,
                handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: 'new_quest.php',
                        success: function(form, action) {
                            var data = this.getForm().values();
                            this.newQuest(name, description);
                        },
                        failure: function(form, action) {
                            var cmp = Ext.ComponentManager.get("aq-main");
                            cmp.getLayout().setActiveItem('aq-editquest');
                        }
                    });
                }
            }
        ]
    }, {
        xtype: 'fieldset',
        title: 'Existing Quest',
        width: 350,
        items: [{
            xtype: 'combobox',
            store: quests,
            queryMode: 'local',
            valueField: 'name',
            autoSelect: true,
            name: 'quest_name',
            forceSelection: true,
            listConfig: {
                loadingText: 'Searching...',
                emptyText: 'No quests found.',
                itemTpl: '{name} - {desc}'
            }
        },
        {
            xtype: 'button',
            text: 'Edit Quest',
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

