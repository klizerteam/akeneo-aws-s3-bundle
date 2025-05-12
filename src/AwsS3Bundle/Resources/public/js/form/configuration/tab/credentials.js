'use strict';

define([
    'pim/form',
    'oro/translator',
    'klizerawss3/template/configuration/tab/credentials'
], function (
    BaseForm,
    __,
    template
) {
    return BaseForm.extend({
        isGroup: true,
        label: __('klizer.aws.credentials.tab'),
        code: 'klizer_aws_tab_credentials',
        template: _.template(template),

        configure() {
            this.trigger('tab:register', {
                code: this.code,
                label: this.label
            });

            return BaseForm.prototype.configure.apply(this, arguments);
        },

        render() {
            this.$el.html(this.template());
            return this;
        }
    });
});

