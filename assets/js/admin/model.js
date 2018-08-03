/* global mcb */

(function ($, document) {
  'use strict'

  var model = {

    init: function () {
      model.update_toggle_x()

      model.canvas = {
        min: 150,
        max: 1350
      }

      model.mobile = {
        minIn: 100,
        maxIn: 600,
        minOut: 50,
        maxOut: 750
      }

      mcb.hook.register('onStopSortingContactList', function (args) {
        var cs = $('.mcb-contact-checkbox input:checked')
        var lis = $('#mobile-contact-bar-outer li')
        var e

        for (var i = 0; i < cs.length; ++i) {
          e = $(cs[i]).closest('ul').find('.fa-stack').children().clone()
          $(lis.get(i)).find('.fa-stack').empty().append(e)
        }

        lis.find('i').removeClass('fa-lg').addClass('fa-fw')
        lis.find('.mcb-badge').removeClass().addClass('mobile-contact-bar-badge')
        model.update_badges()
      })

      mcb.hook.register('onSelectIcon', function (args) {
        $('#mobile-contact-bar-outer li:eq(' + args.index + ')').find('i').removeClass().addClass(args.icon + ' fa-fw')
      })

      mcb.hook.register('onDeleteContact', function (args) {
        $('#mobile-contact-bar-outer li:eq(' + args + ')').remove()
        model.update_icons_width()
        model.update_icons_borders()
      })

      model.onReady()
    },

    onReady: function () {
      model.dragvars = {
        active: false,
        dmy: 0,
        dt: 0,
        db: 0
      }

      $('#mcb-model-mobile-draggable').mousedown(function (event) {
        event.preventDefault()
        event.stopPropagation()

        var ph = $('#mcb-model-placeholder').attr('height')
        var vp = $('#mcb-bar-vertical_position input:checked').val()

        model.dragvars.mgt = $('#mcb-model-mobile-group').attr('transform').match(/\w+\(([^,)]+),([^)]+)\)/)
        model.dragvars.active = true
        model.dragvars.dmy = event.clientY - model.dragvars.mgt[2]

        if (vp === 'top') {
          model.dragvars.dt = -ph
          model.dragvars.db = 0
        } else if (vp === 'bottom') {
          model.dragvars.dt = 0
          model.dragvars.db = -ph
        }
      }).mousemove(function (event) {
        event.preventDefault()
        event.stopPropagation()

        var y = event.clientY - model.dragvars.dmy

        if (model.dragvars.active && (model.mobile.minIn + model.dragvars.dt <= y && y <= model.mobile.maxIn - model.dragvars.db)) {
          $('#mcb-model-mobile-group').attr('transform', 'translate(' + model.dragvars.mgt[1] + ',' + y + ')')
        }
      }).mouseup(function (event) {
        event.preventDefault()
        event.stopPropagation()
        model.dragvars.active = false
      }).mouseleave(function (event) {
        event.preventDefault()
        event.stopPropagation()
        model.dragvars.active = false
      })

      /* Bar options */

      $('#mcb-bar-color').on('input change', function () {
        $('#mobile-contact-bar-outer').css('background-color', this.value)
      })

      $('#mcb-bar-opacity').on('input change', function () {
        $('#mobile-contact-bar').css('opacity', this.value)
      })

      $('#mcb-bar-height').on('input', function () {
        var h = (this.value > 0) ? this.value : 0
        var t = $('#mcb-toggle-is_render').prop('checked')

        if (t) {
          $('#mcb-model-bar').attr('height', +h + 34)
        } else {
          $('#mcb-model-bar').attr('height', h)
        }

        $('#mobile-contact-bar-outer').css('height', h)
        model.update_bar_li_height()
        model.update_bar_y()
        model.update_toggle_y()
      })

      $('#mcb-bar-width').on('input', function () {
        var w = (this.value > 100) ? 100 : this.value
        $('#mobile-contact-bar').css('width', w + '%')
        $('#mobile-contact-bar-toggle').css('width', w + '%')

        model.update_toggle_x()
      })

      $('#mcb-bar-horizontal_position input').on('change', function () {
        model.update_bar_x()
        model.update_toggle_x()
      })

      $('#mcb-bar-vertical_position input').on('change', function () {
        var mgt = $('#mcb-model-mobile-group').attr('transform').match(/\w+\(([^,)]+),([^)]+)\)/)
        if (mgt[2] < model.mobile.minIn) {
          mgt[2] = model.mobile.minIn
          $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + mgt[2] + ')')
        } else if (mgt[2] > model.mobile.maxIn) {
          mgt[2] = model.mobile.maxIn
          $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + mgt[2] + ')')
        }

        model.update_bar_y()
        model.update_model_placeholder_y()
        model.update_bar_borders()
        model.update_toggle()
      })

      $('#mcb-bar-is_fixed').on('change', function () {
        var vp = $('#mcb-bar-vertical_position input:checked').val()

        model.update_toggle()

        if (this.checked) {
          $('#mcb-model-toggle').css('display', 'block')
          if (vp === 'top') {
            $('#mcb-model-bar').detach().insertAfter('#mcb-model-toggle')
          } else if (vp === 'bottom') {
            $('#mcb-model-bar').detach().insertBefore('#mcb-model-toggle')
          }
        } else {
          $('#mcb-model-toggle').css('display', 'none')
          $('#mcb-model-bar').detach().insertBefore('#mcb-model-mobile-group')
        }

        model.update_bar_y()
      })

      $('#mcb-bar-space_height').on('input', function () {
        model.update_bar_y()
        model.update_toggle_y()
      })

      $('#mcb-bar-placeholder_height').on('input', function () {
        var ph = (this.value > 0) ? this.value : 0
        $('#mcb-model-placeholder').attr('height', ph)

        model.update_model_placeholder_y()
        model.update_bar_y()

        var mgt = $('#mcb-model-mobile-group').attr('transform').match(/\w+\(([^,)]+),([^)]+)\)/)
        var min = model.mobile.minIn - ph
        var max = model.mobile.maxIn + +ph

        if (mgt[2] < min) {
          $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + min + ')')
        } else if (mgt[2] > max) {
          $('#mcb-model-mobile-group').attr('transform', 'translate(' + mgt[1] + ',' + max + ')')
        }
      })

      $('#mcb-bar-placeholder_color').on('input change', function () {
        $('#mcb-model-placeholder').attr('fill', this.value)
      })

      $('#mcb-bar-is_border input').on('change', function () {
        model.update_bar_borders()
        model.update_bar_li_height()
      })

      $('#mcb-bar-border_color').on('input change', function () {
        model.update_bar_borders()
      })

      $('#mcb-bar-border_width').on('input', function () {
        model.update_bar_borders()
        model.update_bar_li_height()
      })

      /* Icons options */

      $('#mcb-icons-size').on('change', function () {
        $('.mobile-contact-bar-fa-stack')
          .removeClass()
          .addClass('mobile-contact-bar-fa-stack fa-stack fa-' + this.value)
      })

      $('#mcb-icons-color').on('input change', function () {
        $('#mobile-contact-bar ul li a').css('color', this.value)
        $('#mobile-contact-bar-toggle span').css('color', this.value)
      })

      $('#mcb-icons-alignment input').on('change', function () {
        model.update_icons_width()
      })

      $('#mcb-icons-width').on('input', function () {
        model.update_icons_width()
      })

      $('#mcb-icons-is_border input').on('change', function () {
        model.update_icons_borders()
      })

      $('#mcb-icons-border_color').on('input change', function () {
        model.update_icons_borders()
      })

      $('#mcb-icons-border_width').on('input', function () {
        model.update_icons_borders()
      })

      /* Badges options */

      $('#mcb-badges-background_color').on('input change', function () {
        $('.mobile-contact-bar-badge').css('background-color', this.value)
      })

      $('#mcb-badges-font_color').on('input change', function () {
        $('.mobile-contact-bar-badge').css('color', this.value)
      })

      $('#mcb-badges-size').on('input change', function () {
        $('.mobile-contact-bar-badge').css('font-size', this.value + 'em')
      })

      $('#mcb-badges-corner input').on('change', function () {
        model.update_badges_corner()
      })

      /* Toggle options */

      $('#mcb-toggle-is_render').on('change', function () {
        if (this.checked) {
          $('#mcb-model-toggle').css('display', 'block')
        } else {
          $('#mcb-model-toggle').css('display', 'none')
        }
      })

      $('#mcb-toggle-shape input').on('change', function () {
        model.update_toggle_shape()
      })

      $('#mcb-toggle-color').on('input change', function () {
        $('#mobile-contact-bar-toggle svg').css('fill', this.value)
      })

      $('#mcb-toggle-label').on('input', function () {
        $('#mobile-contact-bar-toggle span').text(this.value)
      })

      $('#mcb-toggle-size').on('input change', function () {
        $('#mobile-contact-bar-toggle span').css('font-size', this.value + 'rem')
      })

      /* Contact list */

      $('#mcb-table-contacts').on('change', '.mcb-contact-checkbox input', function () {
        if (this.checked) {
          var index = $('.mcb-contact-checkbox input:checked').index(this)
          var l = $('#mobile-contact-bar-outer li:eq(0)').clone()
          var e = $(this).closest('ul').find('.fa-stack').children().clone()

          l.find('.fa-stack').empty().append(e)
          l.find('i').removeClass('fa-lg').addClass('fa-fw')
          l.find('.mcb-badge').removeClass().addClass('mobile-contact-bar-badge')

          if (index === 0) {
            $('#mobile-contact-bar-outer li:eq(0)').before(l)
          } else {
            $('#mobile-contact-bar-outer li:eq(' + (index - 1) + ')').after(l)
          }
        } else {
          var cs = $('.mcb-contact-checkbox input:checked')
          var lis = $('#mobile-contact-bar-outer li')
          var cc = []
          var n, c

          for (var i = 0; i < cs.length; ++i) {
            n = cs[i].name.replace('checked', 'icon')
            cc.push($('input[name="' + n + '"]').val())
          }

          for (var j = 0; j < lis.length; ++j) {
            c = $(lis[j]).find('i').removeClass('fa-fw').attr('class')
            if (c !== cc[j]) {
              $(lis[j]).remove()
              break
            }
          }

          lis.find('i').addClass('fa-fw')
        }
        model.update_icons_borders()
        model.update_icons_width()
        model.update_badges()
      })
    },

    update_bar_y: function () {
      var f = $('#mcb-bar-is_fixed').prop('checked')
      var vp = $('#mcb-bar-vertical_position input:checked').val()
      var h = $('#mcb-bar-height').val()
      var ph = $('#mcb-model-placeholder').attr('height')
      var sh = ($('#mcb-bar-space_height').val() > 0) ? $('#mcb-bar-space_height').val() : 0

      if (f) {
        if (vp === 'top') {
          $('#mcb-model-bar').attr('y', model.mobile.minOut + +sh)
        } else if (vp === 'bottom') {
          $('#mcb-model-bar').attr('y', model.mobile.maxOut - h - sh)
        }
      } else {
        if (vp === 'top') {
          $('#mcb-model-bar').attr('y', model.canvas.min - ph + +sh)
        } else if (vp === 'bottom') {
          $('#mcb-model-bar').attr('y', model.canvas.max - h + +ph - sh)
        }
      }
    },

    update_bar_x: function () {
      var hp = $('#mcb-bar-horizontal_position input:checked').val()

      switch (hp) {
        case 'left':
          $('#mobile-contact-bar').css('left', '0')
          $('#mobile-contact-bar').css('-webkit-transform', 'unset')
          $('#mobile-contact-bar').css('-ms-transform', 'unset')
          $('#mobile-contact-bar').css('transform', 'unset')
          break
        case 'center':
          $('#mobile-contact-bar').css('left', '50%')
          $('#mobile-contact-bar').css('-webkit-transform', 'translateX(-50%)')
          $('#mobile-contact-bar').css('-ms-transform', 'translateX(-50%)')
          $('#mobile-contact-bar').css('transform', 'translateX(-50%)')
          break
        case 'right':
          $('#mobile-contact-bar').css('left', '100%')
          $('#mobile-contact-bar').css('-webkit-transform', 'translateX(-100%)')
          $('#mobile-contact-bar').css('-ms-transform', 'translateX(-100%)')
          $('#mobile-contact-bar').css('transform', 'translateX(-100%)')
          break
      }
    },

    update_model_placeholder_y: function () {
      var vp = $('#mcb-bar-vertical_position input:checked').val()
      var ph = $('#mcb-model-placeholder').attr('height')

      if (vp === 'top') {
        $('#mcb-model-placeholder').attr('y', model.canvas.min - ph)
      } else if (vp === 'bottom') {
        $('#mcb-model-placeholder').attr('y', model.canvas.max)
      }
    },

    update_bar_borders: function () {
      var b = $('#mcb-bar-is_border input:checked').val()
      var vp = $('#mcb-bar-vertical_position input:checked').val()
      var bw = ($('#mcb-bar-border_width').val() < 0) ? 0 : $('#mcb-bar-border_width').val()
      var bc = $('#mcb-bar-border_color').val()

      switch (b) {
        case 'one':
          if (vp === 'top') {
            $('#mobile-contact-bar-outer').css('border-top', 'none')
            $('#mobile-contact-bar-outer').css('border-bottom', bw + 'px solid ' + bc)
          } else if (vp === 'bottom') {
            $('#mobile-contact-bar-outer').css('border-top', bw + 'px solid ' + bc)
            $('#mobile-contact-bar-outer').css('border-bottom', 'none')
          }
          break

        case 'two':
          $('#mobile-contact-bar-outer').css('border-top', bw + 'px solid ' + bc)
          $('#mobile-contact-bar-outer').css('border-bottom', bw + 'px solid ' + bc)
          break

        case 'none':
          $('#mobile-contact-bar-outer').css('border-top', 'none')
          $('#mobile-contact-bar-outer').css('border-bottom', 'none')
          break

        default:
          $('#mobile-contact-bar-outer').css('border-top', 'none')
          $('#mobile-contact-bar-outer').css('border-bottom', 'none')
      }
    },

    update_bar_li_height: function () {
      var b = $('#mcb-bar-is_border input:checked').val()
      var h = $('#mcb-bar-height').val()
      var bw = ($('#mcb-bar-border_width').val() < 0) ? 0 : $('#mcb-bar-border_width').val()

      switch (b) {
        case 'one':
          $('#mobile-contact-bar ul li').css('height', (h - bw) + 'px')
          break

        case 'two':
          $('#mobile-contact-bar ul li').css('height', (h - 2 * bw) + 'px')
          break

        case 'none':
          $('#mobile-contact-bar ul li').css('height', h + 'px')
          break

        default:
          $('#mobile-contact-bar ul li').css('height', h + 'px')
      }
    },

    update_icons_width: function () {
      var a = $('#mcb-icons-alignment input:checked').val()
      var iw = ($('#mcb-icons-width').val() < 0) ? 0 : $('#mcb-icons-width').val()
      var cc = ($('.mcb-contact-checkbox input:checked').length > 0) ? $('.mcb-contact-checkbox input:checked').length : 1

      switch (a) {
        case 'centered':
          $('#mobile-contact-bar ul li').css('width', iw)
          break

        case 'justified':
          $('#mobile-contact-bar ul li').css('width', (100 / cc) + '%')
          break

        default:
          $('#mobile-contact-bar ul li').css('width', (100 / cc) + '%')
      }
    },

    update_icons_borders: function () {
      var b = $('#mcb-icons-is_border input:checked').val()
      var bw = ($('#mcb-icons-border_width').val() < 0) ? 0 : $('#mcb-icons-border_width').val()
      var bc = $('#mcb-icons-border_color').val()

      switch (b) {
        case 'two':
          $('#mobile-contact-bar ul li').css('border-top', 'none')
          $('#mobile-contact-bar ul li').css('border-bottom', 'none')
          $('#mobile-contact-bar ul li').css('border-left', bw + 'px solid ' + bc)
          $('#mobile-contact-bar ul li').css('border-right', 'none')
          $('#mobile-contact-bar ul li:last-child').css('border-right', bw + 'px solid ' + bc)
          break

        case 'four':
          $('#mobile-contact-bar ul li').css('border-top', bw + 'px solid ' + bc)
          $('#mobile-contact-bar ul li').css('border-bottom', bw + 'px solid ' + bc)
          $('#mobile-contact-bar ul li').css('border-left', bw + 'px solid ' + bc)
          $('#mobile-contact-bar ul li').css('border-right', 'none')
          $('#mobile-contact-bar ul li:last-child').css('border-right', bw + 'px solid ' + bc)
          break

        case 'none':
          $('#mobile-contact-bar ul li').css('border-top', 'none')
          $('#mobile-contact-bar ul li').css('border-bottom', 'none')
          $('#mobile-contact-bar ul li').css('border-left', 'none')
          $('#mobile-contact-bar ul li').css('border-right', 'none')
          break

        default:
          $('#mobile-contact-bar ul li').css('border-top', 'none')
          $('#mobile-contact-bar ul li').css('border-bottom', 'none')
          $('#mobile-contact-bar ul li').css('border-left', 'none')
          $('#mobile-contact-bar ul li').css('border-right', 'none')
      }
    },

    update_badges_corner: function () {
      var c = $('#mcb-badges-corner input:checked').val()
      switch (c) {
        case 'top-right':
          $('.mobile-contact-bar-badge').css('top', 0)
          $('.mobile-contact-bar-badge').css('right', 0)
          $('.mobile-contact-bar-badge').css('bottom', 'auto')
          $('.mobile-contact-bar-badge').css('left', 'auto')
          break

        case 'bottom-right':
          $('.mobile-contact-bar-badge').css('top', 'auto')
          $('.mobile-contact-bar-badge').css('right', 0)
          $('.mobile-contact-bar-badge').css('bottom', 0)
          $('.mobile-contact-bar-badge').css('left', 'auto')

          break

        case 'bottom-left':
          $('.mobile-contact-bar-badge').css('top', 'auto')
          $('.mobile-contact-bar-badge').css('right', 'auto')
          $('.mobile-contact-bar-badge').css('bottom', 0)
          $('.mobile-contact-bar-badge').css('left', 0)
          break

        case 'top-left':
          $('.mobile-contact-bar-badge').css('top', 0)
          $('.mobile-contact-bar-badge').css('right', 'auto')
          $('.mobile-contact-bar-badge').css('bottom', 'auto')
          $('.mobile-contact-bar-badge').css('left', 0)
          break

        default:
          $('.mobile-contact-bar-badge').css('top', 0)
          $('.mobile-contact-bar-badge').css('right', 0)
          $('.mobile-contact-bar-badge').css('bottom', 'auto')
          $('.mobile-contact-bar-badge').css('left', 'auto')
      }
    },

    update_badges: function () {
      var bc = $('#mcb-badges-background_color').val()
      var c = $('#mcb-badges-font_color').val()
      var fs = $('#mcb-badges-size').val()

      $('.mobile-contact-bar-badge').css('background-color', bc)
      $('.mobile-contact-bar-badge').css('color', c)
      $('.mobile-contact-bar-badge').css('font-size', fs + 'em')

      model.update_badges_corner()
    },

    update_toggle: function () {
      var f = $('#mcb-bar-is_fixed').prop('checked')
      var vp = $('#mcb-bar-vertical_position input:checked').val()

      if (f) {
        if (vp === 'top') {
          $('#mcb-model-toggle').detach().insertBefore('#mcb-model-mobile')
        } else if (vp === 'bottom') {
          $('#mcb-model-toggle').detach().prependTo('#mcb-model-mobile-group')
        }
        model.update_toggle_y()
        model.update_toggle_shape()
      }
    },

    update_toggle_y: function () {
      var h = $('#mcb-bar-height').val()
      var vp = $('#mcb-bar-vertical_position input:checked').val()
      var sh = ($('#mcb-bar-space_height').val() > 0) ? $('#mcb-bar-space_height').val() : 0

      if (vp === 'top') {
        $('#mcb-model-toggle').attr('y', model.mobile.minOut + +sh + +h)
      } else if (vp === 'bottom') {
        $('#mcb-model-toggle').attr('y', model.mobile.maxOut - h - sh - 34)
      }
    },

    update_toggle_x: function () {
      var hp = $('#mcb-bar-horizontal_position input:checked').val()
      var w = $('#mcb-bar-width').val()

      $('#mobile-contact-bar-toggle').css('width', w + '%')
      $('#mobile-contact-bar-toggle').css('margin', 0)

      switch (hp) {
        case 'left':
          $('#mobile-contact-bar-toggle').css('left', '0')
          $('#mobile-contact-bar-toggle').css('-webkit-transform', 'unset')
          $('#mobile-contact-bar-toggle').css('-ms-transform', 'unset')
          $('#mobile-contact-bar-toggle').css('transform', 'unset')
          break

        case 'center':
          $('#mobile-contact-bar-toggle').css('left', '50%')
          $('#mobile-contact-bar-toggle').css('-webkit-transform', 'translateX(-50%)')
          $('#mobile-contact-bar-toggle').css('-ms-transform', 'translateX(-50%)')
          $('#mobile-contact-bar-toggle').css('transform', 'translateX(-50%)')
          break

        case 'right':
          $('#mobile-contact-bar-toggle').css('left', '100%')
          $('#mobile-contact-bar-toggle').css('-webkit-transform', 'translateX(-100%)')
          $('#mobile-contact-bar-toggle').css('-ms-transform', 'translateX(-100%)')
          $('#mobile-contact-bar-toggle').css('transform', 'translateX(-100%)')
          break
      }
    },

    update_toggle_shape: function () {
      var vp = $('#mcb-bar-vertical_position input:checked').val()
      var s = $('#mcb-toggle-shape input:checked').val()

      if (vp === 'top') {
        switch (s) {
          case 'rounded':
            $('#mobile-contact-bar-toggle svg').empty().html('<path d="M 550 0 L 496.9 137.2 C 490.4 156.8 474.1 170 451.4 170 H 98.6 C 77.9 170 59.6 156.8 53.1 137.2 L 0 0 z">')
            break

          case 'sharp':
            $('#mobile-contact-bar-toggle svg').empty().html('<path d="M 550 0 L 494.206 170 H 65.794 L 0 0 z">')
            break

          default:
            $('#mobile-contact-bar-toggle svg').empty().html('<path d="M 550 0 L 494.206 170 H 65.794 L 0 0 z">')
        }
      } else if (vp === 'bottom') {
        switch (s) {
          case 'rounded':
            $('#mobile-contact-bar-toggle svg').empty().html('<path d="M 550 170 L 496.9 32.8 C 490.4 13.2 474.1 0 451.4 0 H 98.6 C 77.9 0 59.6 13.2 53.1 32.8 L 0 170 z">')
            break

          case 'sharp':
            $('#mobile-contact-bar-toggle svg').empty().html('<path d="M 550 170 L 494.206 0 H 65.794 L 0 170 z">')
            break

          default:
            $('#mobile-contact-bar-toggle svg').empty().html('<path d="M 550 170 L 494.206 0 H 65.794 L 0 170 z">')
        }
      }
    }
  }

  $(document).ready(model.init)
})(jQuery, document)
