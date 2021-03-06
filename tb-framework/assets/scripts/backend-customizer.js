/*
 *  @package    TB Framework
 *  @author     Tony Bogdanov <support@tonybogdanov.com>
 *  @license    MIT http://www.opensource.org/licenses/mit-license.php
 *  @copyright  Copyright (c) 2017. www.tonybogdanov.com. All Rights Reserved.
 */
(function($) {
    "use strict";

    if('undefined' == typeof window.tbGetCookie) {
        window.tbGetCookie = function(name) {
            var value = "; " + document.cookie;
            var parts = value.split("; " + name + "=");
            if (parts.length == 2) return parts.pop().split(";").shift();
        };
    }

    if('undefined' == typeof window.tbSetCookie) {
        window.tbSetCookie = function(name, value) {
            document.cookie = name + "=" + value + "; path=/";
        };
    }

    $(function() {
        var $body = $('body');

        $body.on('click', '[href^="#tb_tab_"]', function(e) {
            e.preventDefault();

            var $nav = $(this);
            var $tabs = $nav.parents('.nav-tab-wrapper').first();
            var $active = $nav.siblings('.nav-tab-active');

            if(0 < $active.length) {
                $($active.removeClass('nav-tab-active').attr('href')).removeClass('tb-tab-active');
            }

            $($nav.addClass('nav-tab-active').attr('href')).addClass('tb-tab-active');

            tbSetCookie($tabs.attr('id'), $nav.index());
        });

        $('.nav-tab-wrapper').each(function() {
            var $tabs = $(this);

            var tabsId = $tabs.attr('id');
            var activeTab = tbGetCookie(tabsId);

            $tabs.find('[href^="#tb_tab_"]:eq(' + ('undefined' == typeof activeTab ? 0 : parseInt(activeTab)) + ')').click();
        });
    });
})(jQuery);