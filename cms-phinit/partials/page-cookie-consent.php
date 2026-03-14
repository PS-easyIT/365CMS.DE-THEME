<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$consentOverview = is_array($page['consent_overview'] ?? null) ? $page['consent_overview'] : [];
$consentCategories = is_array($consentOverview['categories'] ?? null) ? $consentOverview['categories'] : [];
$policyUrl = (string)($consentOverview['policy_url'] ?? '/datenschutz');
$preferencesUrl = (string)($consentOverview['preferences_url'] ?? (SITE_URL . '/cookie-einstellungen'));
$updatedAt = trim((string)($consentOverview['updated_at'] ?? ''));
$serviceCount = (int)($consentOverview['service_count'] ?? 0);
$categoryCount = count($consentCategories);
$matomo = is_array($consentOverview['matomo'] ?? null) ? $consentOverview['matomo'] : [];

$allServices = [];
foreach ($consentCategories as $category) {
    foreach ((array)($category['services'] ?? []) as $service) {
        $service['category_name'] = (string)($category['name'] ?? 'Kategorie');
        $service['category_slug'] = (string)($category['slug'] ?? '');
        $allServices[] = $service;
    }
}

$highlights = [
    ['icon' => '🛡️', 'title' => 'Datensparsamkeit', 'text' => 'Optionale Services bleiben bis zu deiner Auswahl deaktiviert. Nur technisch notwendige Funktionen laufen immer.'],
    ['icon' => '🧭', 'title' => 'Volle Kontrolle', 'text' => 'Du kannst jederzeit zwischen kompletter Zustimmung, individueller Auswahl oder Ablehnung wechseln.'],
    ['icon' => '🔍', 'title' => 'Transparenz', 'text' => 'Alle Kategorien, Services, Cookie-Namen und dokumentierten Hinweise sind hier öffentlich nachvollziehbar.'],
];
?>

