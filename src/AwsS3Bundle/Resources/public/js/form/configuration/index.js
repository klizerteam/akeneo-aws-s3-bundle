define(['pim/form'], function (BaseForm) {
    return BaseForm.extend({
        initialize() {
            this.setTitle('Klizer AWS Configuration');
            BaseForm.prototype.initialize.apply(this, arguments);
        },
        render() {
            this.$el.html('<h1>Klizer AWS Settings Page</h1>');
            return this;
        }
    });
});

