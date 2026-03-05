/**
 * flavor-flavor-flavor — Theme JavaScript
 *
 * Sticky CTA, FAQ accordion, gallery thumbnails,
 * "Kup teraz" AJAX, scroll reveal animations.
 */

(function () {
    'use strict';

    /* ─── Buy Now (AJAX → checkout) ───────────────────── */

    function initBuyNow() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.sjp-buy-now-btn');
            if (!btn) return;
            e.preventDefault();

            var productId = btn.dataset.productId;
            if (!productId || !window.sjpData) return;

            btn.classList.add('sjp-btn--loading');
            btn.setAttribute('disabled', 'disabled');

            var body = new FormData();
            body.append('action', 'sjp_buy_now');
            body.append('product_id', productId);
            body.append('quantity', '1');
            body.append('nonce', sjpData.nonce);

            fetch(sjpData.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: body,
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success && data.data.redirect) {
                        window.location.href = data.data.redirect;
                    } else {
                        btn.classList.remove('sjp-btn--loading');
                        btn.removeAttribute('disabled');
                    }
                })
                .catch(function () {
                    btn.classList.remove('sjp-btn--loading');
                    btn.removeAttribute('disabled');
                    if (sjpData.checkoutUrl) {
                        window.location.href = sjpData.checkoutUrl;
                    }
                });
        });
    }

    /* ─── FAQ Accordion ───────────────────────────────── */

    function initFaqAccordion() {
        document.querySelectorAll('.sjp-faq__question').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var expanded = this.getAttribute('aria-expanded') === 'true';
                var answer = this.nextElementSibling;

                this.setAttribute('aria-expanded', String(!expanded));

                if (expanded) {
                    answer.setAttribute('hidden', '');
                } else {
                    answer.removeAttribute('hidden');
                }
            });
        });
    }

    /* ─── Hero Gallery Thumbnails ─────────────────────── */

    function initGalleryThumbs() {
        var heroImage = document.querySelector('.sjp-hero__image');
        if (!heroImage) return;

        document.querySelectorAll('.sjp-hero__thumb').forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                var full = this.dataset.full;
                if (!full) return;

                heroImage.src = full;

                document.querySelectorAll('.sjp-hero__thumb').forEach(function (t) {
                    t.classList.remove('sjp-hero__thumb--active');
                });
                this.classList.add('sjp-hero__thumb--active');
            });
        });
    }

    /* ─── Sticky Mobile CTA ───────────────────────────── */

    function initStickyCta() {
        var stickyEl = document.getElementById('sjp-sticky-cta');
        if (!stickyEl) return;

        document.body.classList.add('sjp-has-sticky-cta');

        var heroCtaBtn = document.querySelector('.sjp-hero__cta-wrap .sjp-btn');
        if (!heroCtaBtn) {
            stickyEl.classList.add('sjp-sticky-mobile-cta--visible');
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        stickyEl.classList.remove('sjp-sticky-mobile-cta--visible');
                    } else {
                        stickyEl.classList.add('sjp-sticky-mobile-cta--visible');
                    }
                });
            },
            { threshold: 0, rootMargin: '0px 0px -80px 0px' }
        );

        observer.observe(heroCtaBtn);
    }

    /* ─── Scroll Reveal ───────────────────────────────── */

    function initScrollReveal() {
        var sections = document.querySelectorAll('.sjp-section');
        if (!sections.length) return;

        sections.forEach(function (s) {
            s.classList.add('sjp-reveal');
        });

        if (!('IntersectionObserver' in window)) {
            sections.forEach(function (s) {
                s.classList.add('sjp-reveal--visible');
            });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('sjp-reveal--visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
        );

        sections.forEach(function (s) {
            observer.observe(s);
        });
    }

    /* ─── Smooth Scroll for anchor links ──────────────── */

    function initSmoothScroll() {
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href^="#"]');
            if (!link) return;

            var target = document.querySelector(link.getAttribute('href'));
            if (!target) return;

            e.preventDefault();
            var offset = document.querySelector('.sjp-header')
                ? document.querySelector('.sjp-header').offsetHeight
                : 0;

            window.scrollTo({
                top: target.offsetTop - offset - 16,
                behavior: 'smooth',
            });
        });
    }

    /* ─── Init ────────────────────────────────────────── */

    function init() {
        initBuyNow();
        initFaqAccordion();
        initGalleryThumbs();
        initStickyCta();
        initScrollReveal();
        initSmoothScroll();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
