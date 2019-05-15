jQuery(document).ready(function($) {
    'use strict';

    $('#lptw-source-tables').sortable({
        connectWith: '#lptw-destination-tables',
        cursor: 'move',
        start: function(event, ui) {
            ui.item.removeClass('lptw-tab-header').addClass('lptw-drag');
        },
        stop: function(event, ui) {
            ui.item.removeClass('lptw-drag').addClass('lptw-tab-header');
        }
    });

    $('#lptw-destination-tables').sortable({
        connectWith: '#lptw-source-tables',
        cursor: 'move',
        start: function(event, ui) {
            ui.item.removeClass('lptw-tab-header').addClass('lptw-drag');
        },
        stop: function(event, ui) {
            ui.item.removeClass('lptw-drag').addClass('lptw-tab-header');
        }
    });

    jQuery.fn.exists = function() {
        return this.length > 0;
    }

    $('#lptw_generate_shortcode_tables').click(function(e) {
        var shortcode = '[lptw_tabs ids="';
        if ($('#lptw-destination-tables .lptw-tab-header').exists()) {
            $('#lptw-destination-tables .lptw-tab-header').each(function() {
                shortcode += $(this).attr('id') + ','
            });
            shortcode = shortcode.substring(0, shortcode.length - 1);
        }
        shortcode += '"]';

        $('#lptw_generate_shortcode_tables_result').val(shortcode).addClass('ready');
        e.preventDefault();
    });

});
