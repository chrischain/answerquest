
function showGenQR() {
    var win = new Ext.Window({
        items: [
            {
                xtype: 'panel',
                html: '<div>Image goes here.</div>'
            },
            {
                xtype: 'form',
                items: [
                    {
                        xtype: 'textfield',
                        name: 'url',
                        fieldLabel: 'Url',
                        allowBlank: false
                    },
                    {
                        xtype: 'button',
                        text: 'Generate QR',
                        handler: function() {
                            var form = this.getForm();
                            var values = form.getValues();
                            console.log('values: %o', values);
                        }
                    }
                ]
            }
        ]
    });
    win.show();
}
