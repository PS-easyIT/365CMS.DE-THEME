/**
 * CMS Phinit Theme – Content Interactions
 * vanilla JS, kein jQuery, ES2020+
 */
(function () {
    'use strict';

    const boot = () => {
        initTocAnchorLinks();
        initTocHighlight();
        initInlineToc();
        initPowerShellHighlighting();
        initShareButtons();
        initCodeCopyButtons();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }

    function initTocAnchorLinks() {
        const links = document.querySelectorAll('.toc-link[href^="#"], .toc-inline__link[href^="#"], .page-toc__link[href^="#"], [data-cms-toc-root] a[href^="#"]');
        if (!links.length) return;

        links.forEach((link) => {
            link.addEventListener('click', (event) => {
                const href = link.getAttribute('href') || '';
                const id = decodeHash(href);
                if (!id) return;

                const target = document.getElementById(id);
                if (!target) return;

                event.preventDefault();
                scrollToHeading(target);
                updateLocationHash(id);
                markActiveTocLink(link);
            });
        });
    }

    function decodeHash(href) {
        const raw = href.startsWith('#') ? href.slice(1) : '';
        if (!raw) return '';

        try {
            return decodeURIComponent(raw);
        } catch (_error) {
            return raw;
        }
    }

    function getStickyOffset() {
        const header = document.querySelector('.site-header, #site-header');
        const quicklinks = document.querySelector('.quicklinks-bar, .header-quicklinks');
        const memberBar = document.querySelector('.member-bar, .site-member-bar');
        const heightOf = (element) => element ? Math.ceil(element.getBoundingClientRect().height) : 0;

        return heightOf(header) + heightOf(quicklinks) + heightOf(memberBar) + 18;
    }

    function scrollToHeading(target) {
        const top = target.getBoundingClientRect().top + window.scrollY - getStickyOffset();
        window.scrollTo({
            top: Math.max(0, top),
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
        });
    }

    function updateLocationHash(id) {
        const encoded = '#' + encodeURIComponent(id);
        if (window.location.hash === encoded) return;

        if (history.pushState) {
            history.pushState(null, '', encoded);
            return;
        }

        window.location.hash = encoded;
    }

    function markActiveTocLink(activeLink) {
        document.querySelectorAll('.toc-link.active, .toc-inline__link.active, .page-toc__link.active').forEach((link) => {
            link.classList.remove('active');
        });
        activeLink.classList.add('active');
    }

    function initTocHighlight() {
        const toc = document.querySelector('.toc-list');
        const headings = document.querySelectorAll('.post-body h2, .post-body h3, .post-body h4, .post-body h5, .post-body h6, .page-content h2, .page-content h3, .page-content h4, .page-content h5, .page-content h6');
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

    function initPowerShellHighlighting() {
        document.querySelectorAll('pre > code').forEach((codeEl) => {
            const classNames = Array.from(codeEl.classList, (className) => className.toLowerCase());
            const isPowerShell = classNames.some((className) => [
                'language-powershell',
                'language-ps',
                'language-ps1',
                'language-pwsh'
            ].includes(className));

            if (!isPowerShell || codeEl.dataset.syntaxHighlighted === 'true') {
                return;
            }

            const pre = codeEl.parentElement;
            if (pre) {
                pre.classList.add('code-block--powershell');
                pre.dataset.codeLanguage = 'PowerShell';
            }

            const original = codeEl.textContent || '';
            if (original.trim() === '') {
                codeEl.dataset.syntaxHighlighted = 'true';
                return;
            }

            codeEl.innerHTML = highlightPowerShell(original);
            codeEl.dataset.syntaxHighlighted = 'true';
        });
    }

    function highlightPowerShell(source) {
        const placeholders = [];
        let working = source;

        const token = (html) => {
            const marker = `%%PS_TOKEN_${placeholders.length}%%`;
            placeholders.push({ marker, html });
            return marker;
        };

        const wrapToken = (className, value) => token(`<span class="ps-token ${className}">${escapeHtml(value)}</span>`);

        working = working.replace(/@"[\s\S]*?"@|@'[\s\S]*?'@/g, (match) => wrapToken('ps-string', match));
        working = working.replace(/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"/g, (match) => wrapToken('ps-string', match));
        working = working.replace(/(^|\s)(#.*)$/gm, (match, prefix, comment) => `${prefix}${wrapToken('ps-comment', comment)}`);
        working = working.replace(/\[[A-Za-z_][A-Za-z0-9_.\[\]]*\]/g, (match) => wrapToken('ps-type', match));
        working = working.replace(/\$[A-Za-z_][\w:.-]*/g, (match) => wrapToken('ps-variable', match));
        working = working.replace(/(^|\s)(-[A-Za-z][\w-]*)/g, (match, prefix, parameter) => `${prefix}${wrapToken('ps-parameter', parameter)}`);
        working = working.replace(/\b(?:function|filter|param|dynamicparam|begin|process|end|if|else|elseif|switch|foreach|for|while|do|until|return|try|catch|finally|throw|trap|break|continue|in|class|enum|default|data|parallel|workflow)\b/gi, (match) => wrapToken('ps-keyword', match));
        working = working.replace(/\$(?:true|false|null)\b/gi, (match) => wrapToken('ps-constant', match));
        working = working.replace(/\b[A-Za-z]+(?:-[A-Za-z0-9]+)+\b/g, (match) => wrapToken('ps-cmdlet', match));
        working = working.replace(/\b\d+(?:\.\d+)?\b/g, (match) => wrapToken('ps-number', match));
        working = working.replace(/[|=]+>|\|\||&&|\|/g, (match) => wrapToken('ps-operator', match));

        let html = escapeHtml(working);
        placeholders.forEach(({ marker, html: replacement }) => {
            html = html.replace(marker, replacement);
        });

        return html;
    }

    function escapeHtml(value) {
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
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