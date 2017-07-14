'use strict';

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
