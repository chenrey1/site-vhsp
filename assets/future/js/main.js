document.addEventListener('DOMContentLoaded', function() {
    const userDropdownTrigger = document.querySelector('.user-dropdown-trigger');
    const fpTopnavDropdown = document.querySelector('.fp-topnav-dropdown');
    
    if (userDropdownTrigger && fpTopnavDropdown) {
        // Trigger tıklama eventi
        userDropdownTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle active class'ları
            userDropdownTrigger.classList.toggle('active');
            fpTopnavDropdown.classList.toggle('active');
        });
        
        // Dropdown dışında tıklandığında kapat
        document.addEventListener('click', function(e) {
            if (!userDropdownTrigger.contains(e.target) && !fpTopnavDropdown.contains(e.target)) {
                userDropdownTrigger.classList.remove('active');
                fpTopnavDropdown.classList.remove('active');
            }
        });
        
        // ESC tuşu ile kapat
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                userDropdownTrigger.classList.remove('active');
                fpTopnavDropdown.classList.remove('active');
            }
        });
    }
});
var swiper = new Swiper(".mySwiper2", {
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      pagination: {
        el: ".swiper-paginationn",
        clickable: true,
      },
    breakpoints: {
        0: {
          slidesPerView: 2,
          spaceBetween: 12,
        },
        640: {
          slidesPerView: 2,
          spaceBetween: 12,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 12,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 12,
        },
      },
});

document.querySelectorAll('.header-dropdown-item').forEach(item => {
    item.addEventListener('click', function () {
        const dropdownArea = this.querySelector('.header-dropdown-area');
        const isVisible = dropdownArea.style.opacity === '1';

        // Tüm dropdown-area'ları kapat
        document.querySelectorAll('.header-dropdown-area').forEach(area => {
            area.style.opacity = '0';
            // Animasyon bittikten sonra display'i none yap
            setTimeout(() => {
                if (area.style.opacity === '0') {
                    area.style.display = 'none';
                }
            }, 1000);
        });

        // Eğer açık değilse göster (tekrar tıklandıysa açma)
        if (!isVisible) {
            dropdownArea.style.display = 'block';
            dropdownArea.style.opacity = '0';
            
            // Animasyon için kısa bir gecikme
            setTimeout(() => {
                dropdownArea.style.opacity = '1';
            }, 10);
        }
    });
});
 function createLeaf() {
    const leaf = document.createElement("div");
    leaf.className = "snowflake";

    // Beyaz kar tanesi emoji
    leaf.textContent = '❄';

    // Rastgele stiller
    const left = Math.random() * 100; 
    const size = 5 + Math.random() * 10; 
    const duration = 10 + Math.random() * 10; // %50 yavaşlatıldı (10-20 saniye)

    leaf.style.left = `${left}vw`;
    leaf.style.fontSize = `${size}px`;
    leaf.style.animationDuration = `${duration}s`;

    document.getElementById("leaf-container").appendChild(leaf);


    setTimeout(() => {
      leaf.remove();
    }, duration * 1000);
  }

  setInterval(createLeaf, 200);


// Old main swiper dursun
// const progressCircle = document.querySelector(".autoplay-progress svg");
// const progressContent = document.querySelector(".autoplay-progress span");
// var swiperHome = new Swiper(".fp-swiper-home", {
//    spaceBetween: 24,
//    on: {
//        autoplayTimeLeft(s, time, progress) {
//            progressCircle.style.setProperty("--progress", 1 - progress);
 //           progressContent.textContent = `${Math.ceil(time / 1000)}s`;
//        }
 //   }
// });

// var totalSlides = swiperHome.slides.length;

