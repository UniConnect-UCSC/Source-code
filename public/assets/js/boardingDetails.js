document.addEventListener('DOMContentLoaded', function () {
    var track = document.getElementById('details-carousel-track');
    var prevBtn = document.getElementById('carousel-prev');
    var nextBtn = document.getElementById('carousel-next');

    if (track && prevBtn && nextBtn) {
        var slides = track.querySelectorAll('.details-carousel-image');
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

    var lightbox = document.getElementById('boarding-photo-lightbox');
    var lightboxImage = document.getElementById('boarding-photo-lightbox-image');
    var lightboxClose = document.getElementById('boarding-photo-lightbox-close');
    var lightboxPrev = document.getElementById('boarding-photo-lightbox-prev');
    var lightboxNext = document.getElementById('boarding-photo-lightbox-next');
    var lightboxThumbs = document.getElementById('boarding-photo-lightbox-thumbs');
    var clickableImages = document.querySelectorAll('.details-carousel-track img.details-carousel-image');
    var lightboxImages = Array.from(clickableImages).map(function (img) {
        return {
            src: img.src,
            alt: img.alt || 'Enlarged boarding photo'
        };
    });
    var lightboxIndex = 0;

    function renderLightboxImage() {
        if (!lightboxImage || !lightboxImages.length) return;

        var active = lightboxImages[lightboxIndex];
        lightboxImage.src = active.src;
        lightboxImage.alt = active.alt;

        if (lightboxThumbs) {
            var thumbButtons = lightboxThumbs.querySelectorAll('.boarding-photo-lightbox-thumb');
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
            thumbBtn.className = 'boarding-photo-lightbox-thumb';
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
        img.style.cursor = 'zoom-in';
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

    var button = document.getElementById('contact-reveal-btn');
    var number = document.getElementById('contact-reveal-number');
    if (!button || !number) return;

    button.addEventListener('click', function () {
        var isHidden = number.hasAttribute('hidden');
        if (isHidden) {
            number.removeAttribute('hidden');
            button.setAttribute('aria-expanded', 'true');
            button.innerHTML = '<i data-lucide="phone"></i> Hide Contact';
        } else {
            number.setAttribute('hidden', 'hidden');
            button.setAttribute('aria-expanded', 'false');
            button.innerHTML = '<i data-lucide="phone"></i> Show Contact';
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
});
