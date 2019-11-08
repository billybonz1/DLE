jQuery(document).ready(function(){
  function htmSlider(){
    /* ������� ��������� ���������� */

    /* ������� �������� */
    var slideWrap = jQuery('.slide-wrap');
    /* ������ �� ���������� ���������� ����� */
    var nextLink = jQuery('.next-slide');
    var prevLink = jQuery('.prev-slide');

    var playLink = jQuery('.auto');
    
    var is_animate = false;
    
    /* ������ ������ � ��������� */
    var slideWidth = jQuery('.slide-item').outerWidth();
    
    /* �������� �������� */
    var newLeftPos = slideWrap.position().left - slideWidth;
      var scrollSlider = slideWrap.position().left - slideWidth; 
    /* ���� �� ������ �� ��������� ����� */
    nextLink.click(function(){
      if(!slideWrap.is(':animated')) {
  
        slideWrap.animate({left: newLeftPos}, 500, function(){
          slideWrap
            .find('.slide-item:first')
            .appendTo(slideWrap)
            .parent()
            .css({'left': 0});
        });
                  
      }
    });
       timer = setInterval(function(){
   slideWrap.animate({left: scrollSlider}, 500, function(){
    slideWrap
     .find('.slide-item:first')
     .appendTo(slideWrap)
     .parent()
     .css({'left': 0});
    });
  }, 6000);

    /* ���� �� ������ �� ����������� ����� */
    prevLink.click(function(){
      if(!slideWrap.is(':animated')) {
      
        slideWrap
          .css({'left': newLeftPos})
          .find('.slide-item:last')
          .prependTo(slideWrap)
          .parent()
          .animate({left: 0}, 50);

      }
    });
     

  
    /* ������� �������������� ��������� �������� */
    function autoplay(){
      if(!is_animate){
        is_animate = true;
        slideWrap.animate({left: newLeftPos}, 500, function(){
          slideWrap
            .find('.slide-item:first')
            .appendTo(slideWrap)
            .parent()
            .css({'left': 0});
          is_animate = false;
        });
      }
    }
    
    /* ����� �� ������� �����/����� */
    /*playLink.click(function(){
      if(playLink.hasClass('play')){
        playLink.removeClass('play').addClass('pause');
        jQuery('.navy').addClass('disable');
        timer = setInterval(autoplay, 1000);
      } else {
        playLink.removeClass('pause').addClass('play');
        jQuery('.navy').removeClass('disable');
        clearInterval(timer);
      }
    });*/

  }
  

  
  /* ������������� ������� �������� */
  htmSlider();
});