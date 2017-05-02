$(document).ready(function(){

  if($(window).width() < 610){
    $('.nieuw_film_slider').slick({
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      dots: true,
      speed: 600,
      responsive: true
    });
  }else if($(window).width() < 1000 && $(window).width() > 610){
    $('.nieuw_film_slider').slick({
      infinite: true,
      slidesToShow: 2,
      slidesToScroll: 1,
      dots: true,
      speed: 600,
      responsive: true
    });
  }else{
    $('.nieuw_film_slider').slick({
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 3,
      dots: true,
      speed: 600,
      responsive: true
    });
  }
  $('.afleverDatum').hide();
  $('.nee').click(function(){
    $('.afleverDatum').show("fast");
    $('.vraag').hide("fast");
  });

  $('.verzendButton').prop('disabled', true);
  $(".bezorger").click(function(){
    $(".bezorger").css("border", "1px solid #009688");
    $(".ophalen").css("border", "none");
    $('.verzendButton').prop('disabled', false);
  });
  $(".ophalen").click(function(){
    $(".ophalen").css("border", "1px solid #009688");
    $(".bezorger").css("border", "none");
    $('.verzendButton').prop('disabled', false);
  });

});
