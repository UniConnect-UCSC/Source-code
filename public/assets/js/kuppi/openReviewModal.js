(function () {
    function openReviewModal({ id , host_id}) {
        // Remove any existing instance
        const old = document.getElementById('reviewModal');
        if (old) old.remove();

        let selectedRating = 0;

        // Overlay
        const overlay = document.createElement('div');
        overlay.id = 'reviewModal';
        overlay.className = 'kuppi-modal-overlay';

        // Modal shell
        const modal = document.createElement('div');
        modal.className = 'kuppi-modal-content';

        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'kuppi-modal-close';
        closeBtn.innerHTML = '&times;';
        closeBtn.onclick = () => overlay.remove();
        modal.appendChild(closeBtn);

        // Body
        const body = document.createElement('div');
        body.id = 'reviewModalBody';

        // Title
        const title = document.createElement('h3');
        title.textContent = 'Review this Kuppi';
        body.appendChild(title);

        const intro = document.createElement('p');
        intro.textContent = 'How would you rate this kuppi session?';
        intro.className = 'review-intro';
        body.appendChild(intro);

        // ── Star rating row ──
        const starsRow = document.createElement('div');
        starsRow.className = 'review-stars';

        const stars = [];
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('span');
            star.className = 'review-star';
            star.dataset.value = i;
            star.innerHTML = '&#9733;'; // ★
            star.setAttribute('role', 'button');
            star.setAttribute('aria-label', i + ' star' + (i > 1 ? 's' : ''));

            star.onmouseenter = () => highlightStars(i);
            star.onmouseleave = () => highlightStars(selectedRating);
            star.onclick = () => {
                selectedRating = i;
                highlightStars(i);
                ratingLabel.textContent = ratingTexts[i] || '';
            };

            starsRow.appendChild(star);
            stars.push(star);
        }

        function highlightStars(count) {
            stars.forEach((s, idx) => {
                s.classList.toggle('active', idx < count);
            });
        }

        const ratingTexts = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };

        body.appendChild(starsRow);

        // Rating label below stars
        const ratingLabel = document.createElement('div');
        ratingLabel.className = 'review-rating-label';
        body.appendChild(ratingLabel);

        // ── Optional comment textarea ──
        const commentLabel = document.createElement('label');
        commentLabel.textContent = 'Comment (optional)';
        commentLabel.className = 'review-comment-label';
        body.appendChild(commentLabel);

        const textarea = document.createElement('textarea');
        textarea.className = 'review-textarea';
        textarea.placeholder = 'Share your experience…';
        textarea.rows = 3;
        body.appendChild(textarea);

        // ── Error message ──
        const errorMsg = document.createElement('div');
        errorMsg.className = 'review-error';
        body.appendChild(errorMsg);

        // ── Button row ──
        const btnRow = document.createElement('div');
        btnRow.className = 'kuppi-modal-actions';

        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn';
        cancelBtn.textContent = 'Cancel';
        cancelBtn.onclick = () => overlay.remove();

        const submitBtn = document.createElement('button');
        submitBtn.type = 'button';
        submitBtn.className = 'btn btn-primary';
        submitBtn.textContent = 'Submit Review';
        submitBtn.onclick = () => {
            if (selectedRating === 0) {
                errorMsg.textContent = 'Please select a star rating.';
                return;
            }
            errorMsg.textContent = '';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting…';

            const data = {
                kuppi_id: id,
                host_id: host_id,
                rating: selectedRating,
                comment: textarea.value.trim()
            };

            Ajax.jsonPost('/kuppi/review', data)
                .then((response) => {
                    if (response.success) {
                        // Show brief success state before closing
                        body.innerHTML = '';
                        const successIcon = document.createElement('div');
                        successIcon.className = 'review-success';
                        successIcon.innerHTML = '&#10003;';
                        body.appendChild(successIcon);

                        const successMsg = document.createElement('p');
                        successMsg.className = 'review-success-text';
                        successMsg.textContent = 'Thank you for your review!';
                        body.appendChild(successMsg);

                        setTimeout(() => overlay.remove(), 1500);
                    } else {
                        errorMsg.textContent = response.message || 'Failed to submit review. Please try again.';
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit Review';
                    }
                })
                .catch(() => {
                    errorMsg.textContent = 'Something went wrong. Please try again.';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Review';
                });
        };

        btnRow.appendChild(cancelBtn);
        btnRow.appendChild(submitBtn);
        body.appendChild(btnRow);

        modal.appendChild(body);
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
    }

    window.openReviewModal = openReviewModal;
})();