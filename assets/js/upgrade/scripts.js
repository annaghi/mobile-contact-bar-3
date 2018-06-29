/*!
 * Mobile Contact Bar 2.0.1 by Anna Bansaghi - http://mobilecontactbar.com
 * License GPL-3.0 - https://www.gnu.org/licenses/gpl-3.0.en.html
 */



(function( $, document ) {
    'use strict';

    $(document).ready(function() {

        $('#mcb-table-contacts').find('input[name$="[icon]"][value=""]').each(function() {
            $(this).closest('tr').addClass('mcb-warning');
        });
    });

})( jQuery, document );
