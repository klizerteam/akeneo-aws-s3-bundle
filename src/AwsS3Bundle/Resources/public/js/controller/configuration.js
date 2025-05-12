define(['pim/router'], function (router) {
    return {
        route: 'klizer_aws_configuration',
        renderRoute: function () {
            router.redirectToRoute('klizer_aws_configuration_form');
        }
    };
});

