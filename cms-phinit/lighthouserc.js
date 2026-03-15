'use strict';

const baseUrl = String(process.env.LHCI_BASE_URL || '').trim().replace(/\/$/, '');

if (baseUrl === '') {
    throw new Error('LHCI_BASE_URL muss für cms-phinit/lighthouserc.js gesetzt sein.');
}

const resolveUrl = (pathValue, fallbackPath) => {
    const raw = String(pathValue || fallbackPath || '').trim();
    if (raw === '') {
        return `${baseUrl}/`;
    }

    if (/^https?:\/\//i.test(raw)) {
        return raw;
    }

    return `${baseUrl}${raw.startsWith('/') ? raw : `/${raw}`}`;
};

module.exports = {
    ci: {
        collect: {
            numberOfRuns: 3,
            url: [
                resolveUrl(process.env.LHCI_HOME_PATH, '/'),
                resolveUrl(process.env.LHCI_BLOG_PATH, '/blog'),
                resolveUrl(process.env.LHCI_POST_PATH, '/blog'),
                resolveUrl(process.env.LHCI_MEMBER_SECURITY_PATH, '/member/security'),
            ],
            settings: {
                chromeFlags: '--no-sandbox --disable-dev-shm-usage',
                preset: 'mobile',
            },
        },
        assert: {
            assertions: {
                'categories:performance': ['warn', { minScore: 0.8 }],
                'largest-contentful-paint': ['warn', { maxNumericValue: 3000 }],
                'cumulative-layout-shift': ['error', { maxNumericValue: 0.1 }],
                'interaction-to-next-paint': ['warn', { maxNumericValue: 200 }],
                'unused-javascript': ['warn', { maxNumericValue: 0 }],
            },
        },
        upload: {
            target: 'temporary-public-storage',
        },
    },
};