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
    'client': 1,
    'file': ['social-followers', 'network-overview', 'website-referrals', 'website-pages']
  };
  pageEventHandlers('html');
});

//Setup CSRF security for AJAX
$.ajaxSetup({
  headers: {
    'CsrfToken': $('meta[name="csrf-token"]').attr('content')
  }
});

var actions = {
  logout: logout,
  setDate: updateWidgetDate
};

//
/// Navigation System
//
$('[data-client]').click(function () {
  var type = $(this).data('id');
  var widget = {
    'nonce': 'test',
    'client': type,
    'file': ['social-followers', 'network-overview', 'website-referrals', 'website-pages']
  };
  $.get('/includes/widgets/widget.php', widget, function (data) {
    swapPage(data);
  });
});
function pageEventHandlers(el) {
  $(el + ' [data-navigate]').click(function (el) {
    var type = $(this).data('navtype');
    var location = $(this).data('navigate');
    if (type === 'frame') {
      $.get('includes/UI/' + location + '.php', function (result) {
        swapPage(result);
      });
    } else if (type === 'action') {
      actions[location](el);
    }
  });
}
function swapPage(data) {
  $('main').fadeOut('fast', function () {
    $('main').html("");
    $(data).appendTo("main");
    pageEventHandlers('main');
    $('main').fadeIn('fast');
  });
}

//
/// Nav Actions
//
function logout() {
  body.style.opacity = '0';
}
function updateWidgetDate(el) {
  var newdate = el.target.innerHTML;
  var parent = $(el.target).parents('article');
  $(parent).find('h5.active').removeClass('active');
  $(el.target).addClass('active');
  var statistics = $(parent).find('[data-now]');
  $(statistics).each(function (index) {
    var nf = new Intl.NumberFormat();
    var newstat = $(this).attr('data-' + newdate);
    if (newdate == 'now') {
      var html = $(this).attr('data-now');
    } else {
      if ($(this).attr('data-now') >= newstat) {
        var change = ($(this).attr('data-now') - newstat) / newstat * 100;
        var html = '<span class="positive">' + nf.format(newstat) + '</span><span class="statcaption">+' + round(change, 2) + '</span>';
      } else {
        var change = ($(this).attr('data-now') - newstat) / newstat * 100;
        var html = '<span class="negative">' + nf.format(newstat) + '</span><span class="statcaption">' + round(change, 2) + '%</span>';
      }
    }
    $(this).html(html);
  });
}
function round(number, precision) {
  var pair = (number + 'e').split('e');
  var value = Math.round(pair[0] + 'e' + (+pair[1] + precision));
  pair = (value + 'e').split('e');
  return +(pair[0] + 'e' + (+pair[1] - precision));
}
//# sourceMappingURL=main.js.map
;'use strict';

//Cached elements
var menu = document.querySelector('body > nav');
var main = document.querySelector('body > main');

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
