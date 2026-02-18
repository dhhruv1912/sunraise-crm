{{-- <footer class="content-footer footer bg-footer-theme">
    <div class="container-fluid">
        <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
            <div class="text-body mb-2 mb-md-0">
                © {{ date('Y') }}
            </div>
        </div>
    </div>
</footer>

<div class="content-backdrop fade"></div> --}}
{{-- resources/views/temp/footer.blade.php --}}

<footer class="sr-footer">
    <div class="sr-footer__inner">

        {{-- LEFT --}}
        <div class="sr-footer__left">
            <span>
                © {{ date('Y') }}
                <strong class="text-primary">Sunraise Industries</strong>
            </span>
            <span class="sr-footer__divider">•</span>
            <span class="text-muted">
                All rights reserved
            </span>
        </div>

    </div>
</footer>

{{-- =========================
     Footer Styles
========================= --}}
<style>
/* Footer shell */
.sr-footer {
    background: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
    margin-top: auto;
}

/* Inner layout */
.sr-footer__inner {
    height: 52px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8125rem;
}

/* Left */
.sr-footer__left {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--bs-secondary-color);
}

.sr-footer__divider {
    opacity: .5;
}

/* Right */
.sr-footer__right {
    display: flex;
    align-items: center;
    gap: 14px;
}

/* Links */
.sr-footer__link {
    text-decoration: none;
    color: var(--bs-secondary-color);
    transition: color .15s ease;
}

.sr-footer__link:hover {
    color: var(--bs-primary);
}

/* Responsive */
@media (max-width: 768px) {
    .sr-footer__inner {
        flex-direction: column;
        gap: 6px;
        height: auto;
        padding: 10px 16px;
    }
}
</style>
