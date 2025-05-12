'use strict';

define([
    'jquery',
    'pim/form',
    'klizer/tab-switch' // This path is from your requirejs.yml
], function (
    $,
    BaseForm
) {
    return BaseForm.extend({
        initialize: function () {
            this._super();
        },

        configure: function () {
            this._super();
        },

        render: function () {
            this._super();
            console.log('AWS Form Loaded - Custom Module');
            return this;
        }
    });
});