<section class="phinit-consent" data-cms-consent-page data-cms-consent-state="loading">
    <header class="phinit-consent__hero" data-anim>
        <div class="phinit-consent__hero-copy">
            <span class="phinit-consent__eyebrow">Datenschutz · Transparenz · Kontrolle</span>
            <h1 class="phinit-consent__title"><?php echo htmlspecialchars((string)($page['title'] ?? 'Cookie-Einstellungen & Einwilligung'), ENT_QUOTES); ?></h1>
            <p class="phinit-consent__lead">
                Hier verwaltest du deine Einwilligung, prüfst alle aktiven Kategorien und siehst transparent,
                welche Services in 365CMS dokumentiert sind — inklusive Feed-, Analyse- und Medien-Diensten.
            </p>
            <div class="phinit-consent__actions">
                <button type="button" class="btn btn-primary" data-cms-consent-action="preferences">Auswahl anpassen</button>
                <button type="button" class="btn btn-accent" data-cms-consent-action="accept-all">Alle akzeptieren</button>
                <button type="button" class="btn btn-ghost" data-cms-consent-action="reject">Ablehnen</button>
            </div>
        </div>

        <div class="phinit-consent__hero-meta">
            <article class="phinit-consent__stat-card">
                <span class="phinit-consent__stat-label">Kategorien</span>
                <strong><?php echo (int)$categoryCount; ?></strong>
            </article>
            <article class="phinit-consent__stat-card">
                <span class="phinit-consent__stat-label">Services</span>
                <strong><?php echo (int)$serviceCount; ?></strong>
            </article>
            <article class="phinit-consent__stat-card">
                <span class="phinit-consent__stat-label">Zuletzt geprüft</span>
                <strong><?php echo htmlspecialchars($updatedAt !== '' ? $updatedAt : 'Derzeit nicht dokumentiert', ENT_QUOTES); ?></strong>
            </article>
            <article class="phinit-consent__status-card">
                <span class="phinit-consent__status-label">Aktueller Status</span>
                <strong data-cms-consent-status-text>Consent wird geladen …</strong>
                <p data-cms-consent-status-detail>Bitte einen Moment — wir lesen deine aktuelle Auswahl aus dem Browser.</p>
            </article>
        </div>
    </header>

    <section class="phinit-consent__highlights" data-anim data-anim-delay="1">
        <?php foreach ($highlights as $highlight): ?>
            <article class="phinit-consent__highlight-card">
                <span class="phinit-consent__highlight-icon" aria-hidden="true"><?php echo htmlspecialchars($highlight['icon'], ENT_QUOTES); ?></span>
                <h2><?php echo htmlspecialchars($highlight['title'], ENT_QUOTES); ?></h2>
                <p><?php echo htmlspecialchars($highlight['text'], ENT_QUOTES); ?></p>
            </article>
        <?php endforeach; ?>
    </section>

    <div class="phinit-consent__layout">
        <section class="phinit-consent__panel phinit-consent__panel--summary" data-anim data-anim-delay="2">
            <div class="phinit-consent__panel-head">
                <h2>Deine Auswahl im Überblick</h2>
                <a href="<?php echo htmlspecialchars($policyUrl, ENT_QUOTES); ?>" class="phinit-consent__inline-link">Datenschutz ansehen</a>
            </div>
            <ul class="phinit-consent__bullet-list">
                <li>Essenzielle Dienste bleiben immer aktiv und sichern Login, Sicherheit und Formularfunktionen.</li>
                <li>Optionale Kategorien steuern Analyse, externe Medien und zusätzliche Komfortfunktionen.</li>
                <li>Änderungen wirken sofort und können jederzeit über denselben Dialog neu gesetzt werden.</li>
            </ul>
            <div class="phinit-consent__mini-actions">
                <a class="btn btn-ghost btn-sm" href="<?php echo htmlspecialchars($preferencesUrl, ENT_QUOTES); ?>">Seite neu laden</a>
                <a class="btn btn-sm btn-ghost" href="<?php echo htmlspecialchars($policyUrl, ENT_QUOTES); ?>">Datenschutz lesen</a>
            </div>
        </section>

        <aside class="phinit-consent__panel phinit-consent__panel--sticky" data-anim data-anim-delay="3">
            <div class="phinit-consent__panel-head">
                <h2>Schnellzugriff</h2>
            </div>
            <nav class="phinit-consent__anchor-nav" aria-label="Cookie-Einstellungen Bereiche">
                <a href="#consent-categories">Kategorien</a>
                <a href="#consent-services">Services</a>
                <a href="#consent-transparency">Transparenz</a>
            </nav>
            <div class="phinit-consent__note">
                <strong>Hinweis zu CMS Feed</strong>
                <p>Öffentliche Feed-Inhalte werden nur nach Freigabe der passenden Kategorie geladen und bleiben bei Ablehnung ausgeblendet.</p>
            </div>
        </aside>
    </div>

    <section class="phinit-consent__section" id="consent-categories" data-anim data-anim-delay="4">
        <div class="phinit-consent__section-head">
            <h2>Kategorien & Zustimmung</h2>
            <p>Jede Kategorie zeigt dir live, ob sie aktuell akzeptiert, abgelehnt oder immer aktiv ist.</p>
        </div>

        <div class="phinit-consent__category-grid">
            <?php foreach ($consentCategories as $category): ?>
                <?php $categorySlug = (string)($category['slug'] ?? ''); ?>
                <article class="phinit-consent__category-card" data-cms-consent-category="<?php echo htmlspecialchars($categorySlug, ENT_QUOTES); ?>">
                    <div class="phinit-consent__category-head">
                        <div>
                            <h3><?php echo htmlspecialchars((string)($category['name'] ?? 'Kategorie'), ENT_QUOTES); ?></h3>
                            <p><?php echo htmlspecialchars((string)($category['description'] ?? ''), ENT_QUOTES); ?></p>
                        </div>
                        <div class="phinit-consent__category-badges">
                            <span class="phinit-consent__category-status" data-cms-consent-category-status="<?php echo htmlspecialchars($categorySlug, ENT_QUOTES); ?>">Noch nicht gewählt</span>
                            <?php if (!empty($category['required'])): ?>
                                <span class="phinit-consent__pill phinit-consent__pill--required">Pflicht</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="phinit-consent__category-meta">
                        <span><?php echo count((array)($category['services'] ?? [])); ?> Service(s)</span>
                        <span>Slug: <code><?php echo htmlspecialchars($categorySlug, ENT_QUOTES); ?></code></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="phinit-consent__section" id="consent-services" data-anim data-anim-delay="5">
        <div class="phinit-consent__section-head">
            <h2>Dokumentierte Services</h2>
            <p>Hier findest du alle aktuell hinterlegten Dienste mit Anbieter, Kategorie und bekannten Cookie-Namen.</p>
        </div>

        <div class="phinit-consent__service-grid">
            <?php if ($allServices === []): ?>
                <article class="phinit-consent__service-card phinit-consent__service-card--empty">
                    <h3>Keine Services hinterlegt</h3>
                    <p>Im Cookie-Manager sind aktuell keine Einzelservices dokumentiert.</p>
                </article>
            <?php else: ?>
                <?php foreach ($allServices as $service): ?>
                    <article class="phinit-consent__service-card">
                        <div class="phinit-consent__service-head">
                            <div>
                                <h3><?php echo htmlspecialchars((string)($service['name'] ?? 'Service'), ENT_QUOTES); ?></h3>
                                <p><?php echo htmlspecialchars((string)($service['provider'] ?? 'Unbekannter Anbieter'), ENT_QUOTES); ?></p>
                            </div>
                            <span class="phinit-consent__pill"><?php echo htmlspecialchars((string)($service['category_name'] ?? 'Kategorie'), ENT_QUOTES); ?></span>
                        </div>

                        <?php if (trim((string)($service['description'] ?? '')) !== ''): ?>
                            <p class="phinit-consent__service-text"><?php echo htmlspecialchars((string)$service['description'], ENT_QUOTES); ?></p>
                        <?php endif; ?>

                        <dl class="phinit-consent__service-meta">
                            <div>
                                <dt>Slug</dt>
                                <dd><code><?php echo htmlspecialchars((string)($service['slug'] ?? ''), ENT_QUOTES); ?></code></dd>
                            </div>
                            <div>
                                <dt>Cookies</dt>
                                <dd><?php echo htmlspecialchars(trim((string)($service['cookie_names'] ?? '')) !== '' ? (string)$service['cookie_names'] : 'Keine Cookie-Namen dokumentiert', ENT_QUOTES); ?></dd>
                            </div>
                        </dl>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="phinit-consent__section" id="consent-transparency" data-anim data-anim-delay="6">
        <div class="phinit-consent__section-head">
            <h2>Transparenz & DSGVO-Hinweise</h2>
            <p>Zusätzliche Hinweise helfen dir einzuschätzen, wie Analyse- und Mediendienste betrieben werden.</p>
        </div>

        <div class="phinit-consent__transparency-grid">
            <article class="phinit-consent__panel">
                <div class="phinit-consent__panel-head">
                    <h3>Rechtliche Grundlagen</h3>
                </div>
                <ul class="phinit-consent__bullet-list">
                    <li>Essenzielle Dienste basieren auf technischer Erforderlichkeit.</li>
                    <li>Optionale Dienste werden erst nach deiner aktiven Auswahl freigegeben.</li>
                    <li>Widerruf und Anpassung sind jederzeit direkt über diese Seite möglich.</li>
                </ul>
            </article>

            <?php if (!empty($matomo['enabled'])): ?>
                <article class="phinit-consent__panel">
                    <div class="phinit-consent__panel-head">
                        <h3>Matomo-Dokumentation</h3>
                    </div>
                    <dl class="phinit-consent__service-meta phinit-consent__service-meta--stacked">
                        <?php if (!empty($matomo['url'])): ?>
                            <div>
                                <dt>Matomo-URL</dt>
                                <dd><a href="<?php echo htmlspecialchars((string)$matomo['url'], ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars((string)$matomo['url'], ENT_QUOTES); ?></a></dd>
                            </div>
                        <?php endif; ?>
                        <div>
                            <dt>Hosting</dt>
                            <dd><?php echo htmlspecialchars((string)($matomo['hosting_region'] ?? 'Deutschland / EU'), ENT_QUOTES); ?></dd>
                        </div>
                        <div>
                            <dt>IP-Anonymisierung</dt>
                            <dd><?php echo !empty($matomo['ip_anonymization']) ? 'Aktiv' : 'Nicht dokumentiert'; ?></dd>
                        </div>
                        <div>
                            <dt>Cookies deaktiviert</dt>
                            <dd><?php echo !empty($matomo['disable_cookies']) ? 'Ja' : 'Nein / nicht dokumentiert'; ?></dd>
                        </div>
                    </dl>
                </article>
            <?php endif; ?>
        </div>
    </section>
</section>