/*!
 * Mobile Contact Bar 3.0.0 by Anna Bansaghi - http://mobilecontactbar.com
 * License GPL-2.0 - https://www.gnu.org/licenses/gpl-2.0.en.html
 */

/* global ajaxurl, pagenow, postboxes, mobile_contact_bar */

(function ($, window, document) {
    'use strict';

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
                    .find('.mcb-action-toggle-details')
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

            // bind toggle child-settings
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
                        action: 'mcb_ajax_get_contact',
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
                    $(contact).addClass('mcb-opened').find('.mcb-action-toggle-details').attr('aria-expanded', 'true');

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

                $(this).toggleAriaExpanded().closest('.mcb-contact').toggleClass('mcb-opened');
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

            // Change contact type
            option.contactList.on('change', '.mcb-details-type select', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contact = $(this).closest('.mcb-contact');
                var contactId = contact.attr('data-contact-id');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'mcb_ajax_get_contact_type',
                        nonce: mobile_contact_bar.nonce,
                        contact_id: contactId,
                        contact_type: $(this).val()
                    }
                }).done(function (response) {
                    if (!response) {
                        return false;
                    }
                    var data = JSON.parse(response);
                    if (!data.hasOwnProperty('contact_type') || !data.hasOwnProperty('uri') || !data.hasOwnProperty('parameters')) {
                        return false;
                    }

                    contact.find('.mcb-details-uri').replaceWith($(data.uri));
                    contact.find('.mcb-builtin-parameters, .mcb-custom-parameters, .mcb-builtin-parameter, .mcb-custom-parameter').detach();
                    contact.find('.mcb-details-uri').after($(data.parameters));
                    contact.find('.mcb-details-type .mcb-description').text(data.contact_type.desc_type);
                });
            });

            // Pick icon
            option.contactList.on('click', '.mcb-action-pick-icon', function (event) {
                event.preventDefault();
                event.stopPropagation();

                setTimeout(function () {
                    $('#mcb-icon-picker-container div input').focus();
                }, 100);

                var iconList,
                    searchTerm = '',
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
                    .css({ top: offset.top - 15, left: offset.left })
                    .appendTo('body')
                    .show();

                iconList = $('#mcb-icon-picker-container ul');

                function circular_rolling_window_forward(path, icons) {
                    var lastIcon = iconList.children().last().attr('data-icon'),
                        firstIconIndex = icons.indexOf(lastIcon) + 1;

                    firstIconIndex = firstIconIndex === icons.length ? 0 : firstIconIndex;

                    update_picker_page2(path, icons, firstIconIndex);
                }

                function circular_rolling_window_backward(path, icons) {
                    var firstIcon = iconList.children().first().attr('data-icon'),
                        firstIconIndex = icons.indexOf(firstIcon) - 30;

                    firstIconIndex = firstIconIndex < 0 ? icons.length + firstIconIndex : firstIconIndex;

                    update_picker_page2(path, icons, firstIconIndex);
                }

                function update_picker_page(path, icons, firstIconIndex) {
                    var sliderIndex, icon;

                    iconList.children().each(function (index) {
                        sliderIndex = firstIconIndex + index;
                        icon = icons[sliderIndex % icons.length];
                        $(this).attr('data-icon', icon);
                        $(this).find('a').prop('title', icon);
                        $(this)
                            .find('use')
                            .attr('xlink:href', path + '#tabler-' + icon);
                    });
                }

                function update_picker_page2(path, icons, firstIconIndex) {
                    var sliderIndex, icon;

                    iconList.children().each(function (index) {
                        sliderIndex = firstIconIndex + index;
                        icon = icons[sliderIndex];
                        if (undefined === icon) {
                            $(this).css({ display: 'none' });
                        } else {
                            $(this).css({ display: 'inline-block' });
                            $(this).attr('data-icon', icon);
                            $(this).find('a').prop('title', icon);
                            $(this)
                                .find('use')
                                .attr('xlink:href', path + '#tabler-' + icon);
                        }
                    });
                }

                function filtered_ti_icons(searchTerm) {
                    return searchTerm === ''
                        ? mobile_contact_bar.ti_icons
                        : mobile_contact_bar.ti_icons.filter(function (icon) {
                              return icon.includes(searchTerm);
                          });
                }

                // Change brand
                $('body')
                    .off('click', '#mcb-icon-picker-container button')
                    .on('click', '#mcb-icon-picker-container button', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        $('#mcb-icon-picker-container').find('button').removeClass('mcb-icon-brand-active');
                        $(this).addClass('mcb-icon-brand-active');

                        if ('ti' === $(this).attr('data-brand')) {
                            var path = mobile_contact_bar.page_url + 'assets/icons/ti/tabler-sprite.svg';
                            var icons = filtered_ti_icons(searchTerm);

                            update_picker_page2(path, icons, 0);
                        } else {
                        }
                    });

                // Select an icon
                $('body')
                    .off('click', '#mcb-icon-picker-container ul li a')
                    .on('click', '#mcb-icon-picker-container ul li a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $('#mcb-icon-picker-container').find('button.mcb-icon-brand-active').attr('data-brand');
                        var icon = $(this).prop('title');

                        if ('ti' === brand) {
                            $.ajax({
                                url: ajaxurl,
                                method: 'POST',
                                data: {
                                    action: 'mcb_ajax_get_icon',
                                    nonce: mobile_contact_bar.nonce,
                                    brand: brand,
                                    icon: icon
                                }
                            }).done(function (response) {
                                if (!response) {
                                    return false;
                                }
                                var svg = JSON.parse(response);
                                if (svg.length <= 0) {
                                    return false;
                                }

                                contact
                                    .find('input[name$="[brand]"]')
                                    .val(brand)
                                    .end()
                                    .find('input[name$="[icon]"]')
                                    .val(icon)
                                    .end()
                                    .find('.mcb-summary-icon')
                                    .removeClass('mcb-blank-icon, mcb-fa')
                                    .empty()
                                    .append(svg)
                                    .end()
                                    .find('.mcb-details-icon span')
                                    .removeClass('mcb-blank-icon, mcb-fa')
                                    .empty()
                                    .append(svg);
                            });
                        } else {
                        }

                        $('#mcb-icon-picker-container').remove();
                    });

                // Paginate icons
                $('body')
                    .off('click', '#mcb-icon-picker-container div a')
                    .on('click', '#mcb-icon-picker-container div a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        if ('ti' === $('#mcb-icon-picker-container').find('button.mcb-icon-brand-active').attr('data-brand')) {
                            var path = mobile_contact_bar.page_url + 'assets/icons/ti/tabler-sprite.svg';
                            var icons = filtered_ti_icons(searchTerm);

                            if ('back' === $(this).attr('data-direction')) {
                                circular_rolling_window_backward(path, icons);
                            } else {
                                circular_rolling_window_forward(path, icons);
                            }
                        } else {
                        }
                    });

                // Search icons
                $('body')
                    .off('input', '#mcb-icon-picker-container div input')
                    .on('input', '#mcb-icon-picker-container div input', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        searchTerm = $(this).val();

                        if ('ti' === $('#mcb-icon-picker-container').find('button.mcb-icon-brand-active').attr('data-brand')) {
                            var path = mobile_contact_bar.page_url + 'assets/icons/ti/tabler-sprite.svg';
                            var icons = filtered_ti_icons(searchTerm);

                            update_picker_page2(path, icons, 0);
                        } else {
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

                // Close icon picker on window resize
                $(window).resize(function () {
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
                    .find('input[name$="[brand]"]')
                    .val('')
                    .end()
                    .find('input[name$="[icon]"]')
                    .val('')
                    .end()
                    .find('.mcb-summary-icon')
                    .addClass('mcb-blank-icon')
                    .text('---')
                    .end()
                    .find('.mcb-details-icon span')
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
                        action: 'mcb_ajax_get_parameter',
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
})(jQuery, window, document);

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
