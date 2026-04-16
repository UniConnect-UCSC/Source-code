(function () {
    function openReviewModal(options) {
        options = options || {};

        const mode = options.mode || 'create';
        const kuppiId = String(options.kuppi_id || options.id || '');
        const reviewId = String(options.review_id || '');
        const hostId = String(options.host_id || '');

        // Remove any existing instance
        const old = document.getElementById('reviewModal');
        if (old) old.remove();

        let selectedRating = Math.max(0, Math.min(5, Number(options.rating || 0)));

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
        if (mode === 'view') {
            title.textContent = options.title || 'Review Details';
        } else if (mode === 'edit') {
            title.textContent = 'Edit Your Review';
        } else {
            title.textContent = 'Review this Kuppi';
        }
        body.appendChild(title);

        const intro = document.createElement('p');
        if (mode === 'view') {
            intro.textContent = options.date ? ('Session Date: ' + options.date) : 'Review information';
        } else {
            intro.textContent = 'How would you rate this kuppi session?';
        }
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

            if (mode !== 'view') {
                star.onmouseenter = () => highlightStars(i);
                star.onmouseleave = () => highlightStars(selectedRating);
                star.onclick = () => {
                    selectedRating = i;
                    highlightStars(i);
                    ratingLabel.textContent = ratingTexts[i] || '';
                };
            }

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
    ratingLabel.textContent = ratingTexts[Math.round(selectedRating)] || '';
        body.appendChild(ratingLabel);
    highlightStars(selectedRating);

        // ── Optional comment textarea ──
        const commentLabel = document.createElement('label');
    commentLabel.textContent = mode === 'view' ? 'Comment' : 'Comment (optional)';
        commentLabel.className = 'review-comment-label';
        body.appendChild(commentLabel);

        const textarea = document.createElement('textarea');
        textarea.className = 'review-textarea';
    textarea.placeholder = 'Share your experience...';
        textarea.rows = 3;
    textarea.value = String(options.comment || '');
    textarea.readOnly = mode === 'view';
        body.appendChild(textarea);

        // ── Error message ──
        const errorMsg = document.createElement('div');
        errorMsg.className = 'review-error';
        body.appendChild(errorMsg);

        // ── Button row ──
        const btnRow = document.createElement('div');
        btnRow.className = 'kuppi-modal-actions';

        const closeLabel = mode === 'view' ? 'Close' : 'Cancel';
        const closeActionBtn = document.createElement('button');
        closeActionBtn.type = 'button';
        closeActionBtn.className = 'btn';
        closeActionBtn.textContent = closeLabel;
        closeActionBtn.onclick = () => overlay.remove();
        btnRow.appendChild(closeActionBtn);

        if (mode !== 'view') {
            const submitBtn = document.createElement('button');
            submitBtn.type = 'button';
            submitBtn.className = 'btn btn-primary';
            submitBtn.textContent = mode === 'edit' ? 'Save Changes' : 'Submit Review';
            submitBtn.onclick = () => {
                if (selectedRating === 0) {
                    errorMsg.textContent = 'Please select a star rating.';
                    return;
                }
                errorMsg.textContent = '';
                submitBtn.disabled = true;
                submitBtn.textContent = mode === 'edit' ? 'Saving...' : 'Submitting...';

                const endpoint = mode === 'edit' ? '/kuppi/updateReview' : '/kuppi/review';
                const payload = {
                    rating: selectedRating,
                    comment: textarea.value.trim(),
                    host_id: hostId
                };

                if (mode === 'edit') {
                    payload.review_id = reviewId;
                    payload.kuppi_id = kuppiId;
                } else {
                    payload.kuppi_id = kuppiId;
                }

                Ajax.jsonPost(endpoint, payload)
                    .then((response) => {
                        if (response && response.success) {
                            body.innerHTML = '';
                            const successIcon = document.createElement('div');
                            successIcon.className = 'review-success';
                            successIcon.innerHTML = '&#10003;';
                            body.appendChild(successIcon);

                            const successMsg = document.createElement('p');
                            successMsg.className = 'review-success-text';
                            successMsg.textContent = mode === 'edit'
                                ? 'Review updated successfully!'
                                : 'Thank you for your review!';
                            body.appendChild(successMsg);

                            if (window.newReviewedKuppiScroll && mode === 'edit') {
                                window.newReviewedKuppiScroll.resetScroll();
                                window.newReviewedKuppiScroll.loadNextElements();
                            }

                            setTimeout(() => overlay.remove(), 1200);
                            return;
                        }

                        errorMsg.textContent = (response && response.message) || 'Failed to save review. Please try again.';
                        submitBtn.disabled = false;
                        submitBtn.textContent = mode === 'edit' ? 'Save Changes' : 'Submit Review';
                    })
                    .catch(() => {
                        errorMsg.textContent = 'Something went wrong. Please try again.';
                        submitBtn.disabled = false;
                        submitBtn.textContent = mode === 'edit' ? 'Save Changes' : 'Submit Review';
                    });
            };
            btnRow.appendChild(submitBtn);
        }

        body.appendChild(btnRow);

        modal.appendChild(body);
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
    }

    window.openReviewModal = openReviewModal;
})();