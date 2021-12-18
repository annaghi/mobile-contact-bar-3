/*!
 * Mobile Contact Bar 3.0.0 by Anna Bansaghi - http://mobilecontactbar.com
 * License GPL-2.0 - https://www.gnu.org/licenses/gpl-2.0.en.html
 */

/* global ajaxurl, pagenow, postboxes, mobile_contact_bar */

(function ($, document) {
    'use strict';

    /*** JQuery addClass & removeClass extensions ***/

    (function (func) {
        $.fn.addClass = function (n) {
            // replace the existing function on $.fn
            this.each(function (i) {
                // for each element in the collection
                var $this = $(this); // 'this' is DOM element in this context
                var prevClasses = this.getAttribute('class'); // note its original classes
                var classNames = $.isFunction(n) ? n(i, prevClasses) : n.toString(); // retain function-type argument support
                $.each(classNames.split(/\s+/), function (index, className) {
                    // allow for multiple classes being added
                    if (!$this.hasClass(className)) {
                        // only when the class is not already present
                        func.call($this, className); // invoke the original function to add the class
                        $this.trigger('classAdded', className); // trigger a classAdded event
                    }
                });
                if (prevClasses != this.getAttribute('class')) {
                    // trigger the classChanged event
                    $this.trigger('classChanged');
                }
            });
            return this; // retain jQuery chainability
        };
    })($.fn.addClass);

    (function (func) {
        $.fn.removeClass = function (n) {
            this.each(function (i) {
                var $this = $(this);
                var prevClasses = this.getAttribute('class');
                if (n === undefined) {
                    func.call($this);
                } else {
                    var classNames = $.isFunction(n) ? n(i, prevClasses) : n.toString();
                    $.each(classNames.split(/\s+/), function (index, className) {
                        if ($this.hasClass(className)) {
                            func.call($this, className);
                            $this.trigger('classRemoved', className);
                        }
                    });
                }

                if (prevClasses != this.getAttribute('class')) {
                    $this.trigger('classChanged');
                }
            });

            return this;
        };
    })($.fn.removeClass);

    $.fn.setIconColors = function (value) {
        var icon = $(this).closest('.mcb-palette').prev().find('.mcb-summary-icon-inner');
        var palette = $(this).closest('.mcb-palette');

        var settingsColors = {
            background_color: !!$('.mcb-setting-bar-color').find('.wp-color-result').attr('style')
                ? $('.mcb-setting-bar-color').find('.wp-color-result').css('background-color')
                : '',
            icon_color: !!$('.mcb-setting-icons_labels-icon_color').find('.wp-color-result').attr('style')
                ? $('.mcb-setting-icons_labels-icon_color').find('.wp-color-result').css('background-color')
                : '',
            label_color: !!$('.mcb-setting-icons_labels-label_color').find('.wp-color-result').attr('style')
                ? $('.mcb-setting-icons_labels-label_color').find('.wp-color-result').css('background-color')
                : '',
            border_color: !!$('.mcb-setting-icons_labels-border_color').find('.wp-color-result').attr('style')
                ? $('.mcb-setting-icons_labels-border_color').find('.wp-color-result').css('background-color')
                : ''
        };

        var paletteColors = {
            background_color: !!palette.find('[data-color="background_color"]').find('.wp-color-result').attr('style')
                ? palette.find('[data-color="background_color"]').find('.wp-color-result').css('background-color')
                : '',
            icon_color: !!palette.find('[data-color="icon_color"]').find('.wp-color-result').attr('style')
                ? palette.find('[data-color="icon_color"]').find('.wp-color-result').css('background-color')
                : '',
            label_color: !!palette.find('[data-color="label_color"]').find('.wp-color-result').attr('style')
                ? palette.find('[data-color="label_color"]').find('.wp-color-result').css('background-color')
                : '',
            border_color: !!palette.find('[data-color="border_color"]').find('.wp-color-result').attr('style')
                ? palette.find('[data-color="border_color"]').find('.wp-color-result').css('background-color')
                : ''
        };

        var currentColors = {
            background_color: !!paletteColors.background_color ? paletteColors.background_color : settingsColors.background_color,
            icon_color: !!paletteColors.icon_color ? paletteColors.icon_color : settingsColors.icon_color,
            label_color: !!paletteColors.label_color ? paletteColors.label_color : settingsColors.label_color,
            border_color: !!paletteColors.border_color ? paletteColors.border_color : settingsColors.border_color
        };

        if (!value) {
            icon.css({
                'background-color': settingsColors.background_color,
                color: settingsColors.icon_color,
                'border-top': $('#mcb-icons_labels-borders--top').prop('checked')
                    ? $('#mcb-icons_labels-border_width').val() + 'px solid ' + settingsColors.border_color
                    : '',
                'border-bottom': $('#mcb-icons_labels-borders--bottom').prop('checked')
                    ? $('#mcb-icons_labels-border_width').val() + 'px solid ' + settingsColors.border_color
                    : ''
            });
        } else {
            icon.css({
                'background-color': currentColors.background_color,
                color: currentColors.icon_color,
                'border-top': $('#mcb-icons_labels-borders--top').prop('checked')
                    ? $('#mcb-icons_labels-border_width').val() + 'px solid ' + currentColors.border_color
                    : '',
                'border-bottom': $('#mcb-icons_labels-borders--bottom').prop('checked')
                    ? $('#mcb-icons_labels-border_width').val() + 'px solid ' + currentColors.border_color
                    : ''
            });
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
                    return Number(x) < Number(y);
                },
                '==': function (x, y) {
                    return x == y;
                },
                '!=': function (x, y) {
                    return x != y;
                }
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
                match = trigger.match(/^mcb-trigger-([<=!]+)(.*)$/),
                operator = match[1],
                operand = match[2] || '',
                value = '';

            if (operand === 'true' || operand === true) {
                value = parent
                    .find('input')
                    .toArray()
                    .reduce((acc, inputEl) => acc || $(inputEl).getValue(), false);
                operand = true;
            } else {
                value = '' + parent.find('input').getValue();
            }

            if (operators[operator](value, operand)) {
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
                match = trigger.match(/^mcb-trigger-([<=!]+)(.*)$/),
                operator = match[1],
                operand = match[2] || '',
                value = '';

            // bind toggle event to parent
            self.find('input, option').on('change input', function () {
                if (operand === 'true' || operand === true) {
                    value = self
                        .find('input')
                        .toArray()
                        .reduce((acc, inputEl) => acc || $(inputEl).getValue(), $(this).getValue());
                    operand = true;
                } else {
                    value = '' + $(this).getValue();
                }

                if (operators[operator](value, operand)) {
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

    $.fn.initSortableContacts = function () {
        $(this).sortable({
            connectWith: '#mcb-contacts',
            handle: '.mcb-sortable-draggable',
            items: '.mcb-contact',

            start: function (event, ui) {
                document.activeElement.blur();

                $(this)
                    .find('.mcb-contact')
                    .removeClass('mcb-opened')
                    .end()
                    .find('.mcb-action-toggle-details i')
                    .attr('aria-expanded', 'false');

                ui.placeholder.height(ui.item.children('.mcb-summary').outerHeight());
                ui.helper.height(ui.item.children('.mcb-summary').outerHeight());
                ui.placeholder.css('visibility', 'visible');

                $(this).sortable('refresh', 'refreshPositions');
            }
        });

        return this;
    };

    $.fn.getValue = function () {
        var value = '';

        switch (this.attr('type')) {
            case 'text':
                value = this.val();
                break;
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
            // bind toggle postbox event
            postboxes.add_postbox_toggles(pagenow);

            // init row classes
            // bind toggle children settings
            $('#mcb-table-bar tbody, #mcb-table-icons_labels tbody, #mcb-table-toggle tbody').initSettings();

            // init contact list
            option.contactList = $('#mcb-contacts');
            option.contactList.initSortableContacts();

            option.onReady();
        },

        onReady: function () {
            // Slider value
            $('.mcb-settings').on('input change', '.mcb-slider-input', function () {
                $(this)
                    .next('span')
                    .html(this.value + ' ' + $(this).data('postfix'));
            });

            // Highlight checked contact
            option.contactList.on('change', '.mcb-summary-checkbox input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (this.checked) {
                    $(this).closest('.mcb-summary').addClass('mcb-checked');
                } else {
                    $(this).closest('.mcb-summary').removeClass('mcb-checked');
                }
            });

            // Add contact
            $('#mcb-add-contact').click(function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contactId = option.contactList.children('.mcb-contact').maxId('contact') + 1;

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'add-contact',
                        nonce: mobile_contact_bar.nonce,
                        contact_id: contactId
                    }
                }).done(function (response) {
                    if (!response) {
                        return false;
                    }
                    var data = JSON.parse(response);
                    if (!data.hasOwnProperty('summary') || !data.hasOwnProperty('details')) {
                        return false;
                    }

                    var contact = document.createElement('div');
                    $(contact).addClass('mcb-contact');
                    $(contact).attr('data-contact-id', contactId);

                    $(contact).append($(data.summary)).append($(data.details));

                    $(contact).find('.color-picker').wpColorPicker();

                    option.contactList.append(contact);
                });
            });

            // Delete contact
            option.contactList.on('click', '.mcb-action-delete-contact', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $(this).closest('.mcb-contact').remove();
            });

            // Toggle details
            option.contactList.on('click', '.mcb-action-toggle-details', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $(this).closest('.mcb-contact').toggleClass('mcb-opened');
                $(this).children('i').toggleAriaExpanded();
            });

            // Order higher
            option.contactList.on('click', '.mcb-action-order-higher', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var focused = document.activeElement;
                var prev = $(this).closest('.mcb-contact').prev();
                var contact = $(this).closest('.mcb-contact').detach();

                prev.before(contact);
                focused.focus();
            });

            // Order lower
            option.contactList.on('click', '.mcb-action-order-lower', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var focused = document.activeElement;
                var next = $(this).closest('.mcb-contact').next();
                var contact = $(this).closest('.mcb-contact').detach();

                next.after(contact);
                focused.focus();
            });

            // Select type
            option.contactList.on('change', '.mcb-details-type select', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contact = $(this).closest('.mcb-contact');
                var contactId = contact.attr('data-contact-id');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'change-contact_type',
                        nonce: mobile_contact_bar.nonce,
                        contact_id: contactId,
                        contact_type: $(this).val()
                    }
                }).done(function (response) {
                    if (!response) {
                        return false;
                    }
                    var data = JSON.parse(response);
                    if (!data.hasOwnProperty('contact') || !data.hasOwnProperty('parameters')) {
                        return false;
                    }

                    contact.find('.mcb-details-uri').replaceWith($(data.contact));
                    contact.find('.mcb-builtin-parameters, .mcb-custom-parameters, .mcb-builtin-parameter, .mcb-custom-parameter').detach();
                    contact.find('.mcb-details-uri').after($(data.parameters));
                });
            });

            // Add icon
            option.contactList.on('click', '.mcb-action-pick-icon', function (event) {
                event.preventDefault();
                event.stopPropagation();

                setTimeout(function () {
                    $('#mcb-icon-picker-container div input').focus();
                }, 100);

                var iconList,
                    button = $(this),
                    offset = button.offset(),
                    contact = $(this).closest('.mcb-contact'),
                    picker = $(
                        $.parseHTML(
                            $('#mcb-tmpl-icon-picker')
                                .html()
                                .replace(/\s{2,}/g, '')
                        )
                    );

                picker
                    .css({ top: offset.top - 25, left: offset.left - 13 })
                    .appendTo('body')
                    .show();

                iconList = $('#mcb-icon-picker-container ul');

                // Select an icon
                $('body')
                    .off('click', '#mcb-icon-picker-container ul li a')
                    .on('click', '#mcb-icon-picker-container ul li a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var icon = $(this).children().attr('class');

                        contact
                            .find('input[name$="[icon]"]')
                            .val(icon)
                            .end()
                            .find('.mcb-summary-icon i')
                            .removeClass()
                            .addClass(icon)
                            .empty()
                            .end()
                            .find('.mcb-details-icon i')
                            .removeClass()
                            .addClass(icon)
                            .empty();

                        $('#mcb-icon-picker-container').remove();
                    });

                // Browse icons
                $('body')
                    .off('click', '#mcb-icon-picker-container div a')
                    .on('click', '#mcb-icon-picker-container div a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        if ('back' === $(this).attr('data-direction')) {
                            iconList.find('li:gt(' + (iconList.children().length - 31) + ')').prependTo(iconList);
                        } else {
                            iconList.find('li:lt(30)').appendTo(iconList);
                        }
                    });

                // Search icons
                $('body')
                    .off('input', '#mcb-icon-picker-container div input')
                    .on('input', '#mcb-icon-picker-container div input', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var searchTerm = $(this).val();

                        if ('' === searchTerm) {
                            iconList.find('li:lt(30)').show();
                        } else {
                            iconList
                                .children()
                                .not('[data-icon*="' + searchTerm + '"]')
                                .hide();
                            iconList.children('[data-icon*="' + searchTerm + '"]').show();
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
                        }
                    });
            });

            // Clear icon
            option.contactList.on('click', '.mcb-action-clear-icon', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contact = $(this).closest('.mcb-contact');

                contact
                    .find('input[name$="[icon]"]')
                    .val('')
                    .end()
                    .find('.mcb-summary-icon i')
                    .removeClass()
                    .addClass('mcb-blank-icon')
                    .text('---')
                    .end()
                    .find('.mcb-details-icon i')
                    .removeClass()
                    .addClass('mcb-blank-icon')
                    .text('---');
            });

            // Update label
            option.contactList.on('input', '.mcb-details-label input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contact = $(this).closest('.mcb-contact');
                contact.find('.mcb-summary-label').text($(this).val());
            });

            // Update URI
            option.contactList.on('input', '.mcb-details-uri input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contact = $(this).closest('.mcb-contact');
                contact.find('.mcb-summary-uri').text($(this).val());
            });

            // Add parameter
            option.contactList.on('click', '.mcb-add-parameter', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contact = $(this).closest('.mcb-contact');
                var parameters = contact.find('.mcb-custom-parameter');

                var contactId = contact.attr('data-contact-id'),
                    parameterId = parameters.maxId('parameter') + 1;

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'add-parameter',
                        nonce: mobile_contact_bar.nonce,
                        contact_id: contactId,
                        parameter_id: parameterId
                    }
                }).done(function (response) {
                    if (!response) {
                        return false;
                    }
                    var parameter = JSON.parse(response);
                    if (parameter.length <= 0) {
                        return false;
                    }

                    contact.find('.mcb-custom-parameters').after($(parameter));
                });
            });

            // Delete parameter
            option.contactList.on('click', '.mcb-action-delete-parameter', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $(this).closest('.mcb-custom-parameter').remove();
            });
        }
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

// groups.fas = groups.fas.sort();
// groups.far = groups.far.sort();
// groups.fab = groups.fab.sort();
// copy(JSON.stringify(groups));
