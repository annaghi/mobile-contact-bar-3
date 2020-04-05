/*!
 * Mobile Contact Bar 2.0.1 by Anna Bansaghi - http://mobilecontactbar.com
 * License GPL-3.0 - https://www.gnu.org/licenses/gpl-3.0.en.html
 */

/* global ajaxurl, pagenow, postboxes, mobile_contact_bar */

(function ($, document) {
  'use strict';

  $.fn.addClassOdd = function (klass) {
    this.each(function () {
      $(this).removeClass(klass);
    })
      .filter(function (index) {
        return index % 2 == 1;
      })
      .addClass(klass);

    return this;
  };

  $.fn.setId = function (id, rowType) {
    var name,
      pattern = '[' + rowType + '][]',
      replacement = '[' + rowType + '][' + id + ']';

    $(this)
      .find('input, select, textarea')
      .each(function () {
        name = $(this).attr('name');
        $(this).attr('name', name.replace(pattern, replacement));
      });
    return this;
  };

  $.fn.setActiveContact = function (checked) {
    if (checked) {
      $(this).closest('tr').removeClass('mcb-odd').addClass('mcb-active');
    } else {
      $(this).closest('tr').removeClass('mcb-active');
    }
    return this;
  };

  $.fn.toggleAriaExpanded = function () {
    this.attr('aria-expanded', function (index, attr) {
      return 'true' == attr ? 'false' : 'true';
    });
    return this;
  };

  $.fn.initSettings = function () {
    var tbody = this,
      operators = {
        '<': function (x, y) {
          return Number(x) < Number(y[0]);
        },
        '==': function (x, y) {
          return x == y[0];
        },
        '!=': function (x, y) {
          return x != y[0];
        },
        '|==': function (x, y) {
          return x == y[0] || x == y[1];
        },
      };

    tbody.children('.mcb-child').each(function () {
      var parentClass =
          '.' +
          $(this)
            .classList()
            .find(function (klass) {
              return klass.startsWith('mcb-parent-');
            }),
        parent = tbody.children('.mcb-parent' + parentClass),
        trigger = parent.classList().find(function (klass) {
          return klass.startsWith('mcb-trigger-');
        }),
        match = trigger.match(/^mcb-trigger-([^a-zA-Z0-9]+)(.+)$/),
        operator = match[1],
        operands = match[2].split(','),
        value = '' + parent.find('input').getValue();

      if (operators[operator](value, operands)) {
        $(this).fadeIn(500);
      } else {
        $(this).fadeOut(500);
      }
    });

    tbody.children('.mcb-parent').each(function () {
      var self = $(this),
        parentClass =
          '.' +
          self.classList().find(function (klass) {
            return klass.startsWith('mcb-parent-');
          }),
        children = tbody.children('.mcb-child' + parentClass),
        trigger = self.classList().find(function (klass) {
          return klass.startsWith('mcb-trigger-');
        }),
        match = trigger.match(/^mcb-trigger-([^a-zA-Z0-9]+)(.+)$/),
        operator = match[1],
        operands = match[2].split(','),
        value;

      // bind toggle event to parent
      self.find('input, option').on('change input', function () {
        value = '' + $(this).getValue();

        if (operators[operator](value, operands)) {
          children.each(function () {
            $(this).fadeIn(500);
          });
        } else {
          children.each(function () {
            $(this).fadeOut(500);
          });
        }
      });
    });

    return this;
  };

  $.fn.sortableContacts = function () {
    var allParameters;

    $(this).sortable({
      connectWith: '#mcb-table-contacts tbody',
      handle: '.mcb-contact-icon',
      items: '.mcb-contact',

      start: function (event, ui) {
        ui.placeholder.height(ui.item.height());

        // detach all parameters
        allParameters = $(this).children('.mcb-parameter');
        allParameters.addClass('hidden').detach();

        // hide parameters and set the toggle-parameters button
        $(this)
          .children('.mcb-contact')
          .each(function () {
            $(this)
              .find('.mcb-row-toggle-parameters')
              .children('i')
              .attr('aria-expanded', 'false')
              .removeClass('fa-caret-up')
              .addClass('fa-caret-down');
          });
      },

      stop: function () {
        var contactId, parameters;

        $(this)
          .children('.mcb-contact')
          .each(function () {
            contactId = $(this).attr('data-contact-id');

            // attach paramters for the current contact
            parameters = allParameters.filter(function (i, parameter) {
              return contactId == $(parameter).attr('data-contact-id');
            });
            parameters.insertAfter($(this));
          });

        allParameters = null;
        $(this).children('.mcb-contact').addClassOdd('mcb-odd');
      },
    });
    return this;
  };

  $.fn.initContacts = function () {
    var contactId, parameter;

    // set contact-id on contacts
    this.children('.mcb-contact').each(function (index) {
      $(this).attr('data-contact-id', index);
    });

    // set contact-id and parameter-id on parameters, set toggle action on contacts
    this.children('.mcb-parameter').each(function (index) {
      parameter = $(this);
      contactId = parameter.prevAll('tr.mcb-contact').first().attr('data-contact-id');

      // show toggle-parameters button
      parameter
        .attr('data-contact-id', contactId)
        .attr('data-parameter-id', index)
        .prevAll('.mcb-contact[data-contact-id="' + contactId + '"]')
        .find('.mcb-row-toggle-parameters')
        .removeClass('mcb-invisible');
    });

    // set jQuery UI sortable on contacts
    this.sortableContacts();

    return this;
  };

  $.fn.getValue = function () {
    var value = '';

    switch (this.attr('type')) {
      case 'number':
        value = this.val();
        break;
      case 'checkbox':
        value = this.prop('checked');
        break;
      case 'radio':
        value = this.filter(':checked').val();
        break;
      case 'select':
        value = this.filter(':selected').val();
        break;
    }
    return value;
  };

  $.fn.classList = function () {
    return this[0].className.split(/\s+/);
  };

  $.fn.maxId = function (rowType) {
    var id = -1,
      attr = 'data-' + rowType + '-id';

    if (0 === this.length) {
      return id;
    } else {
      this.each(function () {
        id = Math.max(id, $(this).attr(attr));
      });
      return id;
    }
  };

  var option = {
    init: function () {
      // Contacts and their parameters
      option.tbody = $('#mcb-table-contacts tbody');

      // bind toggle postbox event
      postboxes.add_postbox_toggles(pagenow);

      // init row classes
      // bind toggle children settings
      $('#mcb-table-bar tbody, #mcb-table-icons tbody, #mcb-table-toggle tbody').initSettings();

      // init row classes, data-contact-id, toggle buttons
      // bind sortables
      option.tbody.initContacts();

      option.onReady();
    },

    onReady: function () {
      // Slider value
      $('.mcb-settings').on('input change', '.mcb-slider-input', function () {
        $(this).next('span').html(this.value);
      });

      // Hover action buttons and icons
      $('#mcb-table-contacts thead').on(
        {
          mouseenter: function () {
            $(this).removeClass('wp-ui-text-highlight').addClass('wp-ui-highlight');
          },
          mouseleave: function () {
            $(this).removeClass('wp-ui-highlight').addClass('wp-ui-text-highlight');
          },
        },
        '.mcb-action'
      );

      // Highlight checked contact
      option.tbody.on('change', '.mcb-contact-checkbox input', function (event) {
        event.preventDefault();
        event.stopPropagation();

        $(this).setActiveContact(this.checked);
      });

      // Toggle parameters
      option.tbody.on('click', '.mcb-row-toggle-parameters', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var button = $(this).children('i'),
          contact = button.closest('tr'),
          contactId = contact.attr('data-contact-id'),
          parameters = option.tbody.find('.mcb-parameter[data-contact-id="' + contactId + '"]');

        parameters.toggleClass('hidden');

        button.toggleAriaExpanded().toggleClass('fa-caret-up fa-caret-down');

        if ('true' == button.attr('aria-expanded')) {
          parameters.first().find('.mcb-parameter-value input, .mcb-parameter-value textarea').focus();
        } else {
          contact.find('.mcb-contact-uri input').focus();
        }
      });

      // Delete contact
      option.tbody.on('click', '.mcb-row-delete-contact', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var contactId = $(this).closest('tr').attr('data-contact-id');

        option.tbody.children('[data-contact-id="' + contactId + '"]').remove();
        option.tbody.children('.mcb-contact').addClassOdd('mcb-odd');
      });

      // Delete parameter
      option.tbody.on('click', '.mcb-row-delete-parameter', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var count,
          contactId = $(this).closest('tr').attr('data-contact-id');

        $(this).closest('tr').remove();

        count = option.tbody.find('.mcb-parameter[data-contact-id="' + contactId + '"]').length;

        if (0 === count) {
          option.tbody
            .find('.mcb-contact[data-contact-id="' + contactId + '"]')
            .find('.mcb-row-toggle-parameters')
            .addClass('mcb-invisible')
            .children('i')
            .attr('aria-expanded', 'false')
            .removeClass('fa-caret-up')
            .addClass('fa-caret-down');
        }
      });

      // Add parameter
      option.tbody.on('click', '.mcb-row-add-parameter', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var contact = $(this).closest('.mcb-contact'),
          parameter = $(
            $.parseHTML(
              $('#mcb-tmpl-parameter')
                .html()
                .replace(/\s{2,}/g, '')
            )
          ),
          contactId = contact.attr('data-contact-id'),
          parameters = option.tbody.find('.mcb-parameter[data-contact-id="' + contactId + '"]'),
          parameterId = parameters.maxId('parameter') + 1;

        contact
          .find('.mcb-row-toggle-parameters')
          .removeClass('mcb-invisible')
          .children('i')
          .attr('aria-expanded', 'true')
          .addClass('fa-caret-up')
          .removeClass('fa-caret-down');

        if (0 === parameters.length) {
          contact.after(parameter);
        } else {
          parameters.removeClass('hidden');
          parameters.last().after(parameter);
        }
        parameter
          .attr('data-contact-id', contactId)
          .attr('data-parameter-id', parameterId)
          .setId(contactId, 'contacts')
          .setId(parameterId, 'parameters')
          .find('.mcb-parameter-key')
          .focus();
      });

      // Add icon
      option.tbody.on('click', '.mcb-row-pick-icon', function (event) {
        event.preventDefault();
        event.stopPropagation();

        setTimeout(function () {
          $('#mcb-icon-picker-container div input').focus();
        }, 100);

        var list,
          button = $(this),
          offset = button.offset(),
          contact = button.closest('tr').addClass('mcb-overlay'),
          picker = $(
            $.parseHTML(
              $('#mcb-tmpl-icon-picker')
                .html()
                .replace(/\s{2,}/g, '')
            )
          );

        picker
          .css({ top: offset.top - 12, left: offset.left - 160 })
          .appendTo('body')
          .show();

        list = $('#mcb-icon-picker-container ul');

        // Select an icon
        $('body')
          .off('click', '#mcb-icon-picker-container ul li a')
          .on('click', '#mcb-icon-picker-container ul li a', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var icon = $(this).children().attr('class');

            contact
              .find('.mcb-contact-icon i')
              .removeClass()
              .addClass(icon + ' fa-lg')
              .end()
              .find('input[name$="[icon]"]')
              .val(icon)
              .end()
              .removeClass('mcb-overlay');

            $('#mcb-icon-picker-container').remove();
            contact.removeClass('mcb-warning'); // this should be in the updater.js
          });

        // Browse icons
        $('body')
          .off('click', '#mcb-icon-picker-container div a')
          .on('click', '#mcb-icon-picker-container div a', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if ('back' === $(this).attr('data-direction')) {
              list.find('li:gt(' + (list.children().length - 26) + ')').prependTo(list);
            } else {
              list.find('li:lt(30)').appendTo(list);
            }
          });

        // Search icons
        $('body')
          .off('input', '#mcb-icon-picker-container div input')
          .on('input', '#mcb-icon-picker-container div input', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var search = $(this).val();

            if ('' === search) {
              list.find('li:lt(30)').show();
            } else {
              list
                .children()
                .not('[data-icon*="' + search + '"]')
                .hide();
              list.children('[data-icon*="' + search + '"]').show();
            }
          });

        // Close icon picker on document click
        $(document)
          .off('mouseup')
          .on('mouseup', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (
              !$('#mcb-icon-picker-container').is(event.target) &&
              0 === $('#mcb-icon-picker-container').has(event.target).length
            ) {
              $('#mcb-icon-picker-container').remove();
              contact.removeClass('mcb-overlay');
            }
          });
      });

      // Add contact
      $('#mcb-integration-icons .mcb-action, #mcb-integration-buttons .mcb-action').click(function (event) {
        event.preventDefault();
        event.stopPropagation();

        var contactType = $(this).attr('data-contact-type'),
          contactId = option.tbody.children('.mcb-contact').maxId('contact') + 1,
          thead = $('#mcb-table-contacts thead');

        $.ajax({
          url: ajaxurl,
          method: 'POST',
          data: {
            action: 'ajax_add_contact',
            nonce: mobile_contact_bar.nonce,
            contact_type: contactType,
          },
          beforeSend: function () {
            var position = thead.position(),
              overlay = $('<tr>', { class: 'mcb-overlay' });

            overlay.css({
              position: 'absolute',
              top: position.top,
              left: position.left,
              height: thead.height(),
              width: thead.width(),
            });
            thead.prepend(overlay);
          },
        })
          .done(function (response) {
            if (!response) {
              return false;
            }
            var data = JSON.parse(response);
            if (!data.hasOwnProperty('contact')) {
              return false;
            }

            var contact = $($.parseHTML(data.contact)[0]);

            contact.attr('data-contact-id', contactId).setId(contactId, 'contacts');
            option.tbody.prepend(contact);
            option.tbody.sortableContacts().children('.mcb-contact').addClassOdd('mcb-odd');

            if (data.hasOwnProperty('parameters')) {
              var parameter;

              contact
                .find('.mcb-row-add-parameter')
                .addClass('mcb-invisible')
                .end()
                .find('.mcb-row-toggle-parameters')
                .removeClass('mcb-invisible')
                .children('i')
                .attr('aria-expanded', 'false')
                .removeClass('fa-caret-up')
                .addClass('fa-caret-down');

              for (var i = data.parameters.length - 1; i > -1; i--) {
                parameter = $(data.parameters[i]);

                parameter
                  .attr('data-contact-id', contactId)
                  .attr('data-parameter-id', i)
                  .setId(contactId, 'contacts')
                  .setId(i, 'parameters');

                contact.after(parameter);
              }
            }
          })
          .always(function () {
            thead.find('.mcb-overlay').remove();
          });
      });
    },
  };

  $(document).ready(option.init);
})(jQuery, document);

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
