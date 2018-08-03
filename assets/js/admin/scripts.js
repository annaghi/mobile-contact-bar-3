/* global ajaxurl, pagenow, postboxes, mobile_contact_bar, mcb */

(function ($, document) {
  'use strict'

  $.fn.addClassOdd = function (klass) {
    this.each(function () {
      $(this).removeClass(klass)
    }).filter(function (index) {
      return index % 2 === 1
    }).addClass(klass)

    return this
  }

  $.fn.setId = function (id, rowType) {
    var name

    var pattern = '[' + rowType + '][]'

    var replacement = '[' + rowType + '][' + id + ']'

    $(this).find('input, select, textarea').each(function () {
      name = $(this).attr('name')
      $(this).attr('name', name.replace(pattern, replacement))
    })
    return this
  }

  $.fn.setActiveContact = function (checked) {
    if (checked) {
      $(this).closest('tr').removeClass('mcb-odd').addClass('mcb-active')
    } else {
      $(this).closest('tr').removeClass('mcb-active')
    }
    return this
  }

  $.fn.toggleAriaExpanded = function () {
    this.attr('aria-expanded', function (index, attr) {
      return attr === 'true' ? 'false' : 'true'
    })
    return this
  }

  $.fn.initSettings = function () {
    var tbody = this

    var operators = {
      '<': function (x, y) { return Number(x) < Number(y[0]) },
      '==': function (x, y) { return x === y[0] },
      '!=': function (x, y) { return x !== y[0] },
      '|==': function (x, y) { return x === y[0] || x === y[1] }
    }

    tbody.children('.mcb-child').each(function () {
      var self = this
      var classes = self.classList
      var parent

      for (var i = 0; i < classes.length; i++) {
        if (classes[i].startsWith('mcb-parent-')) {
          var tmp = classes[i].split('--')
          parent = tmp[0].replace('parent', 'setting')
          parent = tbody.children('.' + parent)

          var match = tmp[1].match(/^([^a-zA-Z0-9]+)(.+)$/)
          var operator = match[1]
          var operands = match[2].split(',')
          var value = '' + parent.find('input').getValue()

          if (operators[operator](value, operands)) {
            $(self).fadeIn(500)
          } else {
            $(self).fadeOut(500)
          }

          // bind toggle event to parent
          parent.find('input, option').on('change input', function () {
            value = '' + $(this).getValue()
            if (operators[operator](value, operands)) {
              $(self).fadeIn(500)
            } else {
              $(self).fadeOut(500)
            }
          })
        }
      }
    })

    return this
  }

  $.fn.sortableContacts = function () {
    var allParameters

    $(this).sortable({
      connectWith: '#mcb-table-contacts tbody',
      handle: '.mcb-contact-icon',
      items: '.mcb-contact',

      start: function (event, ui) {
        ui.placeholder.height(ui.item.height())

        // detach all parameters
        allParameters = $(this).children('.mcb-parameter')
        allParameters.addClass('hidden').detach()

        // hide parameters and set the toggle-parameters button
        $(this).children('.mcb-contact').each(function () {
          $(this).find('.mcb-row-toggle-parameters')
            .children('i').attr('aria-expanded', 'false').removeClass('fa-caret-up').addClass('fa-caret-down')
        })
      },

      stop: function () {
        var contactId,
          parameters

        $(this).children('.mcb-contact').each(function () {
          contactId = $(this).attr('data-contact-id')

          // attach paramters for the current contact
          parameters = allParameters.filter(function (i, parameter) { return contactId === $(parameter).attr('data-contact-id') })
          parameters.insertAfter($(this))
        })

        allParameters = null
        $(this).children('.mcb-contact').addClassOdd('mcb-odd')

        mcb.hook.call('onStopSortingContactList')
      }
    })
    return this
  }

  $.fn.initContacts = function () {
    var contactId,
      parameter

    // set contact-id on contacts
    this.children('.mcb-contact').each(function (index) {
      $(this).attr('data-contact-id', index)
    })

    // set contact-id and parameter-id on parameters, set toggle action on contacts
    this.children('.mcb-parameter').each(function (index) {
      parameter = $(this)
      contactId = parameter.prevAll('tr.mcb-contact').first().attr('data-contact-id')

      // show toggle-parameters button
      parameter
        .attr('data-contact-id', contactId).attr('data-parameter-id', index)
        .prevAll('.mcb-contact[data-contact-id="' + contactId + '"]')
        .find('.mcb-row-toggle-parameters').removeClass('mcb-invisible')
    })

    // set jQuery UI sortable on contacts
    this.sortableContacts()

    return this
  }

  $.fn.getValue = function () {
    var value = ''

    switch (this.attr('type')) {
      case 'number':
        value = this.val()
        break
      case 'checkbox':
        value = this.prop('checked')
        break
      case 'radio':
        value = this.filter(':checked').val()
        break
      case 'select':
        value = this.filter(':selected').val()
        break
    }
    return value
  }

  $.fn.maxId = function (rowType) {
    var id = -1

    var attr = 'data-' + rowType + '-id'

    if (this.length === 0) {
      return id
    } else {
      this.each(function () {
        id = Math.max(id, $(this).attr(attr))
      })
      return id
    }
  }

  var option = {

    init: function () {
      // Contacts and their parameters
      option.tbody = $('#mcb-table-contacts tbody')

      // bind toggle postbox event
      postboxes.add_postbox_toggles(pagenow)

      // init row classes
      // bind toggle children settings
      $('.mcb-settings tbody').each(function () { $(this).initSettings() })

      // init row classes, data-contact-id, toggle buttons
      // bind sortables
      option.tbody.initContacts()

      option.onReady()
    },

    onReady: function () {
      // Slider value
      $('.mcb-settings').on('input change', '.mcb-slider-input', function () {
        $(this).next('span').html(this.value)
      })

      $('.mcb-settings').on('change', '#mcb-badges-corner input', function () {
        var corner = $('#mcb-badges-corner input:checked').val()
        $('#mcb-table-contacts')
          .find('.mcb-badge')
          .removeClass('mcb-badge-top-right mcb-badge-bottom-right mcb-badge-bottom-left mcb-badge-top-left')
          .addClass('mcb-badge-' + corner)
      })

      // Hover action buttons and icons
      $('#mcb-table-contacts thead').on({
        mouseenter: function () {
          $(this).removeClass('wp-ui-text-highlight').addClass('wp-ui-highlight')
        },
        mouseleave: function () {
          $(this).removeClass('wp-ui-highlight').addClass('wp-ui-text-highlight')
        }
      }, '.mcb-action')

      // Highlight checked contact
      option.tbody.on('change', '.mcb-contact-checkbox input', function () {
        $(this).setActiveContact(this.checked)
      })

      // Toggle parameters
      option.tbody.on('click', '.mcb-row-toggle-parameters', function (event) {
        event.preventDefault()
        event.stopPropagation()

        var button = $(this).children('i')

        var contact = button.closest('tr')

        var contactId = contact.attr('data-contact-id')

        var parameters = option.tbody.find('.mcb-parameter[data-contact-id="' + contactId + '"]')

        parameters.toggleClass('hidden')

        button.toggleAriaExpanded().toggleClass('fa-caret-up fa-caret-down')

        if (button.attr('aria-expanded') === 'true') {
          parameters.first().find('.mcb-parameter-value input, .mcb-parameter-value textarea').focus()
        } else {
          contact.find('.mcb-contact-uri input').focus()
        }
      })

      // Delete contact
      option.tbody.on('click', '.mcb-row-delete-contact', function (event) {
        event.preventDefault()
        event.stopPropagation()

        var contactId = $(this).closest('tr').attr('data-contact-id')
        var checkbox = $(this).closest('tr').find('.mcb-contact-checkbox input')

        if (checkbox.prop('checked')) {
          var index = $('.mcb-contact-checkbox input:checked').index(checkbox)
          mcb.hook.call('onDeleteContact', index)
        }

        option.tbody.children('[data-contact-id="' + contactId + '"]').remove()
        option.tbody.children('.mcb-contact').addClassOdd('mcb-odd')
      })

      // Delete parameter
      option.tbody.on('click', '.mcb-row-delete-parameter', function (event) {
        event.preventDefault()
        event.stopPropagation()

        var count

        var contactId = $(this).closest('tr').attr('data-contact-id')

        $(this).closest('tr').remove()

        count = option.tbody.find('.mcb-parameter[data-contact-id="' + contactId + '"]').length

        if (count === 0) {
          option.tbody
            .find('.mcb-contact[data-contact-id="' + contactId + '"]')
            .find('.mcb-row-toggle-parameters').addClass('mcb-invisible')
            .children('i').attr('aria-expanded', 'false').removeClass('fa-caret-up').addClass('fa-caret-down')
        }
      })

      // Add parameter
      option.tbody.on('click', '.mcb-row-add-parameter', function (event) {
        event.preventDefault()
        event.stopPropagation()

        var contact = $(this).closest('.mcb-contact')

        var parameter = $($.parseHTML($('#mcb-tmpl-parameter').html().replace(/\s{2,}/g, '')))

        var contactId = contact.attr('data-contact-id')

        var parameters = option.tbody.find('.mcb-parameter[data-contact-id="' + contactId + '"]')

        var parameterId = parameters.maxId('parameter') + 1

        contact
          .find('.mcb-row-toggle-parameters').removeClass('mcb-invisible')
          .children('i').attr('aria-expanded', 'true').addClass('fa-caret-up').removeClass('fa-caret-down')

        if (parameters.length === 0) {
          contact.after(parameter)
        } else {
          parameters.removeClass('hidden')
          parameters.last().after(parameter)
        }
        parameter
          .attr('data-contact-id', contactId).attr('data-parameter-id', parameterId)
          .setId(contactId, 'contacts').setId(parameterId, 'parameters')
          .find('.mcb-parameter-key').focus()
      })

      // Add icon
      option.tbody.on('click', '.mcb-row-pick-icon', function (event) {
        event.preventDefault()
        event.stopPropagation()

        setTimeout(function () { $('#mcb-icon-picker-container div input').focus() }, 100)

        var list

        var button = $(this)

        var offset = button.offset()

        var contact = button.closest('tr').addClass('mcb-overlay')

        var picker = $($.parseHTML($('#mcb-tmpl-icon-picker').html().replace(/\s{2,}/g, '')))

        picker.css({ 'top': offset.top - 12, 'left': offset.left - 160 }).appendTo('body').show()

        list = $('#mcb-icon-picker-container ul')

        // Select an icon
        $('body').off('click', '#mcb-icon-picker-container ul li a').on('click', '#mcb-icon-picker-container ul li a', function (event) {
          event.preventDefault()
          event.stopPropagation()

          var icon = $(this).children().attr('class')
          var checkbox = contact.find('.mcb-contact-checkbox input')

          contact
            .find('.mcb-contact-icon i').removeClass().addClass(icon + ' fa-lg')
            .end()
            .find('input[name$="[icon]"]').val(icon)
            .end()
            .removeClass('mcb-overlay')

          $('#mcb-icon-picker-container').remove()
          contact.removeClass('mcb-warning') // this should be in the updater.js

          if (checkbox.prop('checked')) {
            var index = $('.mcb-contact-checkbox input:checked').index(checkbox)
            mcb.hook.call('onSelectIcon', {index: index, icon: icon})
          }
        })

        // Browse icons
        $('body').off('click', '#mcb-icon-picker-container div a').on('click', '#mcb-icon-picker-container div a', function (event) {
          event.preventDefault()
          event.stopPropagation()

          if ($(this).attr('data-direction') === 'back') {
            list.find('li:gt(' + (list.children().length - 26) + ')').prependTo(list)
          } else {
            list.find('li:lt(30)').appendTo(list)
          }
        })

        // Search icons
        $('body').off('input', '#mcb-icon-picker-container div input').on('input', '#mcb-icon-picker-container div input', function (event) {
          event.preventDefault()
          event.stopPropagation()

          var search = $(this).val()

          if (search === '') {
            list.find('li:lt(30)').show()
          } else {
            list.children().not('[data-icon*="' + search + '"]').hide()
            list.children('[data-icon*="' + search + '"]').show()
          }
        })

        // Close icon picker on document click
        $(document).off('mouseup').on('mouseup', function (event) {
          event.preventDefault()
          event.stopPropagation()

          if ((!$('#mcb-icon-picker-container').is(event.target)) && ($('#mcb-icon-picker-container').has(event.target).length === 0)) {
            $('#mcb-icon-picker-container').remove()
            contact.removeClass('mcb-overlay')
          }
        })
      })

      // Add contact
      $('#mcb-integration-icons .mcb-action, #mcb-integration-buttons .mcb-action').click(function (event) {
        event.preventDefault()
        event.stopPropagation()

        var contactType = $(this).attr('data-contact-type')

        var contactId = option.tbody.children('.mcb-contact').maxId('contact') + 1

        var thead = $('#mcb-table-contacts thead')

        $.ajax({
          url: ajaxurl,
          method: 'POST',
          data: {
            'action': 'ajax_add_contact',
            'nonce': mobile_contact_bar.nonce,
            'contact_type': contactType
          },
          beforeSend: function () {
            var position = thead.position()

            var overlay = $('<tr>', { 'class': 'mcb-overlay' })

            overlay.css({
              position: 'absolute',
              top: position.top,
              left: position.left,
              height: thead.height(),
              width: thead.width()
            })
            thead.prepend(overlay)
          }
        })
          .done(function (response) {
            if (!response) {
              return false
            }
            var data = JSON.parse(response)
            if (!data.hasOwnProperty('contact')) {
              return false
            }

            var contact = $($.parseHTML(data.contact)[0])

            contact.attr('data-contact-id', contactId).setId(contactId, 'contacts')
            option.tbody.prepend(contact)
            option.tbody.sortableContacts().children('.mcb-contact').addClassOdd('mcb-odd')

            if (data.hasOwnProperty('parameters')) {
              var parameter

              contact
                .find('.mcb-row-add-parameter').addClass('mcb-invisible')
                .end()
                .find('.mcb-row-toggle-parameters').removeClass('mcb-invisible')
                .children('i').attr('aria-expanded', 'false').removeClass('fa-caret-up').addClass('fa-caret-down')

              for (var i = data.parameters.length - 1; i > -1; i--) {
                parameter = $(data.parameters[i])

                parameter
                  .attr('data-contact-id', contactId).attr('data-parameter-id', i)
                  .setId(contactId, 'contacts').setId(i, 'parameters')

                contact.after(parameter)
              }
            }
          })
          .always(function () {
            thead.find('.mcb-overlay').remove()
          })
      })
    }
  }

  $(document).ready(option.init)
})(jQuery, document)

// Colect icon names from FontAwesome cheatsheet
//
//
// var groups = {};
// var sections = document.getElementsByClassName('cheatsheet-set');
// for( const section of sections ) {
//     const names = [];
//     groups['fa' + section.id.charAt(0)] = names;
//     var icons = section.getElementsByClassName('icon');
//     for( const icon of icons ) {
//         const name = icon.getElementsByTagName('dd')[0].innerText;
//         names.push(name);
//     }
// }
// copy(JSON.stringify(groups));
