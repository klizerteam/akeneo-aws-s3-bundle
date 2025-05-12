define(['jquery'], function ($) {
    'use strict';

    return {
        initialize: function () {
            console.log('✅ Klizer AWS tabs script initialized');

            $(document).ready(function () {
                $('.AknHorizontalNavtab-link').on('click', function (e) {
                    e.preventDefault();
                    $('.AknHorizontalNavtab-link').removeClass('AknHorizontalNavtab-link--active');
                    $('[data-tab-content]').hide();

                    $(this).addClass('AknHorizontalNavtab-link--active');
                    var tabName = $(this).closest('.AknHorizontalNavtab-item').data('tab');
                    $('[data-tab-content="' + tabName + '"]').show();
                });

                $('.AknHorizontalNavtab-link--active').trigger('click');
            });
        }
    };
});

