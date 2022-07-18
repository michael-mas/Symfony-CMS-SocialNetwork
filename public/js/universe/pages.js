$(function () {

    var EASE = Power4.easeOut;
  
    var Engine = {
      ui: {
        initBtn: function () {
          var card = $('.card, .btn');
          var body = $('body');
          var btn = $('.btn');
  
          card.on('click', function () {
  
            if (body.hasClass('is-open')) {
              body.removeClass('is-open');
              btn.html('View');
            } else {
              body.addClass('is-open');
              btn.html('close');
              TweenMax.set('.card', { clearProps: 'all' });
            }
          });
        },
        initHover: function (e) {
          $(document).on("mousemove", ".card", function (e) {
            if ($('body').hasClass('is-open')) {
              e.preventDefault();
              // $('.card').attr('style', '').children('.card-title-wrap').attr('style', '');
            } else {
              var halfW = this.clientWidth / 2;
              var halfH = this.clientHeight / 2;
  
              var coorX = halfW - (event.pageX - this.offsetLeft);
              var coorY = halfH - (event.pageY - this.offsetTop);
  
              var degX = coorY / halfH * 10 + 'deg'; // max. degree = 10
              var degY = coorX / halfW * -10 + 'deg'; // max. degree = 10
  
              $(this).css('transform', function () {
                return 'perspective(1600px) translate3d(0, 0px, 0) scale(0.6) rotateX(' + degX + ') rotateY(' + degY + ')';
              }).children('.card-title-wrap').css('transform', function () {
                return 'perspective(1600px) translate3d(0, 0, 200px) rotateX(' + degX + ') rotateY(' + degY + ')';
              });
            }
          }).on("mouseout", ".card", function () {
            $(this).removeAttr('style').children('.card-title-wrap').removeAttr('style');
          });
  
  
        } } };
  
  
  
    Engine.ui.initBtn();
    Engine.ui.initHover();
  
  });