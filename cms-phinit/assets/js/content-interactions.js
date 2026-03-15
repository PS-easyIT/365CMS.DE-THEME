/**
 * CMS Phinit Theme – Content Interactions
 * vanilla JS, kein jQuery, ES2020+
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        initTocHighlight();
        initInlineToc();
        initShareButtons();
        initCodeCopyButtons();
    });

    function initTocHighlight() {
        const toc = document.querySelector('.toc-list');
        const headings = document.querySelectorAll('.post-body h2, .post-body h3');
        if (!toc || !headings.length) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;

                    const id = entry.target.id;
                    toc.querySelectorAll('a').forEach((link) => {
                        link.classList.toggle('active', link.getAttribute('href') === '#' + id);
                    });
                });
            },
            { rootMargin: '-20% 0px -70% 0px' }
        );

        headings.forEach((heading) => {
            if (heading.id) {
                observer.observe(heading);
            }
        });
    }

    function initInlineToc() {
        const inlineTocs = document.querySelectorAll('[data-inline-toc]');
        if (!inlineTocs.length) return;

        inlineTocs.forEach((toc) => {
            const summary = toc.querySelector('summary');
            if (!summary) return;

            toc.open = false;

            const syncState = () => {
                summary.setAttribute('aria-expanded', toc.open ? 'true' : 'false');
                toc.classList.toggle('is-open', toc.open);
            };

            syncState();
            toc.addEventListener('toggle', syncState);

            toc.querySelectorAll('a[href^="#"]').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 1024) {
                        window.setTimeout(() => {
                            toc.open = false;
                        }, 120);
                    }
                });
            });
        });
    }

    function initShareButtons() {
        document.querySelectorAll('[data-share-copy]').forEach((copyBtn) => {
            copyBtn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    const originalText = copyBtn.textContent;
                    copyBtn.textContent = '✓ Kopiert!';
                    window.setTimeout(() => {
                        copyBtn.textContent = originalText;
                    }, 2000);
                } catch (_error) {
                }
            });
        });

        document.querySelectorAll('[data-share-print]').forEach((printBtn) => {
            printBtn.addEventListener('click', () => {
                window.print();
            });
        });
    }

    function initCodeCopyButtons() {
        document.querySelectorAll('pre > code').forEach((codeEl) => {
            const pre = codeEl.parentElement;
            if (!pre || pre.querySelector('.code-copy-btn')) return;

            const btn = document.createElement('button');
            btn.className = 'code-copy-btn';
            btn.setAttribute('aria-label', 'Code kopieren');
            btn.setAttribute('title', 'Code kopieren');
            btn.textContent = '\uD83D\uDCCB';
            pre.appendChild(btn);

            btn.addEventListener('click', async () => {
                const code = codeEl.textContent || '';

                try {
                    await navigator.clipboard.writeText(code);
                    btn.textContent = '\u2713';
                    btn.classList.add('copied');
                    window.setTimeout(() => {
                        btn.textContent = '\uD83D\uDCCB';
                        btn.classList.remove('copied');
                    }, 2000);
                } catch (_error) {
                    const range = document.createRange();
                    range.selectNodeContents(codeEl);
                    window.getSelection()?.removeAllRanges();
                    window.getSelection()?.addRange(range);
                }
            });
        });
    }
})();