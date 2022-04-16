/*!
 * Mobile Contact Bar 3.0.0 by Anna Bansaghi - https://mobilecontactbar.com
 * License GPLv3 - https://www.gnu.org/licenses/gpl-3.0.en.html
 */

/* global isRtl, ajaxurl, pagenow, postboxes, mcb */

(function ($, window, document) {
    'use strict';

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

    $.fn.closeAllButtons = function (currentButtonKey) {
        document.activeElement.blur();

        var buttons = this.find(`.mcb-button[data-button-key!="${currentButtonKey}"]`);

        buttons
            .removeClass('mcb-opened')
            .find('.mcb-action-toggle-details, .mcb-action-toggle-query, .mcb-action-toggle-customization')
            .attr('aria-expanded', 'false')
            .end()
            .find('.mcb-details, .mcb-query, .mcb-customization')
            .addClass('mcb-hidden');
    };

    $.fn.initSortableButtons = function () {
        $(this).sortable({
            connectWith: '#mcb-builder',
            handle: '.mcb-sortable-draggable',
            items: '.mcb-button',

            start: function (event, ui) {
                $(this).closeAllButtons();

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

    $.fn.blankIcon = function () {
        this.find('input[name$="[brand]"]')
            .val('')
            .end()
            .find('input[name$="[group]"]')
            .val('')
            .end()
            .find('input[name$="[icon]"]')
            .val('')
            .end()
            .find('.mcb-summary-brand')
            .addClass('mcb-blank-icon')
            .text('--')
            .end()
            .find('.mcb-summary-icon')
            .removeClass('mcb-fa')
            .addClass('mcb-blank-icon')
            .text('--')
            .end()
            .find('.mcb-details-brand')
            .addClass('mcb-blank-icon')
            .text('--')
            .end()
            .find('.mcb-details-icon')
            .removeClass('mcb-fa')
            .addClass('mcb-blank-icon')
            .text('--');
    };

    $.fn.loadingIcon = function () {
        this.find('input[name$="[brand]"]')
            .val('')
            .end()
            .find('input[name$="[group]"]')
            .val('')
            .end()
            .find('input[name$="[icon]"]')
            .val('')
            .end()
            .find('.mcb-summary-brand')
            .addClass('mcb-blank-icon')
            .text('--')
            .end()
            .find('.mcb-summary-icon')
            .removeClass('mcb-blank-icon')
            .addClass('mcb-loading-icon')
            .empty()
            .end()
            .find('.mcb-details-brand')
            .addClass('mcb-blank-icon')
            .text('--')
            .end()
            .find('.mcb-details-icon')
            .removeClass('mcb-blank-icon')
            .addClass('mcb-loading-icon')
            .empty();
    };

    var option = {
        init: function () {
            // Bind toggle child-settings
            option.settings = $('#mcb-section-bar tbody, #mcb-section-buttons tbody, #mcb-section-toggle tbody, #mcb-section-badge tbody');
            option.settings.initSettings();

            // Init button builder
            option.builder = $('#mcb-builder');
            option.builder.initSortableButtons();

            // Bind save-to-database on toggle postbox
            postboxes.add_postbox_toggles(pagenow);

            // Close button details on Button Builder meta box closed
            postboxes.pbhide = function (id) {
                if ('mcb-meta-box-builder' === id) {
                    option.builder.closeAllButtons();
                }
            };

            // Generate icon lists
            option.ti_icons = mcb.ti_icons;
            option.fa_icons = [];
            $.each(mcb.fa_icons, function (section, icons) {
                $.each(icons, function (index, icon) {
                    option.fa_icons.push(section + ' ' + icon);
                });
            });

            option.onReady();
        },

        onReady: function () {
            // Update iframe
            // $('#mcb-section-preview iframe').on('load', function () {
            //     // var iframe = $(this).contents();
            //     // var css = iframe.find('#mobile-contact-bar-css');
            //     // css.attr('href', css.attr('href') + '#' + new Date().getTime());
            //     // iframe.attr('src', iframe.attr('src'));
            //     // iframe.find('html').css({ 'pointer-events': 'none' });
            //     // iframe.find('body').css({ 'pointer-events': 'none' });
            //     // iframe.find('#mobile-contact-bar').css({ 'pointer-events': 'all' });
            // });

            // Add loading indicator to the form submit button
            $('#mcb-form').submit(function () {
                $('#submit').addClass('mcb-loading');
            });

            // Highlight checked button
            // Update badge-length
            option.builder.on('change', '.mcb-summary-checkbox input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (this.checked) {
                    $(this).closest('.mcb-button').addClass('mcb-checked');
                } else {
                    $(this).closest('.mcb-button').removeClass('mcb-checked');
                }

                var checked_buttons_length = option.builder.find('.mcb-checked').length;
                0 === checked_buttons_length
                    ? $('#mcb-badge-length').removeClass().addClass('mcb-badge-disabled').text(0)
                    : $('#mcb-badge-length').removeClass().addClass('mcb-badge-enabled').text(checked_buttons_length);
            });

            // Update badge-display
            $('#mcb-bar-device').on('change', 'input', function () {
                'mcb-bar-device--none' === $(this).attr('id')
                    ? $('#mcb-badge-display').removeClass().addClass('mcb-badge-disabled').text(mcb.l10n.disabled)
                    : $('#mcb-badge-display').removeClass().addClass('mcb-badge-enabled').text(mcb.l10n.enabled);
            });

            // Slider value
            $('.mcb-settings').on('input change', '.mcb-slider-input', function () {
                $(this)
                    .next('span')
                    .html(this.value + ' ' + $(this).data('postfix'));
            });

            // Close color picker on ESC
            option.settings.on('keydown', function (event) {
                if (27 !== event.which) {
                    return;
                }

                var pickerContainers = option.settings.find('.wp-picker-container');

                pickerContainers.each(function () {
                    if ($(this).hasClass('wp-picker-active')) {
                        $(this).find('.color-picker').wpColorPicker('close');
                        $(this).find('.wp-color-result').focus();
                    }
                });
            });

            // Add button
            $('#mcb-add-button').click(function (event) {
                event.preventDefault();
                event.stopPropagation();

                var buttonKey = option.builder.children('.mcb-button').maxKey('button') + 1;

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'mcb_ajax_get_button',
                        nonce: mcb.nonce,
                        button_key: buttonKey
                    },

                    beforeSend: function () {
                        $('#mcb-add-button').addClass('mcb-loading');
                        option.builder.closeAllButtons();
                    },

                    complete: function () {
                        $('#mcb-add-button').removeClass('mcb-loading');
                    }
                })
                    .done(function (response) {
                        if (!response) {
                            return false;
                        }
                        var data = JSON.parse(response);
                        if (
                            !data.hasOwnProperty('summary') ||
                            !data.hasOwnProperty('details') ||
                            !data.hasOwnProperty('query') ||
                            !data.hasOwnProperty('customization')
                        ) {
                            return false;
                        }

                        var button = document.createElement('div');
                        $(button).addClass(['mcb-button', 'mcb-opened']).attr('data-button-key', buttonKey);
                        $(button).append($(data.summary)).append($(data.details)).append($(data.query)).append($(data.customization));

                        $(button).find('.color-picker').wpColorPicker();
                        $(button)
                            .find('.mcb-action-toggle-details')
                            .attr('aria-expanded', 'true')
                            .end()
                            .find('.mcb-details')
                            .removeClass('mcb-hidden');

                        option.builder.append(button);
                    })
                    .always(function () {
                        $('#mcb-add-button').removeClass('mcb-loading');
                    });
            });

            // Order higher
            option.builder.on('click', '.mcb-action-order-higher', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var focused = document.activeElement;
                var prev = $(this).closest('.mcb-button').prev();
                var button = $(this).closest('.mcb-button').detach();

                prev.before(button);
                focused.focus();
            });

            // Order lower
            option.builder.on('click', '.mcb-action-order-lower', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var focused = document.activeElement;
                var next = $(this).closest('.mcb-button').next();
                var button = $(this).closest('.mcb-button').detach();

                next.after(button);
                focused.focus();
            });

            // Delete button
            // Update badge-length
            option.builder.on('click', '.mcb-action-delete-button', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $(this).closest('.mcb-button').remove();

                var checked_buttons_length = option.builder.find('.mcb-checked').length;
                0 === checked_buttons_length
                    ? $('#mcb-badge-length').removeClass().addClass('mcb-badge-disabled').text(0)
                    : $('#mcb-badge-length').removeClass().addClass('mcb-badge-enabled').text(checked_buttons_length);
            });

            // Toggle details
            option.builder.on('click', '.mcb-action-toggle-details', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var button = $(this).closest('.mcb-button');

                option.builder.closeAllButtons(button.attr('data-button-key'));

                $(this).toggleAriaExpanded();

                button
                    .toggleClass('mcb-opened', $(this).attr('aria-expanded') === 'true')
                    .find('.mcb-details')
                    .toggleClass('mcb-hidden')
                    .end()
                    .find('.mcb-action-toggle-query, .mcb-action-toggle-customization')
                    .attr('aria-expanded', false)
                    .end()
                    .find('.mcb-query, .mcb-customization')
                    .addClass('mcb-hidden');
            });

            // Toggle query
            option.builder.on('click', '.mcb-action-toggle-query', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var button = $(this).closest('.mcb-button');

                option.builder.closeAllButtons(button.attr('data-button-key'));

                $(this).toggleAriaExpanded();

                button
                    .toggleClass('mcb-opened', $(this).attr('aria-expanded') === 'true')
                    .find('.mcb-query')
                    .toggleClass('mcb-hidden')
                    .end()
                    .find('.mcb-action-toggle-details, .mcb-action-toggle-customization')
                    .attr('aria-expanded', false)
                    .end()
                    .find('.mcb-details, .mcb-customization')
                    .addClass('mcb-hidden');
            });

            // Toggle customization
            option.builder.on('click', '.mcb-action-toggle-customization', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var button = $(this).closest('.mcb-button');

                option.builder.closeAllButtons(button.attr('data-button-key'));

                $(this).toggleAriaExpanded();

                button
                    .toggleClass('mcb-opened', $(this).attr('aria-expanded') === 'true')
                    .find('.mcb-customization')
                    .toggleClass('mcb-hidden')
                    .end()
                    .find('.mcb-action-toggle-details, .mcb-action-toggle-query')
                    .attr('aria-expanded', false)
                    .end()
                    .find('.mcb-details, .mcb-query')
                    .addClass('mcb-hidden');
            });

            // Change button type
            option.builder.on('change', '.mcb-details-type select', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var input = $(this);
                var button = $(this).closest('.mcb-button');
                var buttonKey = button.attr('data-button-key');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'mcb_ajax_get_button_field',
                        nonce: mcb.nonce,
                        button_key: buttonKey,
                        button_type: $(this).val()
                    },

                    beforeSend: function () {
                        input.addClass('mcb-loading');
                    },

                    complete: function () {
                        input.removeClass('mcb-loading');
                    }
                })
                    .done(function (response) {
                        if (!response) {
                            return false;
                        }
                        var data = JSON.parse(response);
                        if (!data.hasOwnProperty('button_field') || !data.hasOwnProperty('uri') || !data.hasOwnProperty('query')) {
                            return false;
                        }

                        [('historyback', 'historyforward', 'scrolltotop')].includes(data.button_field.type)
                            ? button.find('.mcb-summary-uri').text('#')
                            : button.find('.mcb-summary-uri').text(!!data.button_field.uri ? data.button_field.uri : mcb.l10n.no_URI);

                        button.find('.mcb-details-text input').val(data.button_field.text);
                        button.find('.mcb-details-uri').replaceWith($(data.uri));
                        button.find('.mcb-details-type .mcb-description').text(data.button_field.desc_type);

                        button.find('.mcb-query').detach();
                        button.find('.mcb-action-toggle-query').toggleClass('mcb-disabled', data.query === '');
                        button.find('.mcb-details').after($(data.query));
                    })
                    .always(function () {
                        input.removeClass('mcb-loading');
                    });
            });

            // Pick icon
            option.builder.on('click', '.mcb-action-pick-icon', function (event) {
                event.preventDefault();
                event.stopPropagation();

                setTimeout(function () {
                    $('#mcb-icon-picker-container div input').focus();
                }, 100);

                var iconList,
                    ti_path = mcb.plugin_url + 'assets/svg/ti/tabler-sprite.svg',
                    fa_path = mcb.plugin_url + 'assets/svg/fa/sprites/',
                    ti_filtered_icons = [],
                    fa_filtered_icons = [],
                    searchTerm = '',
                    clickedButton = $(this),
                    offset = clickedButton.offset(),
                    button = $(this).closest('.mcb-button'),
                    picker = $(
                        $.parseHTML(
                            $('#mcb-tmpl-icon-picker')
                                .html()
                                .replace(/\s{2,}/g, '')
                        )
                    );

                picker
                    .css({ top: offset.top - 15, left: offset.left - (isRtl ? 185 : 0) })
                    .appendTo('body')
                    .show();

                iconList = $('#mcb-icon-picker-container ul');

                fa_filtered_icons = filtered_icons(option.fa_icons, searchTerm);
                ti_filtered_icons = filtered_icons(option.ti_icons, searchTerm);

                // Change brand
                $('body')
                    .off('click', '#mcb-icon-picker-container button')
                    .on('click', '#mcb-icon-picker-container button', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $(this).attr('data-brand');

                        $('#mcb-icon-picker-container').find('button').removeClass('mcb-brand-active');
                        $(this).addClass('mcb-brand-active');

                        if ('ti' === brand) {
                            ti_update_picker_window(iconList, ti_path, ti_filtered_icons, 0);
                        } else if ('fa' === brand) {
                            fa_update_picker_window(iconList, fa_path, fa_filtered_icons, 0);
                        } else {
                            $('#mcb-icon-picker-container').find('button').removeClass('mcb-brand-active');
                            $('#mcb-icon-picker-container').find('button[data-brand="fa"]').addClass('mcb-brand-active');
                            fa_update_picker_window(iconList, fa_path, fa_filtered_icons, 0);
                        }
                    });

                // Select an icon
                $('body')
                    .off('click', '#mcb-icon-picker-container ul li a')
                    .on('click', '#mcb-icon-picker-container ul li a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $('#mcb-icon-picker-container').find('button.mcb-brand-active').attr('data-brand');
                        var icon = $(this).closest('li').attr('data-icon');
                        var names = 'fa' === brand ? icon.split(' ') : ['', icon];

                        $('#mcb-icon-picker-container').remove();

                        if (
                            !['ti', 'fa'].includes(brand) ||
                            ('ti' === brand && !option.ti_icons.includes(icon)) ||
                            ('fa' === brand && !option.fa_icons.includes(icon))
                        ) {
                            button.blankIcon();
                            return false;
                        }

                        $.ajax({
                            url: ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'mcb_ajax_get_icon',
                                nonce: mcb.nonce,
                                brand: brand,
                                group: names[0],
                                icon: names[1]
                            },

                            beforeSend: function () {
                                clickedButton.addClass('mcb-loading');
                                button.loadingIcon();
                            },

                            complete: function () {
                                clickedButton.removeClass('mcb-loading');
                                button
                                    .find('.mcb-summary-icon')
                                    .removeClass('mcb-loading-icon')
                                    .end()
                                    .find('.mcb-details-icon')
                                    .removeClass('mcb-loading-icon');
                            }
                        })
                            .done(function (response) {
                                if (!response) {
                                    button.blankIcon();
                                    return false;
                                }
                                var svg = JSON.parse(response);
                                if (svg.length <= 0) {
                                    button.blankIcon();
                                    return false;
                                }

                                button
                                    .find('input[name$="[brand]"]')
                                    .val(brand)
                                    .end()
                                    .find('input[name$="[group]"]')
                                    .val(names[0])
                                    .end()
                                    .find('input[name$="[icon]"]')
                                    .val(names[1])
                                    .end()
                                    .find('.mcb-summary-brand')
                                    .removeClass('mcb-blank-icon')
                                    .text(brand.toUpperCase())
                                    .end()
                                    .find('.mcb-summary-icon')
                                    .removeClass(['mcb-blank-icon', 'mcb-fa'])
                                    .empty()
                                    .append(svg)
                                    .end()
                                    .find('.mcb-details-brand')
                                    .removeClass('mcb-blank-icon')
                                    .text(brand.toUpperCase())
                                    .end()
                                    .find('.mcb-details-icon')
                                    .removeClass(['mcb-blank-icon', 'mcb-fa'])
                                    .empty()
                                    .append(svg);

                                if ('fa' === brand) {
                                    button.find('.mcb-summary-icon').addClass('mcb-fa').end().find('.mcb-details-icon').addClass('mcb-fa');
                                }
                            })
                            .always(function () {
                                clickedButton.removeClass('mcb-loading');
                                button
                                    .find('.mcb-summary-icon')
                                    .removeClass('mcb-loading-icon')
                                    .end()
                                    .find('.mcb-details-icon')
                                    .removeClass('mcb-loading-icon');
                            });
                    });

                // Paginate icons
                $('body')
                    .off('click', '#mcb-icon-picker-container div a')
                    .on('click', '#mcb-icon-picker-container div a', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $('#mcb-icon-picker-container').find('button.mcb-brand-active').attr('data-brand');

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
                        } else {
                            $('#mcb-icon-picker-container').find('button').removeClass('mcb-brand-active');
                            $('#mcb-icon-picker-container').find('button[data-brand="fa"]').addClass('mcb-brand-active');
                            fa_update_picker_window(iconList, fa_path, fa_filtered_icons, 0);
                        }
                    });

                // Search icons
                $('body')
                    .off('input', '#mcb-icon-picker-container div input')
                    .on('input', '#mcb-icon-picker-container div input', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var brand = $('#mcb-icon-picker-container').find('button.mcb-brand-active').attr('data-brand');

                        searchTerm = $(this).val();
                        ti_filtered_icons = filtered_icons(option.ti_icons, searchTerm);
                        fa_filtered_icons = filtered_icons(option.fa_icons, searchTerm);

                        if ('ti' === brand) {
                            ti_update_picker_window(iconList, ti_path, ti_filtered_icons, 0);
                        } else if ('fa' === brand) {
                            fa_update_picker_window(iconList, fa_path, fa_filtered_icons, 0);
                        } else {
                            $('#mcb-icon-picker-container').find('button').removeClass('mcb-brand-active');
                            $('#mcb-icon-picker-container').find('button[data-brand="fa"]').addClass('mcb-brand-active');
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
            option.builder.on('click', '.mcb-action-clear-icon', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $(this).closest('.mcb-button').blankIcon();
            });

            // Update label
            option.builder.on('input', '.mcb-details-label input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var button = $(this).closest('.mcb-button');
                button.find('.mcb-summary-label').text($(this).val());
            });

            // Update URI
            option.builder.on('input', '.mcb-details-uri input', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var uri = $(this).val();
                var button = $(this).closest('.mcb-button');

                if ('' === uri) {
                    button.find('.mcb-summary-uri').removeClass('mcb-monospace').text(mcb.l10n.no_URI);
                } else {
                    button.find('.mcb-summary-uri').addClass('mcb-monospace').text(uri);
                }
            });

            // Add parameter
            option.builder.on('click', '.mcb-action-add-parameter', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var input = $(this);
                var button = $(this).closest('.mcb-button');
                var parameters = button.find('.mcb-link-parameter');

                var buttonKey = button.attr('data-button-key'),
                    parameterKey = parameters.maxKey('parameter') + 1;

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'mcb_ajax_get_parameter',
                        nonce: mcb.nonce,
                        button_key: buttonKey,
                        parameter_key: parameterKey
                    },

                    beforeSend: function () {
                        input.addClass('mcb-loading');
                    },

                    complete: function () {
                        input.removeClass('mcb-loading');
                    }
                })
                    .done(function (response) {
                        if (!response) {
                            return false;
                        }
                        var parameter = JSON.parse(response);
                        if (parameter.length <= 0) {
                            return false;
                        }

                        button.find('.mcb-link-query').after($(parameter));
                    })
                    .always(function () {
                        input.removeClass('mcb-loading');
                    });
            });

            // Delete parameter
            option.builder.on('click', '.mcb-action-delete-parameter', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $(this).closest('.mcb-link-parameter').remove();
            });
        }
    };

    $(document).ready(option.init);
})(jQuery, window, document);
