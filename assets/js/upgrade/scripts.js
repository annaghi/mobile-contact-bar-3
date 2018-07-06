(function ($, document) {
  'use strict'

  $(document).ready(function () {
    $('#mcb-table-contacts').find('input[name$="[icon]"][value=""]').each(function () {
      $(this).closest('tr').addClass('mcb-warning')
    })
  })
})(jQuery, document)
