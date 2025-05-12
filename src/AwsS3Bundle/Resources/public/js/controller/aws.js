define(['jquery', 'klizerawss3/tab-switch'], function ($, tabSwitch) {
    return {
        render: function () {
            console.log('✅ AWS controller loaded');

            // Call your tab logic automatically
            tabSwitch.initialize();
        }
    };
});

