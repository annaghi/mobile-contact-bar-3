/*!
 * Mobile Contact Bar 3.0.0 by Anna Bansaghi - http://mobilecontactbar.com
 * License GPLv3 - https://www.gnu.org/licenses/gpl-3.0.en.html
 */

(function (document) {
    'use strict';

    var MobileContactBar = {
        createCookie: function (name, value, days) {
            var expires, date;

            if (days) {
                date = new Date();
                date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
                expires = '; expires=' + date.toGMTString();
            } else {
                expires = '';
            }
            document.cookie = encodeURIComponent(name) + '=' + encodeURIComponent(value) + expires + '; path=/';
        },

        readCookie: function (name) {
            var nameEQ, ca, c;

            nameEQ = encodeURIComponent(name) + '=';
            ca = document.cookie.split(';');

            for (var i = 0; i < ca.length; i++) {
                c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1, c.length);
                }
                if (c.indexOf(nameEQ) === 0) {
                    return decodeURIComponent(c.substring(nameEQ.length, c.length));
                }
            }
            return null;
        },

        eraseCookie: function (name) {
            MobileContactBar.createCookie(name, '', -1);
        }
    };

    var toggle = document.getElementById('mobile-contact-bar-toggle-checkbox');

    if (null !== toggle) {
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();

            if (event.target.checked) {
                MobileContactBar.createCookie('mobile_contact_bar_toggle', 'closed');
            } else {
                MobileContactBar.createCookie('mobile_contact_bar_toggle', 'open');
            }
        });

        if ('closed' == MobileContactBar.readCookie('mobile_contact_bar_toggle') && !toggle.checked) {
            toggle.checked = true;
        }

        if ('open' == MobileContactBar.readCookie('mobile_contact_bar_toggle') && toggle.checked) {
            toggle.checked = false;
        }
    }
})(document);
