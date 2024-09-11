document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const foodItemsContainer = document.getElementById('food-items');
    const swiperSlides = foodItemsContainer.getElementsByClassName('swiper-slide');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();

        Array.from(swiperSlides).forEach(slide => {
            const foodName = slide.querySelector('.card_info h2').textContent.toLowerCase();
            if (foodName.includes(filter)) {
                slide.style.display = '';
            } else {
                slide.style.display = 'none';
            }
        });
    });
});
