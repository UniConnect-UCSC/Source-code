document.addEventListener('DOMContentLoaded', function () {
    var track = document.getElementById('marketplace-details-track');
    var prevBtn = document.getElementById('marketplace-details-prev');
    var nextBtn = document.getElementById('marketplace-details-next');

    if (track && prevBtn && nextBtn) {
        var slides = track.querySelectorAll('.marketplace-details-image');
        var currentIndex = 0;
        var maxIndex = slides.length - 1;

        function updateCarousel() {
            track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
        }

        prevBtn.addEventListener('click', function () {
            currentIndex = currentIndex <= 0 ? maxIndex : currentIndex - 1;
            updateCarousel();
        });

        nextBtn.addEventListener('click', function () {
            currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
            updateCarousel();
        });
    }

    var revealBtn = document.getElementById('marketplace-contact-reveal-btn');
    var contactNumber = document.getElementById('marketplace-contact-number');
    if (revealBtn && contactNumber) {
        revealBtn.addEventListener('click', function () {
            var isHidden = contactNumber.hasAttribute('hidden');
            if (isHidden) {
                contactNumber.removeAttribute('hidden');
                revealBtn.setAttribute('aria-expanded', 'true');
                revealBtn.innerHTML = '<i data-lucide="phone"></i> Hide Contact';
            } else {
                contactNumber.setAttribute('hidden', 'hidden');
                revealBtn.setAttribute('aria-expanded', 'false');
                revealBtn.innerHTML = '<i data-lucide="phone"></i> Show Contact';
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }

    var lightbox = document.getElementById('marketplace-photo-lightbox');
    var lightboxImage = document.getElementById('marketplace-photo-lightbox-image');
    var lightboxClose = document.getElementById('marketplace-photo-lightbox-close');
    var lightboxPrev = document.getElementById('marketplace-photo-lightbox-prev');
    var lightboxNext = document.getElementById('marketplace-photo-lightbox-next');
    var lightboxThumbs = document.getElementById('marketplace-photo-lightbox-thumbs');
    var clickableImages = document.querySelectorAll('.marketplace-details-track img.marketplace-details-image');
    var lightboxImages = Array.from(clickableImages).map(function (img) {
        return {
            src: img.src,
            alt: img.alt || 'Enlarged item photo'
        };
    });
    var lightboxIndex = 0;

    function renderLightboxImage() {
        if (!lightboxImage || !lightboxImages.length) return;

        var active = lightboxImages[lightboxIndex];
        lightboxImage.src = active.src;
        lightboxImage.alt = active.alt;

        if (lightboxThumbs) {
            var thumbButtons = lightboxThumbs.querySelectorAll('.marketplace-photo-lightbox-thumb');
            thumbButtons.forEach(function (thumb, index) {
                thumb.classList.toggle('is-active', index === lightboxIndex);
            });
        }
    }

    function renderThumbnails() {
        if (!lightboxThumbs || !lightboxImages.length) return;

        lightboxThumbs.innerHTML = '';
        lightboxImages.forEach(function (image, index) {
            var thumbBtn = document.createElement('button');
            thumbBtn.type = 'button';
            thumbBtn.className = 'marketplace-photo-lightbox-thumb';
            thumbBtn.setAttribute('aria-label', 'Open photo ' + (index + 1));

            var thumbImg = document.createElement('img');
            thumbImg.src = image.src;
            thumbImg.alt = image.alt;

            thumbBtn.appendChild(thumbImg);
            thumbBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                lightboxIndex = index;
                renderLightboxImage();
            });

            lightboxThumbs.appendChild(thumbBtn);
        });
    }

    function openLightbox(index) {
        if (!lightbox || !lightboxImage || !lightboxImages.length) return;
        lightboxIndex = index;
        renderLightboxImage();
        lightbox.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lightbox || !lightboxImage) return;
        lightbox.setAttribute('hidden', 'hidden');
        lightboxImage.src = '';
        document.body.style.overflow = '';
    }

    function showNextLightboxImage() {
        if (!lightboxImages.length) return;
        lightboxIndex = (lightboxIndex + 1) % lightboxImages.length;
        renderLightboxImage();
    }

    function showPreviousLightboxImage() {
        if (!lightboxImages.length) return;
        lightboxIndex = (lightboxIndex - 1 + lightboxImages.length) % lightboxImages.length;
        renderLightboxImage();
    }

    renderThumbnails();

    clickableImages.forEach(function (img, index) {
        img.addEventListener('click', function () {
            openLightbox(index);
        });
    });

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }

    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', function (e) {
            e.stopPropagation();
            showPreviousLightboxImage();
        });
    }

    if (lightboxNext) {
        lightboxNext.addEventListener('click', function (e) {
            e.stopPropagation();
            showNextLightboxImage();
        });
    }

    if (lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (!lightbox || lightbox.hasAttribute('hidden')) return;

        if (e.key === 'Escape') {
            closeLightbox();
        }

        if (e.key === 'ArrowLeft') {
            showPreviousLightboxImage();
        }

        if (e.key === 'ArrowRight') {
            showNextLightboxImage();
        }
    });
});
