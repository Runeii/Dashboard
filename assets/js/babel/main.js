'use strict';

$(document).ready(function () {
  var widget = {
    'nonce': 'test',
    'client': 1,
    'file': ['social-overview', 'website-referrals', 'website-pages']
  };
  $('.brand[data-id=1]').addClass('active');
  $.get('/includes/widgets/widget.php', widget, function (data) {
    swapPage(data);
  });
  pageEventHandlers('html');
});

//Setup CSRF security for AJAX
$.ajaxSetup({
  headers: {
    'csrftoken': $('meta[name="csrf-token"]').attr('content')
  }
});

//
/// Navigation System
//
$('[data-client]').click(function () {
  var type = $(this).data('id');
  var widget = {
    'nonce': 'test',
    'client': type,
    'file': ['social-overview', 'website-referrals', 'website-pages']
  };
  $('.brand.active').removeClass('active');
  $(this).addClass('loading');
  $.get('/includes/widgets/widget.php', widget, function (data) {
    swapPage(data);
    $('.brand.loading').addClass('active');
    $('.brand.active').removeClass('loading');
  });
});
function pageEventHandlers(el) {

  $(el + ' [data-navigate]').click(function (el) {
    var location = $(this).data('navigate');
    $.get('includes/UI/' + location + '.php', function (result) {
      swapPage(result);
    });
  });
  $(el + ' [data-action]').click(function (el) {
    var action = $(this).data('action');
    window[action](el);
  });
  $(".comparison select").change(function () {
    var newdate = $(this).val();
    var parent = $(this).parents('section');
    var statistics = $(parent).find('[data-now]');
    $(statistics).each(function (index) {
      var nf = new Intl.NumberFormat();
      var newstat = $(this).attr('data-' + newdate);
      console.log(newstat);
      if (newdate == 'now') {
        var html = $(this).attr('data-now');
      } else {
        if ($(this).attr('data-now') >= newstat) {
          var change = ($(this).attr('data-now') - newstat) / newstat * 100;
          var html = '<span class="positive">' + nf.format(newstat) + '</span><span class="statcaption">We\'re up ' + round(change, 2) + '%</span>';
        } else {
          var change = ($(this).attr('data-now') - newstat) / newstat * 100;
          var html = '<span class="negative">' + nf.format(newstat) + '</span><span class="statcaption">We\'re down ' + round(change, 2) + '%</span>';
        }
      }
      $(this).html(html);
    });
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
//


//
function round(number, precision) {
  var pair = (number + 'e').split('e');
  var value = Math.round(pair[0] + 'e' + (+pair[1] + precision));
  pair = (value + 'e').split('e');
  return +(pair[0] + 'e' + (+pair[1] - precision));
}
//# sourceMappingURL=main.js.map
