/**
 * 2019-2021 Team Ever
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Team Ever <https://www.team-ever.com/>
 *  @copyright 2019-2021 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
 $(document).ready(function(){
    var cookie_time = $('#everpspopup_block_center').data('expire');
    var adult_mode = $('#everpspopup_block_center').data('adult');
    var delay = parseInt($('#everpspopup_block_center').data('delay'));
    var cookie_suffix = $('#everpspopup_block_center').data('cookiesuffix');

    (function (factory) {
        if (typeof define === 'function' && define.amd) {
            // AMD
            define(['jquery'], factory);
        } else if (typeof exports === 'object') {
            // CommonJS
            factory(require('jquery'));
        } else {
            // Browser globals
            factory(jQuery);
        }
    }(function ($) {

        var pluses = /\+/g;

        function encode(s) {
            return config.raw ? s : encodeURIComponent(s);
        }

        function decode(s) {
            return config.raw ? s : decodeURIComponent(s);
        }

        function stringifyCookieValue(value) {
            return encode(config.json ? JSON.stringify(value) : String(value));
        }

        function parseCookieValue(s) {
            if (s.indexOf('"') === 0) {
                // This is a quoted cookie as according to RFC2068, unescape...
                s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
            }

            try {
                // Replace server-side written pluses with spaces.
                // If we can't decode the cookie, ignore it, it's unusable.
                // If we can't parse the cookie, ignore it, it's unusable.
                s = decodeURIComponent(s.replace(pluses, ' '));
                return config.json ? JSON.parse(s) : s;
            } catch(e) {}
        }

        function read(s, converter) {
            var value = config.raw ? s : parseCookieValue(s);
            return $.isFunction(converter) ? converter(value) : value;
        }

        var config = $.cookie = function (key, value, options) {

            // Write

            if (value !== undefined && !$.isFunction(value)) {
                options = $.extend({}, config.defaults, options);

                if (typeof options.expires === 'number') {
                    var days = options.expires, t = options.expires = new Date();
                    t.setTime(+t + days * 864e+5);
                }

                return (document.cookie = [
                    encode(key), '=', stringifyCookieValue(value),
                    options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                    options.path    ? '; path=' + options.path : '',
                    options.domain  ? '; domain=' + options.domain : '',
                    options.secure  ? '; secure' : ''
                ].join(''));
            }

            // Read

            var result = key ? undefined : {};

            // To prevent the for loop in the first place assign an empty array
            // in case there are no cookies at all. Also prevents odd result when
            // calling $.cookie().
            var cookies = document.cookie ? document.cookie.split('; ') : [];

            for (var i = 0, l = cookies.length; i < l; i++) {
                var parts = cookies[i].split('=');
                var name = decode(parts.shift());
                var cookie = parts.join('=');

                if (key && key === name) {
                    // If second argument (value) is a function it's a converter...
                    result = read(cookie, value);
                    break;
                }

                // Prevent storing a cookie that we couldn't decode.
                if (!key && (cookie = read(cookie)) !== undefined) {
                    result[name] = cookie;
                }
            }

            return result;
        };

        config.defaults = {};

        $.removeCookie = function (key, options) {
            if ($.cookie(key) === undefined) {
                return false;
            }

            // Must not alter options, thus extending a fresh object...
            $.cookie(key, '', $.extend({}, options, { expires: -1 }));
            return !$.cookie(key);
        };

    }));

    if (cookie_time == 0) {
        var popcontent = 0;
    } else {
        var popcontent = 1;
    }

    if ($('#ever_fancy_mark').length) {
        setTimeout(function() {
            if ($.cookie('everpspopup' + cookie_suffix) != popcontent) {
                if (adult_mode) {
                    $('#ever_fancy_mark').fancybox({
                        'type'  :   'inline',
                        'transitionIn'  :   'elastic',
                        'transitionOut' :   'elastic',
                        'speedIn'       :   600,
                        'speedOut'      :   200,
                        'showCloseButton' : false,
                        'hideOnOverlayClick' : false,
                        'hideOnContentClick' : false,
                        'closeClick' : false,
                        'overlayShow'   :   false,
                        'closeBtn' : false,
                        'opacity' : 1,
                        beforeClose : function () {
                            return false;
                        },
                    }).trigger('click');
                    $('.fancybox-close-small, .fancybox-skin .fancybox-close').hide();
                    $('.fancybox-bg, .fancybox-stage, .fancybox-overlay, .fancybox-overlay-fixed').css('background-color', '#000000');
                } else {
                    $('#ever_fancy_mark').fancybox({
                        'type'  :   'inline',
                        'transitionIn'  :   'elastic',
                        'transitionOut' :   'elastic',
                        'speedIn'       :   600,
                        'speedOut'      :   200,
                        'hideOnContentClick'    :   true,
                        'overlayShow'   :   false,
                        'opacity' : 1,
                        'beforeClose': function() {
                            $.cookie('everpspopup' + cookie_suffix, popcontent, { expires: cookie_time});
                        },
                    }).trigger('click');
                    $(window).bind('beforeunload', function(){
                      $.cookie('everpspopup' + cookie_suffix, popcontent, { expires: cookie_time});
                    });
                }
            }
        }, delay);
        if(adult_mode) {
            $('#adult_mode_form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: $('#everpspopup_new_adult_url').val(),
                    cache: false,
                    dataType: 'JSON',
                    data: {
                        action: 'CheckAge',
                        ajax: true,
                        ever_birthday: $('#ever_birthday').val()
                    },
                    success: function(data) {
                        if (data.return) {
                            console.log(data.message);
                            $.cookie('everpspopup' + cookie_suffix, popcontent, { expires: cookie_time});
                            $('#everpspopup_confirm').slideDown();
                            $('#everpspopup_confirm').html(data.message);
                            setTimeout(function() { location.reload() }, 2000);
                        } else {
                            $('#everpspopup_error').slideDown();
                            $('#everpspopup_error').html(data.error);
                            setTimeout(function() { $('#everpspopup_error').fadeOut(); }, 5000);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus + ' ' + errorThrown);
                    }
                });
            });
        }
    } else {
        console.log('No popup available');
    }
    if ($('#ever_error_content').length) {
        var errors = $('#ever_error_content').html();
        console.log(errors);
    }

    $('#ever_subscription_form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: $('#everpspopup_new_subscribe_url').val(),
            cache: false,
            dataType: 'JSON',
            data: {
                action: 'NewSubscribe',
                ajax: true,
                ever_email: $('#ever_email').val()
            },
            success: function(data) {
                if (data.return) {
                    $('#everpspopup_confirm').slideDown();
                    $('#everpspopup_confirm').html(data.message);
                    setTimeout(function() { $.fancybox.close() }, 4000);
                } else {
                    $('#everpspopup_error').slideDown();
                    $('#everpspopup_error').html(data.error);
                    setTimeout(function() { $('#everpspopup_error').fadeOut(); }, 5000);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus + ' ' + errorThrown);
            }
        });
    });
});