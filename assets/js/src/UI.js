//Cached elements
var menu = document.querySelector('body > nav');
var main = document.querySelector('body > main');

//Variables
var menuoffset = menu.getBoundingClientRect().top,
    menustuck = false;
$(window).scroll(function() {
  var scrollPos = window.scrollY;
  if(menustuck === false && menuoffset <= scrollPos) {
    menu.classList.add('fixed');
    menustuck = true;
  } else if(menustuck === true && menuoffset > scrollPos) {
    menu.classList.remove('fixed');
    menustuck = false;
  }
});
