define(['pim/menu/item'], function (MenuItem) {
    return MenuItem.extend({
        configure: function () {
            this.label = 'AWS Connector';
            this.icon = 'bundles/klizeraws/images/icon.svg'; // Ensure the path is correct
            this.url = '#/klizer/aws';
            return MenuItem.prototype.configure.apply(this, arguments);
        },
    });
});

