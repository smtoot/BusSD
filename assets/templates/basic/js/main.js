(function ($) {
  "user strict";

  $(document).ready(function () {
    //preloader
    $(".preloader")
      .delay(300)
      .animate(
        {
          opacity: "0",
        },
        500,
        function () {
          $(".preloader").css("display", "none");
        }
      );
    // nice-select
    $("select").niceSelect();
    background();
  });

  $(".navbar-toggler").click(function () {
    $("#navbarSupportedContent").toggleClass("show");
  });

  $(window).on("load", function () {});

  /*---------------====================
     11.WOW Active
  ================-------------------*/

  if ($(".wow").length) {
    var wow = new WOW({
      boxClass: "wow",
      // animated element css class (default is wow)
      animateClass: "animated",
      // animation css class (default is animated)
      offset: 0,
      // distance to the element when triggering the animation (default is 0)
      mobile: false,
      // trigger animations on mobile devices (default is true)
      live: true, // act on asynchronously loaded content (default is true)
    });
    wow.init();
  }

  //Create Background Image
  function background() {
    var img = $(".bg_img");
    img.css("background-image", function () {
      var bg = "url(" + $(this).data("background") + ")";
      return bg;
    });
  }

  var fixed_top = $(".header-section");
  $(window).on("scroll", function () {
    if ($(window).scrollTop() > 100) {
      fixed_top.addClass("animated fadeInDown header-fixed");
    } else {
      fixed_top.removeClass("animated fadeInDown header-fixed");
    }
  });

  // navbar-click
  $(".navbar li a").on("click", function () {
    var element = $(this).parent("li");
    if (element.hasClass("show")) {
      element.removeClass("show");
      element.find("li").removeClass("show");
    } else {
      element.addClass("show");
      element.siblings("li").removeClass("show");
      element.siblings("li").find("li").removeClass("show");
    }
  });

  // scroll-to-top
  var ScrollTop = $(".scrollToTop");
  $(window).on("scroll", function () {
    if ($(this).scrollTop() < 500) {
      ScrollTop.removeClass("active");
    } else {
      ScrollTop.addClass("active");
    }
  });

  // slider
  var swiper = new Swiper(".client-slider", {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    autoplay: {
      speed: 2000,
      delay: 3000,
    },
    speed: 1000,
    breakpoints: {
      991: {
        slidesPerView: 1,
      },
      767: {
        slidesPerView: 1,
      },
      575: {
        slidesPerView: 1,
      },
    },
  });

  var swiper = new Swiper(".brand-wrapper", {
    slidesPerView: 7,
    spaceBetween: 30,
    loop: true,
    autoplay: {
      speeds: 1000,
      delay: 2000,
    },
    speed: 1000,
    breakpoints: {
      991: {
        slidesPerView: 3,
      },
      767: {
        slidesPerView: 2,
      },
      575: {
        slidesPerView: 2,
      },
    },
  });
})(jQuery);
