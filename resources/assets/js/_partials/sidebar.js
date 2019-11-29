/* 
| Sidebar scripts 
*/
import slimScroll from 'slim-scroll';

if (document.querySelector('#main-sidebar')) {
  window.onload = function () {
    let sidebarScroll = new slimScroll(document.querySelectorAll('#main-sidebar')[0], {
      'wrapperClass': 'scroll-wrapper',
      'scrollBarContainerClass': 'scrollBarContainer',
      'scrollBarContainerSpecialClass': 'animate',
      'scrollBarClass': 'scroll',
      'keepFocus': true
    });
    window.onresize = sidebarScroll.resetValues;
  };

  function showSidebar (el) {
    $(el).toggleClass('open');
    $('#main-sidebar').toggleClass('mob-show')
  }

  window.showSidebar = showSidebar;

  var sidebar = new Vue({
    el: '#main-sidebar',
  });
}