// if (totalSlides == 1) {
//    var swiperHome = new Swiper(".fp-swiper-home", {
//        spaceBetween: 24,
//        on: {
//            autoplayTimeLeft(s, time, progress) {
//                progressCircle.style.setProperty("--progress", 1 - progress);
//                progressContent.textContent = `${Math.ceil(time / 1000)}s`;
//            }
//        }
//    });
// } else {
//    var swiperHome = new Swiper(".fp-swiper-home", {
//        spaceBetween: 24,
//        autoplay: {
//            delay: 5000,
//            disableOnInteraction: false
//        },
//        on: {
//            autoplayTimeLeft(s, time, progress) {
//                progressCircle.style.setProperty("--progress", 1 - progress);
//                progressContent.textContent = `${Math.ceil(time / 1000)}s`;
//            }
//        },
//        loop: true
//    });
// }

// Swiper
document.addEventListener("DOMContentLoaded", function () {
  const progressCircle = document.querySelector(".autoplay-progress svg");
  const progressContent = document.querySelector(".autoplay-progress span");
  const blurImage = document.getElementById("blurImage");

  const swiperHome = new Swiper(".fp-swiper-home", {
    spaceBetween: 24,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    loop: true,
    on: {
      init(swiper) {
        updateBlurImage(swiper);
      },
      slideChange(swiper) {
        updateBlurImage(swiper);
      },
      autoplayTimeLeft(s, time, progress) {
        progressCircle?.style.setProperty("--progress", 1 - progress);
        if (progressContent) {
          progressContent.textContent = `${Math.ceil(time / 1000)}s`;
        }
      }
    }
  });


  function updateBlurImage(swiper) {
    const activeSlide = swiper.slides[swiper.activeIndex];
    const img = activeSlide.querySelector(".img-cover");
    if (img && blurImage) {
      blurImage.src = img.src;
    }
  }
});

// function updateBlurImage(swiper) {
//   const activeSlide = swiper.slides[swiper.activeIndex];
//   const blurData = activeSlide.getAttribute("data-blur");
//   if (blurData && blurImage) {
//     const tempDiv = document.createElement('div');
//     tempDiv.innerHTML = blurData.trim();
//     const imgTag = tempDiv.querySelector('img');
// 
//     if (imgTag && imgTag.src) {
//       blurImage.src = imgTag.src;
//     }
//   }
// }
// 


var swiperCategories = new Swiper(".fp-swiper-categories", {
    spaceBetween: 20,
    slidesPerView: 8,
    pagination: {
        el: ".swiper-pagination",
        clickable: true
    },
    navigation: {
        nextEl: ".fp-swiper-categories-next",
        prevEl: ".fp-swiper-categories-prev"
    },
    breakpoints: {
        1200: {slidesPerView: 8},
        992: {slidesPerView: 7},
        768: {slidesPerView: 5},
        576: {slidesPerView: 4},
        0: {slidesPerView: 3}
    }
});


var swiperTwo = new Swiper(".fp-swiper-two", {
    spaceBetween: 15,
    slidesPerView: 2,
    navigation: {
        nextEl: ".fp-swiper-two-next",
        prevEl: ".fp-swiper-two-prev"
    },
    loop: true,
    breakpoints: {
        768: {slidesPerView: 2},
        0: {slidesPerView: 1},
    }
});


var swiperStreamers = new Swiper(".fp-swiper-streamers", {
    spaceBetween: 15,
    slidesPerView: 5,
    navigation: {
        nextEl: ".fp-swiper-streamers-next",
        prevEl: ".fp-swiper-streamers-prev"
    },
    loop: true,
    breakpoints: {
        1200: {slidesPerView: 5},
        992: {slidesPerView: 4},
        768: {slidesPerView: 3},
        0: {slidesPerView: 2},
    }
});


// Quantity
$(document).on('click', '.fp-quantity-btn.plus', function (e) {
    e.preventDefault();
    var inputElement = $(this).closest('.fp-quantity').find('.form-control');
    var currentValue = parseInt(inputElement.val(), 10);
    inputElement.val(currentValue + 1);
});

