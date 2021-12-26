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

    $.fn.maxKey = function (rowType) {
        var key = -1,
            attr = 'data-' + rowType + '-key';

        if (0 === this.length) {
            return key;
        } else {
            this.each(function () {
                key = Math.max(key, $(this).attr(attr));
            });
            return key;
        }
    };

    var filtered_icons = function (icons, searchTerm) {
        return searchTerm === ''
            ? icons
            : icons.filter(function (icon) {
                  return icon.includes(searchTerm);
              });
    };

    var circular_window_forward = function (iconList, path, icons, fn) {
        var firstIcon = iconList.children().first().attr('data-icon'),
            firstIconIndex = icons.indexOf(firstIcon),
            nextPageFirstIconIndex = firstIconIndex + 30 < icons.length ? firstIconIndex + 30 : 0;

        fn(iconList, path, icons, nextPageFirstIconIndex);
    };

    var circular_window_backward = function (iconList, path, icons, fn) {
        var firstIcon = iconList.children().first().attr('data-icon'),
            firstIconIndex = icons.indexOf(firstIcon),
            prevPageFirstIconIndex = 0;

        if (firstIconIndex === 0 && icons.length % 30 === 0) {
            prevPageFirstIconIndex = icons.length - 30;
        } else if (firstIconIndex === 0 && icons.length % 30 > 0) {
            prevPageFirstIconIndex = icons.length - (icons.length % 30);
        } else if (firstIconIndex >= 30) {
            prevPageFirstIconIndex = firstIconIndex - 30;
        }

        fn(iconList, path, icons, prevPageFirstIconIndex);
    };

    var ti_update_picker_window = function (iconList, path, icons, firstIconIndex) {
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
    };

    var fa_update_picker_window = function (iconList, path, icons, firstIconIndex) {
        var sliderIndex,
            icon,
            names = [];

        iconList.children().each(function (index) {
            sliderIndex = firstIconIndex + index;
            icon = icons[sliderIndex];

            if (undefined === icon) {
                $(this).css({ display: 'none' });
            } else {
                names = icon.split(' ');
                $(this).css({ display: 'inline-block' });
                $(this).attr('data-icon', icon);
                $(this).find('a').prop('title', names[1]);
                $(this)
                    .find('use')
                    .attr('xlink:href', path + names[0] + '.svg#' + names[1]);
            }
        });
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
            // Icon lists
            var ti_icons = mobile_contact_bar.ti_icons;
            var fa_icons = [];
            $.each(mobile_contact_bar.fa_icons, function (section, icons) {
                $.each(icons, function (index, icon) {
                    fa_icons.push(section + ' ' + icon);
                });
            });

            // Slider value
            $('.mcb-settings').on('input change', '.mcb-slider-input', function () {
                $(this)
                    .next('span')
                    .html(this.value + ' ' + $(this).data('postfix'));
            });

            // Highlight checked contact
            // Update badge-length
            option.contactList.on('change', '.mcb-summary-checkbox input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (this.checked) {
                    $(this).closest('.mcb-contact').addClass('mcb-checked');
                } else {
                    $(this).closest('.mcb-contact').removeClass('mcb-checked');
                }

                var checked_contacts_length = option.contactList.find('.mcb-checked').length;
                0 === checked_contacts_length
                    ? $('#mcb-badge-length').removeClass().addClass('mcb-badge-disabled').text(0)
                    : $('#mcb-badge-length').removeClass().addClass('mcb-badge-enabled').text(checked_contacts_length);
            });

            // Update badge-display
            $('#mcb-bar-device').on('change', 'input', function () {
                'mcb-bar-device--none' === $(this).attr('id')
                    ? $('#mcb-badge-display').removeClass().addClass('mcb-badge-disabled').text(mobile_contact_bar.l10n.disabled)
                    : $('#mcb-badge-display').removeClass().addClass('mcb-badge-enabled').text(mobile_contact_bar.l10n.enabled);
            });

            option.contactList.on('change', '.mcb-summary-checkbox input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (this.checked) {
                    $(this).closest('.mcb-contact').addClass('mcb-checked');
                } else {
                    $(this).closest('.mcb-contact').removeClass('mcb-checked');
                }
                option.contactList.find('.mcb-checked').length === 0
                    ? $('#mcb-bar-count').removeClass().addClass('mcb-disabled')
                    : $('#mcb-bar-count').removeClass().addClass('mcb-enabled');
            });

            // Add contact
            $('#mcb-add-contact').click(function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contactKey = option.contactList.children('.mcb-contact').maxKey('contact') + 1;

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'mcb_ajax_get_contact',
                        nonce: mobile_contact_bar.nonce,
                        contact_key: contactKey
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
                    $(contact).attr('data-contact-key', contactKey);

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

            // Close contact
            option.contactList.on('click', '.mcb-action-close-contact', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $(this).closest('.mcb-contact').removeClass('mcb-opened').find('.mcb-action-toggle-details').attr('aria-expanded', 'false');
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
                var contactKey = contact.attr('data-contact-key');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'mcb_ajax_get_contact_type',
                        nonce: mobile_contact_bar.nonce,
                        contact_key: contactKey,
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

                    ['historyback', 'historyforward', 'scrolltotop'].includes(data.contact_type.type)
                        ? contact.find('.mcb-summary-uri').text('#')
                        : contact.find('.mcb-summary-uri').text('(no URI)');
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
                    ti_path = mobile_contact_bar.page_url + 'assets/icons/ti/tabler-sprite.svg',
                    fa_path = mobile_contact_bar.page_url + 'assets/icons/fa/sprites/',
                    ti_filtered_icons = [],
                    fa_filtered_icons = [],
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

                fa_filtered_icons = filtered_icons(fa_icons, searchTerm);
                ti_filtered_icons = filtered_icons(ti_icons, searchTerm);

                // Change brand
                $('body')
                    .off('click', '#mcb-icon-picker-container button')
                    .on('click', '#mcb-icon-picker-container button', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        $('#mcb-icon-picker-container').find('button').removeClass('mcb-icon-brand-active');
                        $(this).addClass('mcb-icon-brand-active');

                        if ('ti' === $(this).attr('data-brand')) {
                            ti_update_picker_window(iconList, ti_path, ti_filtered_icons, 0);
                        } else {
                            fa_update_picker_window(iconList, fa_path, fa_filtered_icons, 0);
                        }
                    });

                // Select an icon
                $('body')
                    .off('click', '#mcb-icon-picker-container ul li a')
                    .on('click', '#mcb-icon-picker-container ul li a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $('#mcb-icon-picker-container').find('button.mcb-icon-brand-active').attr('data-brand');
                        var icon = $(this).closest('li').attr('data-icon');
                        var names = 'fa' === brand ? icon.split(' ') : ['', icon];

                        $.ajax({
                            url: ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'mcb_ajax_get_icon',
                                nonce: mobile_contact_bar.nonce,
                                brand: brand,
                                group: names[0],
                                icon: names[1]
                            }
                        }).done(function (response) {
                            if (!response) {
                                return false;
                            }
                            var svg = JSON.parse(response);
                            if (svg.length <= 0) {
                                contact
                                    .find('input[name$="[brand]"]')
                                    .val('')
                                    .end()
                                    .find('input[name$="[group]"]')
                                    .val('')
                                    .end()
                                    .find('input[name$="[icon]"]')
                                    .val('')
                                    .end()
                                    .find('.mcb-summary-icon')
                                    .removeClass('mcb-fa')
                                    .addClass('mcb-blank-icon')
                                    .text('--')
                                    .end()
                                    .find('.mcb-details-icon span')
                                    .removeClass('mcb-fa')
                                    .addClass('mcb-blank-icon')
                                    .text('--');
                                return false;
                            }

                            contact
                                .find('input[name$="[brand]"]')
                                .val(brand)
                                .end()
                                .find('input[name$="[group]"]')
                                .val(names[0])
                                .end()
                                .find('input[name$="[icon]"]')
                                .val(names[1])
                                .end()
                                .find('.mcb-summary-icon')
                                .removeClass(['mcb-blank-icon', 'mcb-fa'])
                                .empty()
                                .append(svg)
                                .end()
                                .find('.mcb-details-icon span')
                                .removeClass(['mcb-blank-icon', 'mcb-fa'])
                                .empty()
                                .append(svg);

                            if ('fa' === brand) {
                                contact
                                    .find('.mcb-summary-icon')
                                    .addClass('mcb-fa')
                                    .end()
                                    .find('.mcb-details-icon span')
                                    .addClass('mcb-fa');
                            }
                        });

                        $('#mcb-icon-picker-container').remove();
                    });

                // Paginate icons
                $('body')
                    .off('click', '#mcb-icon-picker-container div a')
                    .on('click', '#mcb-icon-picker-container div a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $('#mcb-icon-picker-container').find('button.mcb-icon-brand-active').attr('data-brand');

                        if ('ti' === brand) {
                            if ('back' === $(this).attr('data-direction')) {
                                circular_window_backward(iconList, ti_path, ti_filtered_icons, ti_update_picker_window);
                            } else {
                                circular_window_forward(iconList, ti_path, ti_filtered_icons, ti_update_picker_window);
                            }
                        } else if ('fa' === brand) {
                            if ('back' === $(this).attr('data-direction')) {
                                circular_window_backward(iconList, fa_path, fa_filtered_icons, fa_update_picker_window);
                            } else {
                                circular_window_forward(iconList, fa_path, fa_filtered_icons, fa_update_picker_window);
                            }
                        }
                    });

                // Search icons
                $('body')
                    .off('input', '#mcb-icon-picker-container div input')
                    .on('input', '#mcb-icon-picker-container div input', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $('#mcb-icon-picker-container').find('button.mcb-icon-brand-active').attr('data-brand');

                        searchTerm = $(this).val();
                        ti_filtered_icons = filtered_icons(ti_icons, searchTerm);
                        fa_filtered_icons = filtered_icons(fa_icons, searchTerm);

                        if ('ti' === brand) {
                            ti_update_picker_window(iconList, ti_path, ti_filtered_icons, 0);
                        } else if ('fa' === brand) {
                            fa_update_picker_window(iconList, fa_path, fa_filtered_icons, 0);
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
                    .removeClass('mcb-fa')
                    .addClass('mcb-blank-icon')
                    .text('--')
                    .end()
                    .find('.mcb-details-icon span')
                    .removeClass('mcb-fa')
                    .addClass('mcb-blank-icon')
                    .text('--');
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
            option.contactList.on('click', '.mcb-action-add-parameter', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var contact = $(this).closest('.mcb-contact');
                var parameters = contact.find('.mcb-custom-parameter');

                var contactKey = contact.attr('data-contact-key'),
                    parameterKey = parameters.maxKey('parameter') + 1;

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'mcb_ajax_get_parameter',
                        nonce: mobile_contact_bar.nonce,
                        contact_key: contactKey,
                        parameter_key: parameterKey
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
