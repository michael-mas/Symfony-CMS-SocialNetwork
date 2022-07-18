//Part of Help

$(document).ready(function(){
    var zindex = 10;
    
    $("div.card3").click(function(e){
      e.preventDefault();
  
      var isShowing = false;
  
      if ($(this).hasClass("show")) {
        isShowing = true
      }
  
      if ($("div.cards").hasClass("showing")) {
        // a card is already in view
        $("div.card3.show")
          .removeClass("show");
  
        if (isShowing) {
          // this card was showing - reset the grid
          $("div.cards")
            .removeClass("showing");
        } else {
          // this card isn't showing - get in with it
          $(this)
            .css({zIndex: zindex})
            .addClass("show");
  
        }
  
        zindex++;
  
      } else {
        // no cards in view
        $("div.cards")
          .addClass("showing");
        $(this)
          .css({zIndex:zindex})
          .addClass("show");
  
        zindex++;
      }
      
    });
  });

//Part of Pleasure


var mouse = {
    X   : 0,
    Y   : 0,
    CX  : 0,
    CY  : 0
  },
  block = {
    X   : mouse.X,
    Y   : mouse.Y,
    CX  : mouse.CX,
    CY  : mouse.CY
  },
  imags = [
    'https://images.unsplash.com/photo-1572947650440-e8a97ef053b2?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxzZWFyY2h8OHx8YXJ0JTIwZ2FsbGVyeXxlbnwwfHwwfHw%3D&w=1000&q=80',
    'https://wallpaperaccess.com/full/127045.jpg',
    'https://images.squarespace-cdn.com/content/v1/5475f6eae4b0821160f6ac3e/1537912616389-TAI14JP1X723CJ7JV2DS/Teacher+using+storytelling+in+class',
    'https://images.pexels.com/photos/3165335/pexels-photo-3165335.jpeg?cs=srgb&dl=pexels-lucie-liz-3165335.jpg&fm=jpg',
  ];

$('.block').on('mousemove', function(e) {
mouse.X   = (e.pageX - $(this).offset().left) - $('.block').width() / 2;
mouse.Y   = (e.pageY - $(this).offset().top) - $('.block').height() / 2;
})

$('.block').on('mouseleave', function(e) {
mouse.X   = mouse.CX;
mouse.Y   = mouse.CY;
})

setInterval(function(){

block.CY   += (mouse.Y - block.CY) / 12;
block.CX   += (mouse.X - block.CX) / 12;

$('.block .circleLight').css('background', 'radial-gradient(circle at ' + mouse.X + 'px ' + mouse.Y + 'px, #fff, transparent)')
$('.block').css({
  transform : 'scale(1.03) translate(' + (block.CX * 0.05) + 'px, ' + (block.CY * 0.05) + 'px) rotateX(' + (block.CY * 0.05) + 'deg) rotateY(' + (block.CX * 0.05) + 'deg)'
})

}, 20);

$('.slider .item').each(function(i){

if(i == 0){
  
  $(this).addClass('active');
  $(this).next().addClass('next');
  $(this).prev().addClass('prev');
}

$(this).attr('id', 'slide-'+i);

$(this).prepend(
  $('<div>', {class: 'blur', style: 'background-image: url(' + imags[i] + ');'}),
  $('<div>', {class: 'bg', style: 'background-image: url(' + imags[i] + ');'})
)

$(this).find('.block').css('background-image', 'url(' + imags[i] + ')')

$('.navigations .dots').append(
  $('<li>', {class: i == 0 ? 'active' : '', id: i}).on('click', function(){
  var cSlide = $('.slider #slide-'+$(this).attr('id'));
    
    $('.navigations .dots li').removeClass('active');
    $(this).addClass('active');
    
    $('.slider .item').removeClass('active prev next');
    cSlide.addClass('active');
    cSlide.next().addClass('next');
    cSlide.prev().addClass('prev');
  })
)
})