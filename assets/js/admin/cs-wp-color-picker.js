/*!
 * Codestar WP Color Picker v1.1.0 - This is plugin for WordPress Color Picker Alpha Channel
 * Copyright 2015 Codestar <info@codestarthemes.com> - GNU GENERAL PUBLIC LICENSE (http://www.gnu.org/licenses/gpl-2.0.txt)
 */

/* global Color */

(function ($, document) {
  'use strict'

  // adding alpha support for Automattic Color.js toString function.
  if (typeof Color.fn.toString !== 'undefined') {
    Color.fn.toString = function () {
      // check for alpha
      if (this._alpha < 1) {
        return this.toCSS('rgba', this._alpha).replace(/\s+/g, '')
      }

      var hex = parseInt(this._color, 10).toString(16)

      if (this.error) { return '' }

      // maybe left pad it
      if (hex.length < 6) {
        for (var i = 6 - hex.length - 1; i >= 0; i--) {
          hex = '0' + hex
        }
      }

      return '#' + hex
    }
  }

  $.cs_ParseColorValue = function (val) {
    var value = val.replace(/\s+/g, '')

    var alpha = (value.indexOf('rgba') !== -1) ? parseFloat(value.replace(/^.*,(.+)\)/, '$1') * 100) : 100

    var rgba = (alpha < 100)

    return { value: value, alpha: alpha, rgba: rgba }
  }

  $.fn.cs_wpColorPicker = function () {
    return this.each(function () {
      var $this = $(this)

      // check for rgba enabled/disable
      if ($this.data('rgba') !== false) {
        // parse value
        var picker = $.cs_ParseColorValue($this.val())

        // wpColorPicker core
        $this.wpColorPicker({

          // wpColorPicker: clear
          clear: function () {
            $this.trigger('keyup')
          },

          // wpColorPicker: change
          change: function (event, ui) {
            var uiColorValue = ui.color.toString()

            $this.closest('.wp-picker-container').find('.cs-alpha-slider-offset').css('background-color', uiColorValue)
            $this.val(uiColorValue).trigger('change')
          },

          // wpColorPicker: create
          create: function () {
            // set variables for alpha slider
            var a8cIris = $this.data('a8cIris')

            var $container = $this.closest('.wp-picker-container')

            // appending alpha wrapper

            var $alphaWrap = $('<div class="cs-alpha-wrap">' +
                                            '<div class="cs-alpha-slider"></div>' +
                                            '<div class="cs-alpha-slider-offset"></div>' +
                                            '<div class="cs-alpha-text"></div>' +
                                            '</div>').appendTo($container.find('.wp-picker-holder'))

            var $alphaSlider = $alphaWrap.find('.cs-alpha-slider')

            var $alphaText = $alphaWrap.find('.cs-alpha-text')

            var $alphaOffset = $alphaWrap.find('.cs-alpha-slider-offset')

            // alpha slider
            $alphaSlider.slider({

              // slider: slide
              slide: function (event, ui) {
                var slideValue = parseFloat(ui.value / 100)

                // update iris data alpha && wpColorPicker color option && alpha text
                a8cIris._color._alpha = slideValue
                $this.wpColorPicker('color', a8cIris._color.toString())
                $alphaText.text((slideValue < 1 ? slideValue : ''))
              },

              // slider: create
              create: function () {
                var slideValue = parseFloat(picker.alpha / 100)

                var alphaTextValue = slideValue < 1 ? slideValue : ''

                // update alpha text && checkerboard background color
                $alphaText.text(alphaTextValue)
                $alphaOffset.css('background-color', picker.value)

                // wpColorPicker clear for update iris data alpha && alpha text && slider color option
                $container.on('click', '.wp-picker-clear', function () {
                  a8cIris._color._alpha = 1
                  $alphaText.text('')
                  $alphaSlider.slider('option', 'value', 100).trigger('slide')
                })

                // wpColorPicker default button for update iris data alpha && alpha text && slider color option
                $container.on('click', '.wp-picker-default', function () {
                  var defaultPicker = $.cs_ParseColorValue($this.data('default-color'))

                  var defaultValue = parseFloat(defaultPicker.alpha / 100)

                  var defaultText = defaultValue < 1 ? defaultValue : ''

                  a8cIris._color._alpha = defaultValue
                  $alphaText.text(defaultText)
                  $alphaSlider.slider('option', 'value', defaultPicker.alpha).trigger('slide')
                })

                // show alpha wrapper on click color picker button
                $container.on('click', '.wp-color-result', function () {
                  $alphaWrap.toggle()
                })

                // hide alpha wrapper on click body
                $('body').on('click.wpcolorpicker', function () {
                  $alphaWrap.hide()
                })
              },

              // slider: options
              value: picker.alpha,
              step: 1,
              min: 1,
              max: 100
            })
          }
        })
      } else {
        // wpColorPicker default picker
        $this.wpColorPicker({
          clear: function () {
            $this.trigger('keyup')
          },
          change: function (event, ui) {
            $this.val(ui.color.toString()).trigger('change')
          }
        })
      }
    })
  }

  $(document).ready(function () {
    $('.cs-wp-color-picker').cs_wpColorPicker()
  })
})(jQuery, document)
