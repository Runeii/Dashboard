// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// Place any jQuery/helper plugins in here.
;'use strict';

$(document).ready(function () {
  var widget = {
    'nonce': 'test',
    'file': ['lastupdated', 'social-followers', 'network-overview', 'website-referrals', 'website-pages']
  };
  $.get('/includes/widgets/widget.php', widget, function (data) {
    $(data).appendTo("main");
  });
});
//# sourceMappingURL=main.js.map
;'use strict';

//Cached elements
var menu = document.querySelector('body > nav');

//Variables
var menuoffset = menu.getBoundingClientRect().top,
    menustuck = false;
$(window).scroll(function () {
  var scrollPos = window.scrollY;
  if (menustuck === false && menuoffset <= scrollPos) {
    menu.classList.add('fixed');
    menustuck = true;
  } else if (menustuck === true && menuoffset > scrollPos) {
    menu.classList.remove('fixed');
    menustuck = false;
  }
});
//# sourceMappingURL=UI.js.map
