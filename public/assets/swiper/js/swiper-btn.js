document.addEventListener('DOMContentLoaded', function() {
    const boissonSwiperElement = document.querySelector('.boisson-slider'); 
    const menuSwiperElement = document.querySelector('.menu-swiper');

    if (menuSwiperElement) {
        new Swiper(menuSwiperElement, {
            loop: true,
            navigation: {
                nextEl: '.menu-next',
                prevEl: '.menu-prev',
            },
        });
    } else {
        console.error('Element .menu-swiper non trouvé');
    }

    if (boissonSwiperElement) {
        new Swiper(boissonSwiperElement, {
            loop: true,
            navigation: {
                nextEl: '.boisson-next',
                prevEl: '.boisson-prev',
            },
        });
    } else {
        console.error('Element .boisson-slider non trouvé');
    }
});
