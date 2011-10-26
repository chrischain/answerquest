/*
 * DATA STORE
 */
Ext.create('Ext.data.Store', {
    storeId: 'aqStore',
    fields: ['name', 'description', 'min_responses', 'max_responses', 'active'],
    data: {
        'items': [{
            'name': 'LikeAQ',
            "description": "popularity poll",
            'max_responses': '',
            'min_responses': 5,
            'active': 1
        }, {
            'name': 'Austin Pub Crawl',
            "description": "pub crawl 6th street",
            'max_responses': '',
            'min_responses': '',
            'active': 0
        }]
    },
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            root: 'items'
        }
    }
});

Ext.create('Ext.data.Store', {
    storeId: 'typeStore',
    fields: ['type'],
    data: {
        'items': [{
            "type": "yes/no"
        }, {
            "type": "multiple choice"
        }, {
            "type": "scavenger hunt"
        }, {
            "type": "series"
        }]
    },
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            root: 'items'
        }
    }
});

/*
 * GRID CONFIG
 */
Ext.define('AQ.questMgr', {
    extend: 'Ext.form.Panel',
    alias: 'widget.aq-editquest',
    items: {
        autoWidth: true,
        store: Ext.data.StoreManager.lookup('aqStore'),
        xtype: 'grid',
        columns: [{
            header: 'Name',
            dataIndex: 'name',
            field: {
                xtype: 'textfield',
                allowBlank: false
            },
            width: 150
        }, {
            header: 'Description',
            dataIndex: 'description',
            field: {
                xtype: 'textfield'
            },
            flex: 1
        }, {
            header: 'Min Responses',
            dataIndex: 'min_responses',
            field: {
                xtype: 'textfield'
            },
            align: 'center'
        }, {
            header: 'Max Responses',
            dataIndex: 'max_responses',
            field: {
                xtype: 'textfield'
            },
            align: 'center'
        }, {
            header: 'Active?',
            dataIndex: 'active',
            xtype: 'booleancolumn',
            align: 'center',
            editor: {
                xtype: 'checkbox'
            }
        }, {
            header: 'Actions',
            xtype: 'actioncolumn',
            align: 'center',
            handler: function(){
                //put QR generator here
            },
            items: [{
                icon: 'http://cdn.sencha.io/ext-4.0.7-gpl/examples/shared/icons/fam/image_add.png',
                tooltip: 'Generate QR Code',
            }]
        }],
        plugins: [Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1
        })],
        height: 200,
        renderTo: Ext.getBody()
    },
    title: 'Manage Quests'
});

