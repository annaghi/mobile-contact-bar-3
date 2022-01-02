/*!
 * Mobile Contact Bar 3.0.0 by Anna Bansaghi - http://mobilecontactbar.com
 * License GPLv3 - https://www.gnu.org/licenses/gpl-3.0.en.html
 */

/* global ajaxurl, mobile_contact_bar */

(function ($, document) {
    'use strict';

    var notice = {
        init: function () {
            $('.mobile-contact-bar-notice[data-dismiss]').on('click.wp-dismiss-notice', function (event) {
                if ($(event.target).hasClass('notice-dismiss') || $(event.target).closest('a').hasClass('mobile-contact-bar-whats-new')) {
                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'mcb_ajax_dismiss_notice',
                            nonce: mobile_contact_bar.nonce,
                            notice: $(event.currentTarget).closest('.mobile-contact-bar-notice').data('dismiss')
                        }
                    });
                }
            });

            $('.mobile-contact-bar-notice[data-dismiss]').on('click', '.mobile-contact-bar-whats-new', function (event) {
                $(event.currentTarget).closest('.mobile-contact-bar-notice').remove();
            });
        }
    };

    $(document).ready(notice.init);
})(jQuery, document);
