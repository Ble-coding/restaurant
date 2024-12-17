window.addEventListener('load', function() {
    const boissonSwiperElement = document.querySelector('.boisson-slider'); // Mise à jour du sélecteur
    const menuSwiperElement = document.querySelector('.menu-swiper');

    // console.log('Boisson swiper:', boissonSwiperElement);
    // console.log('Menu swiper:', menuSwiperElement);

    // S'assurer que les éléments sont visibles avant d'initialiser Swiper
    if (boissonSwiperElement && menuSwiperElement) {
        const boissonSwiper = new Swiper(boissonSwiperElement, {
            direction: 'vertical',
            loop: true,
            navigation: {
                nextEl: '.boisson-next',
                prevEl: '.boisson-prev',
            },
        });

        const menuSwiper = new Swiper(menuSwiperElement, {
            direction: 'vertical',
            loop: true,
            navigation: {
                nextEl: '.menu-next',
                prevEl: '.menu-prev',
            },
        });
    } else {
        console.error('Swiper elements not found');
    }
});
