Ext.onReady(function(){
 Ext.application({
    name: 'HelloExt',
    launch: function() {
        Ext.create('Ext.container.Viewport', {
            title: 'Answer Quest',
            layout: 'fit',
            items: [
                {
                    xtype: 'aq-editquest',
                    id: 'aq-editquest'
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
        {xtype: 'aq-startpage', id: 'aq-startpage'},
        {xtype: 'aq-editquest', id: 'aq-editquest'}
    ],
    onRender: function() {
        this.callParent(arguments); // call the superclass onRender method

        // perform additional rendering tasks here.
    }
});























