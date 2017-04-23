/**
 * city
 *  
 */

$("#city-img").on("mousemove", function(event) {
  var cityWidth = parseInt($("#city-img").css("width")) ;

  var frontWidth = parseInt($("#city-front").css("width")) ;
  var frontLeft = (cityWidth-frontWidth)/2;
  var frontLeft = frontLeft + (33 - (event.pageX / cityWidth) * 66) ;
  $("#city-front").css("left", frontLeft);

  var backWidth = parseInt($("#city-back").css("width")) ;
  var backLeft = (cityWidth-backWidth)/2;
  var backLeft = backLeft + (12 - (event.pageX / cityWidth) * 24) ;
  $("#city-back").css("left", backLeft);
});

function copyToClipboard(that)
{
  event.preventDefault();
  var data = $('#'.concat($(that).data('to-copy'))).html();
  let clipboard = document.createElement('textarea');
  clipboard.id = 'clipboard';
  clipboard.style.height = 0;
  document.body.appendChild(clipboard);
  clipboard.value = data;
  let selector = document.querySelector('#clipboard');
  selector.select();
  document.execCommand('copy');
  document.body.removeChild(clipboard);
}

/* Smooth Scroll */
$('a[href*="#"]:not([href="#"])').click(function() {
  if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
    var target = $(this.hash);
    target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
    if (target.length) {
      $('html,body').animate({
        scrollTop: target.offset().top
      }, 1000);
      return false;
    }
  }
});

/* thumb / modal */
$(".thumb-wrapper").click(function() {
  
  $("#modal-img").attr("src",$(this).find(".thumb-img").attr("src"));
  $("#modal-title").text($(this).find(".thumb-title").text());
  $("#modal-container").removeClass("modal-off").addClass("modal-on");

  $("#modal-container").click(function() {
    $("#modal-container").removeClass("modal-on").addClass("modal-off");
  });
});

