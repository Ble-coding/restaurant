function toggleCouponSection() {
    const couponSection = document.getElementById('couponSection');
    if (couponSection.classList.contains('open')) {
        couponSection.classList.remove('open'); // Cache la section
    } else {
        couponSection.classList.add('open'); // Affiche la section
    }
}