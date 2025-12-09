document.getElementById('toggleReviewBtn').addEventListener('click', function() {
    const form = document.getElementById('reviewFormContainer');

    form.style.display = (form.style.display === 'none' || form.style.display === '') 
        ? 'block' 
        : 'none';
});

