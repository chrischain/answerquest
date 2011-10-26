Ext.onReady(function(){
 Ext.application({
    name: 'HelloExt',
    launch: function() {
        Ext.create('Ext.container.Viewport', {
            layout: 'fit',
            items: [
                {
                    xtype: 'aq-main',
                    id: 'aq-main'
                }
            ]
        });
    }
 });

});

Ext.define('AQ.Main', {
    extend: 'Ext.Panel',
    alias: 'widget.aq-main',
    layout: 'card',
    items: [
        {xtype: 'aq-startpage'},
        {xtype: 'aq-editquest'   }
    ],
    onRender: function() {
        this.callParent(arguments); // call the superclass onRender method

        // perform additional rendering tasks here.
    }
});


Ext.define('AQ.EditQuest', {
    extend: 'Ext.Panel',
    alias: 'widget.aq-editquest',
    layout: 'vbox',
    html: 'test new page'

});




















