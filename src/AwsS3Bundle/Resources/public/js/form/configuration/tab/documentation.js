'use strict';

define([
    'pim/form',
    'klizerawss3/template/configuration/tab/documentation'
], function (
    BaseForm,
    template
) {
    return BaseForm.extend({
        className: 'tab-content documentation',
        template: _.template(template),
        render() {
            this.$el.html(this.template());
            return this;
        }
    });
});

