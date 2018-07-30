
// Link: http://www.velvetcache.org/2010/08/19/a-simple-javascript-hooks-system

(function (window) {
  'use strict'

  var hook = {
    hooks: [],

    register: function (name, callback) {
      if (typeof (hook.hooks[name]) === 'undefined') { hook.hooks[name] = [] }
      hook.hooks[name].push(callback)
    },

    call: function (name, args) {
      if (typeof (hook.hooks[name]) !== 'undefined') {
        for (var i = 0; i < hook.hooks[name].length; ++i) {
          if (hook.hooks[name][i](args) !== true) { break }
        }
      }
    }
  }

  window.hook = window.hook || hook
})(window)
