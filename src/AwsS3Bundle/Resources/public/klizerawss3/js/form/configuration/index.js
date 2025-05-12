require('jquery');
require('bootstrap');

$(document).ready(function () {
    // Activate the first tab by default
    $('#documentation-tab').tab('show');
    
    // Tab switching functionality
    $('#aws-tabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});

