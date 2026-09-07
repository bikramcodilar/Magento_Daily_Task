document.addEventListener('DOMContentLoaded', function () {
    const questions = document.querySelectorAll('.faq-question');
    const filters = document.querySelectorAll('.faq-filter');
    const faqItems = document.querySelectorAll('.faq-item');
    const searchInput = document.querySelector('#faq-search');
    let selectedCategory = 'all';
    questions.forEach(function (question) {
        question.addEventListener('click', function () {
            const currentItem = question.parentElement;
            const currentAnswer = currentItem.querySelector('.faq-answer');
            const isOpen = currentItem.classList.contains('active');
            faqItems.forEach(function (item) {
                item.classList.remove('active');
                item.querySelector('.faq-answer').style.display = 'none';
            });
            if (!isOpen) {
                currentItem.classList.add('active');
                currentAnswer.style.display = 'block';
            }
        });
    });
    filters.forEach(function (filter) {
        filter.addEventListener('click', function () {
            selectedCategory = filter.dataset.category;
            filters.forEach(function (button) {
                button.classList.remove('active');
            });
            filter.classList.add('active');
            filterFaqs();
        });
    });
    searchInput.addEventListener('input', function () {
        filterFaqs();
    });
    function filterFaqs() {
        const searchText = searchInput.value.toLowerCase();
        faqItems.forEach(function (item) {
            item.classList.remove('active');
            item.querySelector('.faq-answer').style.display = 'none';
        });
        faqItems.forEach(function (item) {
            const category = item.dataset.category;
            const text = item.textContent.toLowerCase();
            const categoryMatches =
                selectedCategory === 'all' ||
                selectedCategory === category;
            const searchMatches =
                text.includes(searchText);
            if (categoryMatches && searchMatches) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
});
