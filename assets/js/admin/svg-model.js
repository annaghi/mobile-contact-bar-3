(function ($, document) {
    'use strict';

    var model = {
        init: function () {
            var h = $('#mcb-bar-height').val(),
                w = $('#mcb-bar-width').val();

            h = h > 0 ? h / 2 : 0;
            w = w > 0 ? w * 2.6 : 0;

            $('#mcb-model-bar').attr('fill', $('#mcb-bar-color').val());
            $('#mcb-model-bar').attr('fill-opacity', $('#mcb-bar-opacity').val());
            $('#mcb-model-bar').attr('height', h);
            $('#mcb-model-bar').attr('width', w);

            $('#mcb-model-border-top').attr('width', w);
            $('#mcb-model-border-bottom').attr('width', w);
            $('#mcb-model-border-top').attr('fill', $('#mcb-bar-border_color').val());
            $('#mcb-model-border-bottom').attr('fill', $('#mcb-bar-border_color').val());

            $('#mcb-model-space').attr('height', $('#mcb-bar-space_height').val() > 0 ? $('#mcb-bar-space_height').val() / 2 : 0);

            $('#mcb-model-placeholder').attr('fill', $('#mcb-bar-placeholder_color').val());
            $('#mcb-model-placeholder').attr(
                'height',
                $('#mcb-bar-placeholder_height').val() > 0 ? $('#mcb-bar-placeholder_height').val() / 2 : 0
            );

            model.update_bar_group_x();
            model.update_bar_group_y();
            model.update_placeholder_y();
            model.update_borders_height();
            model.update_border_bottom_y();

            model.onReady();
        },

        onReady: function () {
            model.max = {
                canvas: 690,
                mobile: 315
            };

            model.min = {
                canvas: 25,
                mobile: 55
            };

            model.dragvars = {
                active: false,
                dmy: 0,
                dby: 0,
                dt: 0,
                db: 0
            };

            $('#mcb-model-mobile-draggable')
                .mousedown(function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var mgt = $('#mcb-model-mobile-group')
                            .attr('transform')
                            .match(/\w+\(([^,)]+),([^)]+)\)/),
                        vp = $('#mcb-bar-vertical_position input:checked').val(),
                        f = $('#mcb-bar-is_fixed').prop('checked'),
                        ph = $('#mcb-model-placeholder').attr('height'),
                        bgt;

                    model.dragvars.active = true;
                    model.dragvars.dmy = event.clientY - mgt[2];

                    if (f) {
                        bgt = $('#mcb-model-bar-group')
                            .attr('transform')
                            .match(/\w+\(([^,)]+),([^)]+)\)/);
                        model.dragvars.dby = event.clientY - bgt[2];
                    }

                    if ('top' == vp) {
                        model.dragvars.dt = -ph;
                        model.dragvars.db = 0;
                    } else if ('bottom' == vp) {
                        model.dragvars.dt = 0;
                        model.dragvars.db = -ph;
                    }
                })
                .mousemove(function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var y = event.clientY - model.dragvars.dmy;

                    if (model.dragvars.active && model.min.mobile + model.dragvars.dt <= y && y <= model.max.mobile - model.dragvars.db) {
                        var mgt = $('#mcb-model-mobile-group')
                                .attr('transform')
                                .match(/\w+\(([^,)]+),([^)]+)\)/),
                            bgt = $('#mcb-model-bar-group')
                                .attr('transform')
                                .match(/\w+\(([^,)]+),([^)]+)\)/),
                            f = $('#mcb-bar-is_fixed').prop('checked');

                        if (f) {
                            $('#mcb-model-bar-group').attr(
                                'transform',
                                'translate(' + bgt[1] + ',' + (event.clientY - model.dragvars.dby) + ')'
                            );
                        }
                        $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + y + ')');
                    }
                })
                .mouseup(function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    model.dragvars.active = false;
                })
                .mouseleave(function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    model.dragvars.active = false;
                });

            // Colors
            $('#mcb-bar-color').on('input change', function () {
                $('#mcb-model-bar').attr('fill', this.value);
            });

            $('#mcb-bar-opacity').on('input change', function () {
                $('#mcb-model-bar').attr('fill-opacity', this.value);
            });

            $('#mcb-bar-placeholder_color').on('input change', function () {
                $('#mcb-model-placeholder').attr('fill', this.value);
            });

            $('#mcb-bar-border_color').on('input change', function () {
                $('#mcb-model-border-top').attr('fill', this.value);
                $('#mcb-model-border-bottom').attr('fill', this.value);
            });

            // Heights
            $('#mcb-bar-height').on('input', function () {
                $('#mcb-model-bar').attr('height', this.value > 0 ? this.value / 2 : 0);
                model.update_bar_group_y();
                model.update_border_bottom_y();
            });

            $('#mcb-bar-placeholder_height').on('input', function () {
                $('#mcb-model-placeholder').attr('height', this.value > 0 ? this.value / 2 : 0);
                model.update_placeholder_y();
                model.update_bar_group_y();
                model.update_mobile_group_y();
            });

            $('#mcb-bar-space_height').on('input', model.update_bar_group_y);

            $('#mcb-bar-border_width').on('input', function () {
                model.update_borders_height();
                model.update_border_bottom_y();
            });

            // Width
            $('#mcb-bar-width').on('input', function () {
                var w = this.value > 0 ? this.value * 2.6 : 0;

                $('#mcb-model-bar').attr('width', w);
                $('#mcb-model-border-top').attr('width', w);
                $('#mcb-model-border-bottom').attr('width', w);
                model.update_bar_group_x();
            });

            // Positions
            $('#mcb-bar-is_fixed').on('change', model.update_bar_group_y);

            $('#mcb-bar-horizontal_position input').on('change', model.update_bar_group_x);

            $('#mcb-bar-vertical_position input').on('change', function () {
                var mgt = $('#mcb-model-mobile-group')
                    .attr('transform')
                    .match(/\w+\(([^,)]+),([^)]+)\)/);

                if (mgt[2] < model.min.mobile) {
                    mgt[2] = model.min.mobile;
                    $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + mgt[2] + ')');
                } else if (mgt[2] > model.max.mobile) {
                    mgt[2] = model.max.mobile;
                    $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + mgt[2] + ')');
                }
                model.update_placeholder_y();
                model.update_bar_group_y();
                model.update_mobile_group_y();
                model.update_borders_height();
            });

            // Borders
            $('#mcb-bar-is_border input').on('change', model.update_borders_height);
        },

        update_bar_group_x: function () {
            var hp = $('#mcb-bar-horizontal_position input:checked').val(),
                w = $('#mcb-model-bar').attr('width'),
                bgt = $('#mcb-model-bar-group')
                    .attr('transform')
                    .match(/\w+\(([^,)]+),([^)]+)\)/);

            if ('left' == hp) {
                $('#mcb-model-bar-group').attr('transform', 'translate(25,' + bgt[2] + ')');
            } else if ('center' == hp) {
                $('#mcb-model-bar-group').attr('transform', 'translate(' + ((260 - w) / 2 + 25) + ',' + bgt[2] + ')');
            } else if ('right' == hp) {
                $('#mcb-model-bar-group').attr('transform', 'translate(' + (260 - w + 25) + ',' + bgt[2] + ')');
            }
        },

        update_bar_group_y: function () {
            var vp = $('#mcb-bar-vertical_position input:checked').val(),
                f = $('#mcb-bar-is_fixed').prop('checked'),
                h = $('#mcb-model-bar').attr('height'),
                sh = $('#mcb-bar-space_height').val() > 0 ? $('#mcb-bar-space_height').val() / 2 : 0,
                ph = $('#mcb-model-placeholder').attr('height'),
                bgt = $('#mcb-model-bar-group')
                    .attr('transform')
                    .match(/\w+\(([^,)]+),([^)]+)\)/),
                mgt;

            if (f) {
                mgt = $('#mcb-model-mobile-group')
                    .attr('transform')
                    .match(/\w+\(([^,)]+),([^)]+)\)/);

                if ('top' == vp) {
                    $('#mcb-model-bar-group').attr('transform', 'translate(' + bgt[1] + ',' + (25 + +mgt[2] + sh) + ')');
                } else if ('bottom' == vp) {
                    $('#mcb-model-bar-group').attr('transform', 'translate(' + bgt[1] + ',' + (375 + +mgt[2] - sh - h) + ')');
                }
            } else {
                if ('top' == vp) {
                    $('#mcb-model-bar-group').attr('transform', 'translate(' + bgt[1] + ',' + (80 - ph + sh) + ')');
                } else if ('bottom' == vp) {
                    $('#mcb-model-bar-group').attr('transform', 'translate(' + bgt[1] + ',' + (690 + +ph - sh - h) + ')');
                }
            }
        },

        update_mobile_group_y: function () {
            var vp = $('#mcb-bar-vertical_position input:checked').val(),
                f = $('#mcb-bar-is_fixed').prop('checked'),
                h = $('#mcb-model-bar').attr('height'),
                sh = $('#mcb-bar-space_height').val() > 0 ? $('#mcb-bar-space_height').val() / 2 : 0,
                ph = $('#mcb-model-placeholder').attr('height'),
                mgt = $('#mcb-model-mobile-group')
                    .attr('transform')
                    .match(/\w+\(([^,)]+),([^)]+)\)/),
                bgt = $('#mcb-model-bar-group')
                    .attr('transform')
                    .match(/\w+\(([^,)]+),([^)]+)\)/);

            if ('top' == vp && mgt[2] < model.min.mobile) {
                mgt[2] = model.min.mobile;
                $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + (mgt[2] - ph) + ')');

                if (f) {
                    bgt[2] = 80 + sh;
                    $('#mcb-model-bar-group').attr('transform', 'translate(' + bgt[1] + ',' + (bgt[2] - ph) + ')');
                }
            } else if ('bottom' == vp && mgt[2] > model.max.mobile) {
                mgt[2] = model.max.mobile;
                $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + (mgt[2] + +ph) + ')');

                if (f) {
                    bgt[2] = 690 - h - sh;
                    $('#mcb-model-bar-group').attr('transform', 'translate(' + bgt[1] + ',' + (bgt[2] + +ph) + ')');
                }
            }
        },

        update_placeholder_y: function () {
            var vp = $('#mcb-bar-vertical_position input:checked').val(),
                ph;

            if ('top' == vp) {
                ph = $('#mcb-model-placeholder').attr('height');
                $('#mcb-model-placeholder').attr('y', 80 - ph);
            } else if ('bottom' == vp) {
                $('#mcb-model-placeholder').attr('y', 690);
            }
        },

        update_border_bottom_y: function () {
            var h = $('#mcb-model-bar').attr('height'),
                bbh = $('#mcb-model-border-bottom').attr('height');

            $('#mcb-model-border-bottom').attr('y', h - bbh);
        },

        update_borders_height: function () {
            var b = $('#mcb-bar-is_border input:checked').val(),
                vp = $('#mcb-bar-vertical_position input:checked').val(),
                bw = $('#mcb-bar-border_width').val() > 0 ? $('#mcb-bar-border_width').val() / 2 : 0;

            if ('one' == b) {
                if ('top' == vp) {
                    $('#mcb-model-border-top').attr('height', 0);
                    $('#mcb-model-border-bottom').attr('height', bw);
                } else if ('bottom' == vp) {
                    $('#mcb-model-border-top').attr('height', bw);
                    $('#mcb-model-border-bottom').attr('height', 0);
                }
            } else if ('two' == b) {
                $('#mcb-model-border-bottom').attr('height', bw);
                $('#mcb-model-border-top').attr('height', bw);
            } else {
                $('#mcb-model-border-top').attr('height', 0);
                $('#mcb-model-border-bottom').attr('height', 0);
            }
        }
    };

    $(document).ready(model.init);
})(jQuery, document);