// Azaltma fonksiyonu
$(document).on('click', '.fp-quantity-btn.minus', function (e) {
    e.preventDefault();
    var inputElement = $(this).closest('.fp-quantity').find('.form-control');
    var currentValue = parseInt(inputElement.val(), 10);
    if (currentValue > 1) {
        inputElement.val(currentValue - 1);
    }
});


$(document).ready(function () {
    var savedTheme = getCookie('theme');
    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme) {
        setTheme(savedTheme);
    } else {
        setTheme(prefersDark ? 'dark' : 'light');
    }

    $('.link-light-theme').click(function (e) {
        e.preventDefault();
        
        // Animasyon ekle
        var icon = $(this).find('i');
        icon.css({
            'transform': 'rotate(360deg)',
            'transition': 'transform 0.2s ease'
        });
        
        setTimeout(function() {
            setTheme('light');
            icon.css('transform', 'rotate(0deg)');
        }, 500);
    });

    $('.link-dark-theme').click(function (e) {
        e.preventDefault();
        
        // Animasyon ekle
        var icon = $(this).find('i');
        icon.css({
            'transform': 'rotate(360deg)',
            'transition': 'transform 0.2s ease'
        });
        
        setTimeout(function() {
            setTheme('dark');
            icon.css('transform', 'rotate(0deg)');
        }, 500);
    });

    function setTheme(theme) {
        $('.link-light-theme, .link-dark-theme').removeClass('active');
        $('.link-' + theme + '-theme').addClass('active');
        $('html').attr('data-theme', theme);
        setCookie('theme', theme, 365);
    }

    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + value + expires + '; path=/';
    }

    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
});

// Mobile Menu
$(".btn-all-categories").click(function(e) {
    e.preventDefault();

    $(".fp-navbar").fadeToggle(150);
    $("body").toggleClass("overflow-hidden");
});


// Tabs
$(document).ready(function () {
    $(".fp-tabs-nav-system a").click(function () {
        $(".fp-tabs-nav-system a").removeClass("active");

        $(this).addClass("active");

        $(".fp-tabs-content").removeClass("active");

        var contentId = $(this).attr("id");
        $("#" + contentId + "-content").addClass("active");

        return false;
    });
});


// Dropdown
function menuDropdownMobile() {
    $(".fp-navbar-dropdown-item-open").click(function(e){
        if (window.matchMedia('(max-width: 992px)').matches) {
            $(this).next().next().stop().fadeToggle(200);
        }
    });

    $(".fp-navbar-dropdown-item").hover(function(e){
        if (window.matchMedia('(min-width: 992px)').matches) {
            $(this).find(".fp-navbar-dropdown-menu").stop().fadeToggle(200);
        }
    });
}

menuDropdownMobile();



// Client Menu
$(".toggle-client-menu").click(function (e) {
    e.preventDefault();

    $(".client-menu .mobile-none").toggleClass("mobile-show");

    if ($(this).find("i").hasClass("ri-menu-line")) {
        $(this).find("i").removeClass("ri-menu-line");
        $(this).find("i").addClass("ri-close-line");
    } else {
        $(this).find("i").addClass("ri-menu-line");
        $(this).find("i").removeClass("ri-close-line");
    }

});


// Order Item
$(".fp-order-item .head").click(function (e) {
    e.preventDefault();
    $(this).next().toggle();
});


// Auth Toggle
$(".show-register-form").click(function(e) {
    $(".auth-register-form").show();
    $(".auth-login-form").hide();
});

$(".show-login-form").click(function(e) {
    $(".auth-login-form").show();
    $(".auth-register-form").hide();
});


var currentPageUrl = window.location.href;

$('.client-menu a').each(function() {
    if ($(this).attr('href') === currentPageUrl) {
        $(this).addClass('active');
    }
});


// Notification
$(".notification").click(function(e) {
    e.preventDefault();
    $(this).next().toggle();
});

$(document).click(function(e) {
    if (!$(e.target).closest(".right-link.notification").length && !$(e.target).closest(".fp-nav-notification-menu").length) {
        $(".fp-nav-notification-menu").hide();
    }
});
