/**
 * CMS Phinit Theme – Customizer Admin Interactions
 */
(function () {
    'use strict';

    const COLOR_PRESETS = {
        phinit: {primary_color:'#1e3a5f',primary_dark:'#0f2340',primary_mid:'#1a3255',primary_light:'#2a4f7c',accent_color:'#e8a838',accent_hover:'#d4922a',accent_blue:'#4a9eff',accent_blue2:'#2d7dd2',accent_teal:'#0d9488',accent_teal_light:'#14b8a6',bg_header1:'#111827',bg_header2:'#162030',bg_header3:'#0e1a28',bg_primary:'#ffffff',bg_secondary:'#f1f5f9',bg_dark:'#0a0f1a',text_primary:'#1e293b',text_secondary:'#4a5568',text_muted:'#7a8898',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#b0bec5',text_nav_dropdown:'#e2e8f0',logo_suffix_color:'#e8a838',border_light:'#dde3ea',footer_bg:'#0d1828',footer_bottom_bg:'#080d15',footer_border:'#2d7dd2',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#2d7dd2',progress_bar_end:'#e8a838'},
        bluesteel: {primary_color:'#1a2744',primary_dark:'#0d1a33',primary_mid:'#162140',primary_light:'#233b6e',accent_color:'#3b82f6',accent_hover:'#2563eb',accent_blue:'#60a5fa',accent_blue2:'#3b82f6',accent_teal:'#0ea5e9',accent_teal_light:'#38bdf8',bg_header1:'#0d1a33',bg_header2:'#111f3d',bg_header3:'#091528',bg_primary:'#f8fafc',bg_secondary:'#eff6ff',bg_dark:'#060d1a',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#e2e8f0',logo_suffix_color:'#60a5fa',border_light:'#e2e8f0',footer_bg:'#0b1630',footer_bottom_bg:'#060e1e',footer_border:'#3b82f6',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#3b82f6',progress_bar_end:'#60a5fa'},
        greentech: {primary_color:'#064e3b',primary_dark:'#022c22',primary_mid:'#065f46',primary_light:'#047857',accent_color:'#10b981',accent_hover:'#059669',accent_blue:'#34d399',accent_blue2:'#10b981',accent_teal:'#0d9488',accent_teal_light:'#2dd4bf',bg_header1:'#022c22',bg_header2:'#0a3728',bg_header3:'#001a14',bg_primary:'#f0fdf4',bg_secondary:'#ecfdf5',bg_dark:'#01110b',text_primary:'#064e3b',text_secondary:'#065f46',text_muted:'#6b7280',text_nav:'#d1fae5',text_nav_member:'#d1fae5',text_nav_main:'#d1fae5',text_nav_quicklinks:'#6ee7b7',text_nav_dropdown:'#d1fae5',logo_suffix_color:'#10b981',border_light:'#d1fae5',footer_bg:'#031c15',footer_bottom_bg:'#010e0a',footer_border:'#10b981',success_color:'#10b981',error_color:'#ef4444',progress_bar_start:'#10b981',progress_bar_end:'#2dd4bf'},
        slate: {primary_color:'#1e293b',primary_dark:'#0f172a',primary_mid:'#1c2944',primary_light:'#334155',accent_color:'#f59e0b',accent_hover:'#d97706',accent_blue:'#818cf8',accent_blue2:'#6366f1',accent_teal:'#06b6d4',accent_teal_light:'#22d3ee',bg_header1:'#0f172a',bg_header2:'#1e293b',bg_header3:'#0b1120',bg_primary:'#ffffff',bg_secondary:'#f8fafc',bg_dark:'#060c16',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#f1f5f9',text_nav_member:'#f1f5f9',text_nav_main:'#f1f5f9',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#f1f5f9',logo_suffix_color:'#f59e0b',border_light:'#e2e8f0',footer_bg:'#0c1527',footer_bottom_bg:'#060b15',footer_border:'#6366f1',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#6366f1',progress_bar_end:'#f59e0b'},
        ruby: {primary_color:'#7f1d1d',primary_dark:'#450a0a',primary_mid:'#6b1b1b',primary_light:'#991b1b',accent_color:'#ef4444',accent_hover:'#dc2626',accent_blue:'#f87171',accent_blue2:'#ef4444',accent_teal:'#f59e0b',accent_teal_light:'#fbbf24',bg_header1:'#1c0a0a',bg_header2:'#280d0d',bg_header3:'#140707',bg_primary:'#fffbfb',bg_secondary:'#fef2f2',bg_dark:'#0a0404',text_primary:'#1c0707',text_secondary:'#450a0a',text_muted:'#6b7280',text_nav:'#fee2e2',text_nav_member:'#fee2e2',text_nav_main:'#fee2e2',text_nav_quicklinks:'#fca5a5',text_nav_dropdown:'#fee2e2',logo_suffix_color:'#f59e0b',border_light:'#fecaca',footer_bg:'#1a0707',footer_bottom_bg:'#0d0404',footer_border:'#ef4444',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#ef4444',progress_bar_end:'#f59e0b'}
    };

    const GOOGLE_FONT_FAMILIES = {
        'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'Open+Sans',
        'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
        'source-sans':'Source+Sans+3','nunito':'Nunito',
        'space-grotesk':'Space+Grotesk',
        'sora':'Sora',
        'barlow-condensed':'Barlow+Condensed','roboto-condensed':'Roboto+Condensed',
        'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'Exo+2',
        'jetbrains-mono':'JetBrains+Mono','fira-code':'Fira+Code','source-code':'Source+Code+Pro'
    };

    const FONT_FAMILY_NAMES = {
        'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'"Open Sans"',
        'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
        'source-sans':'"Source Sans 3"','nunito':'Nunito','system':'system-ui,sans-serif',
        'space-grotesk':'"Space Grotesk"',
        'sora':'"Sora"',
        'barlow-condensed':'"Barlow Condensed"','roboto-condensed':'"Roboto Condensed"',
        'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'"Exo 2"',
        'jetbrains-mono':'"JetBrains Mono",monospace','fira-code':'"Fira Code",monospace',
        'source-code':'"Source Code Pro",monospace','cascadia':'"Cascadia Code",monospace',
        'system-mono':'monospace'
    };

    function readConfig() {
        const configElement = document.getElementById('phinit-customizer-config');
        if (!configElement) {
            return {};
        }

        try {
            return JSON.parse(configElement.textContent || '{}');
        } catch (error) {
            return {};
        }
    }

    function initCustomizerAdmin() {
        const config = readConfig();
        const form = document.getElementById('customizer-form');
        const hint = document.getElementById('unsaved-hint');
        const pxDrawer = document.getElementById('px-drawer');
        const pxIframe = document.getElementById('px-iframe');
        const pxLabel = document.getElementById('px-label');
        const pxDevBtns = document.querySelectorAll('.px-dev-btn');
        const confirmOverlay = document.getElementById('phinit-customizer-confirm');
        const confirmMessage = document.getElementById('phinit-customizer-confirm-message');
        const confirmAccept = confirmOverlay ? confirmOverlay.querySelector('[data-confirm-accept]') : null;
        const confirmCancel = confirmOverlay ? confirmOverlay.querySelector('[data-confirm-cancel]') : null;
        const loadedFonts = new Set();
        let changed = false;
        let pendingConfirmButton = null;

        function markChanged() {
            if (!changed) {
                changed = true;
                if (hint) {
                    hint.classList.add('is-visible');
                }
            }
        }

        function syncColor(cpId, txtId, hiddenId) {
            const cp = document.getElementById(cpId);
            const txt = document.getElementById(txtId);
            const hid = document.getElementById(hiddenId);
            if (cp && txt) {
                txt.value = cp.value;
            }
            if (hid && cp) {
                hid.value = cp.value;
            }
            markChanged();
        }

        function syncColorTxt(cpId, txtId, hiddenId) {
            const cp = document.getElementById(cpId);
            const txt = document.getElementById(txtId);
            const hid = document.getElementById(hiddenId);
            const value = txt ? txt.value : '';

            if (cp && /^#[0-9a-f]{6}$/i.test(value)) {
                cp.value = value;
            }
            if (hid) {
                hid.value = value;
            }
            markChanged();
        }

        const menuEditors = new Map();

        function createMenuItem(label, url, nextIdRef) {
            const id = 'menu-item-' + nextIdRef.value++;
            return {
                id,
                label: label || '',
                url: url || '',
                children: []
            };
        }

        function parseMenuTree(rawValue, nextIdRef) {
            const root = [];
            const stack = [{ depth: -1, children: root }];
            const lines = String(rawValue || '').replace(/\t/g, '  ').split(/\r?\n/);

            lines.forEach((lineRaw) => {
                const line = String(lineRaw || '').replace(/\s+$/, '');
                if (!line.trim()) {
                    return;
                }

                const leadingSpaces = line.length - line.trimStart().length;
                const depth = Math.max(0, Math.floor(leadingSpaces / 2));
                const content = line.trim();
                const separatorIndex = content.indexOf('|');
                const label = (separatorIndex >= 0 ? content.slice(0, separatorIndex) : content).trim();
                const url = (separatorIndex >= 0 ? content.slice(separatorIndex + 1) : '').trim();
                if (!label) {
                    return;
                }

                while (stack.length > 1 && stack[stack.length - 1].depth >= depth) {
                    stack.pop();
                }

                const parent = stack[stack.length - 1];
                const item = createMenuItem(label, url, nextIdRef);
                parent.children.push(item);
                stack.push({ depth, children: item.children });
            });

            return root;
        }

        function serializeMenuTreeLines(items, depth) {
            const lines = [];
            const indent = '  '.repeat(Math.max(0, depth));
            items.forEach((item) => {
                if (!item || !String(item.label || '').trim()) {
                    return;
                }
                lines.push(indent + String(item.label).trim() + ' | ' + String(item.url || '').trim());
                if (Array.isArray(item.children) && item.children.length > 0) {
                    lines.push(...serializeMenuTreeLines(item.children, depth + 1));
                }
            });
            return lines;
        }

        function serializeMenuTree(items) {
            return serializeMenuTreeLines(items, 0).join('\n');
        }

        function findMenuItemContext(items, id, parentList = null, parentItem = null) {
            for (let index = 0; index < items.length; index += 1) {
                const item = items[index];
                if (!item) {
                    continue;
                }
                if (item.id === id) {
                    return { item, index, list: items, parentList, parentItem };
                }
                if (Array.isArray(item.children) && item.children.length > 0) {
                    const found = findMenuItemContext(item.children, id, items, item);
                    if (found) {
                        return found;
                    }
                }
            }
            return null;
        }

        function syncMenuEditorHiddenInput(editorState) {
            editorState.hiddenInput.value = serializeMenuTree(editorState.items);
        }

        function focusMenuRow(editorState, itemId) {
            if (!itemId) {
                return;
            }
            const row = editorState.listElement.querySelector('[data-menu-row-id="' + itemId + '"]');
            if (row instanceof HTMLElement) {
                row.focus({ preventScroll: true });
            }
        }

        function buildMenuTreeDom(editorState, items, depth) {
            const fragment = document.createDocumentFragment();
            const levelLabel = editorState.text.levelLabel;

            items.forEach((item, index) => {
                const listItem = document.createElement('div');
                listItem.className = 'phinit-menu-editor__item';
                listItem.dataset.menuItemId = item.id;
                listItem.setAttribute('role', 'treeitem');
                listItem.setAttribute('aria-level', String(depth + 1));
                listItem.setAttribute('aria-setsize', String(items.length));
                listItem.setAttribute('aria-posinset', String(index + 1));

                const row = document.createElement('div');
                row.className = 'phinit-menu-editor__row';
                row.dataset.menuRowId = item.id;
                row.setAttribute('tabindex', '0');
                row.setAttribute('aria-label', levelLabel + ' ' + (depth + 1) + ': ' + (item.label || editorState.text.labelField));
                listItem.appendChild(row);

                const meta = document.createElement('div');
                meta.className = 'phinit-menu-editor__meta';
                meta.textContent = levelLabel + ' ' + (depth + 1);
                row.appendChild(meta);

                const labelInput = document.createElement('input');
                labelInput.type = 'text';
                labelInput.className = 'form-control form-control-sm phinit-menu-editor__input';
                labelInput.value = item.label || '';
                labelInput.placeholder = editorState.text.labelField;
                labelInput.dataset.menuInput = 'label';
                labelInput.dataset.menuId = item.id;
                labelInput.setAttribute('aria-label', editorState.text.labelField);
                row.appendChild(labelInput);

                const urlInput = document.createElement('input');
                urlInput.type = 'text';
                urlInput.inputMode = 'url';
                urlInput.className = 'form-control form-control-sm phinit-menu-editor__input';
                urlInput.value = item.url || '';
                urlInput.placeholder = editorState.text.urlField;
                urlInput.dataset.menuInput = 'url';
                urlInput.dataset.menuId = item.id;
                urlInput.setAttribute('aria-label', editorState.text.urlField);
                row.appendChild(urlInput);

                const actions = document.createElement('div');
                actions.className = 'phinit-menu-editor__actions';
                actions.setAttribute('role', 'group');
                actions.setAttribute('aria-label', editorState.text.reorderGroup);
                row.appendChild(actions);

                const actionSpecs = [
                    ['up', '↑', editorState.text.moveUpLabel],
                    ['down', '↓', editorState.text.moveDownLabel],
                    ['indent', '⇥', editorState.text.indentLabel],
                    ['outdent', '⇤', editorState.text.outdentLabel],
                    ['add-after', '+', editorState.text.addAfterLabel],
                    ['add-child', '↳', editorState.text.addChildLabel],
                    ['remove', '✕', editorState.text.removeLabel]
                ];

                actionSpecs.forEach(([action, label, ariaLabel]) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-sm btn-outline-secondary phinit-menu-editor__action-btn';
                    button.dataset.menuAction = action;
                    button.dataset.menuId = item.id;
                    button.textContent = label;
                    button.setAttribute('aria-label', ariaLabel);
                    actions.appendChild(button);
                });

                if (Array.isArray(item.children) && item.children.length > 0) {
                    const childContainer = document.createElement('div');
                    childContainer.className = 'phinit-menu-editor__children';
                    childContainer.setAttribute('role', 'group');
                    childContainer.appendChild(buildMenuTreeDom(editorState, item.children, depth + 1));
                    listItem.appendChild(childContainer);
                }

                fragment.appendChild(listItem);
            });

            return fragment;
        }

        function renderMenuEditor(editorState, focusItemId) {
            editorState.listElement.innerHTML = '';
            syncMenuEditorHiddenInput(editorState);

            if (!Array.isArray(editorState.items) || editorState.items.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'phinit-menu-editor__empty';
                empty.textContent = editorState.text.emptyLabel;
                editorState.listElement.appendChild(empty);
                return;
            }

            editorState.listElement.appendChild(buildMenuTreeDom(editorState, editorState.items, 0));
            if (focusItemId) {
                focusMenuRow(editorState, focusItemId);
            }
        }

        function executeMenuAction(editorState, action, menuId) {
            if (!action) {
                return;
            }

            const context = menuId ? findMenuItemContext(editorState.items, menuId) : null;
            let focusItemId = menuId;
            let changedStructure = false;

            if (action === 'add-root') {
                const newItem = createMenuItem('', '', editorState.nextIdRef);
                editorState.items.push(newItem);
                focusItemId = newItem.id;
                changedStructure = true;
            } else if (!context) {
                return;
            } else if (action === 'add-after') {
                const newItem = createMenuItem('', '', editorState.nextIdRef);
                context.list.splice(context.index + 1, 0, newItem);
                focusItemId = newItem.id;
                changedStructure = true;
            } else if (action === 'add-child') {
                const newItem = createMenuItem('', '', editorState.nextIdRef);
                context.item.children = Array.isArray(context.item.children) ? context.item.children : [];
                context.item.children.push(newItem);
                focusItemId = newItem.id;
                changedStructure = true;
            } else if (action === 'remove') {
                context.list.splice(context.index, 1);
                const replacement = context.list[Math.max(0, context.index - 1)] || context.list[context.index] || null;
                focusItemId = replacement ? replacement.id : null;
                changedStructure = true;
            } else if (action === 'up' && context.index > 0) {
                const previous = context.list[context.index - 1];
                context.list[context.index - 1] = context.item;
                context.list[context.index] = previous;
                changedStructure = true;
            } else if (action === 'down' && context.index < context.list.length - 1) {
                const next = context.list[context.index + 1];
                context.list[context.index + 1] = context.item;
                context.list[context.index] = next;
                changedStructure = true;
            } else if (action === 'indent' && context.index > 0) {
                const previousSibling = context.list[context.index - 1];
                previousSibling.children = Array.isArray(previousSibling.children) ? previousSibling.children : [];
                context.list.splice(context.index, 1);
                previousSibling.children.push(context.item);
                changedStructure = true;
            } else if (action === 'outdent' && Array.isArray(context.parentList)) {
                const parentContext = findMenuItemContext(editorState.items, context.parentItem ? context.parentItem.id : '');
                if (parentContext) {
                    context.list.splice(context.index, 1);
                    parentContext.list.splice(parentContext.index + 1, 0, context.item);
                    changedStructure = true;
                }
            }

            if (changedStructure) {
                renderMenuEditor(editorState, focusItemId);
                markChanged();
            }
        }

        function initMenuEditors() {
            document.querySelectorAll('[data-menu-editor]').forEach((editorElement) => {
                if (!(editorElement instanceof HTMLElement) || menuEditors.has(editorElement)) {
                    return;
                }

                const hiddenInput = editorElement.parentElement
                    ? editorElement.parentElement.querySelector('[data-menu-source]')
                    : null;
                const listElement = editorElement.querySelector('[data-menu-tree]');
                if (!(hiddenInput instanceof HTMLTextAreaElement) || !(listElement instanceof HTMLElement)) {
                    return;
                }

                const nextIdRef = { value: 1 };
                const state = {
                    editorElement,
                    hiddenInput,
                    listElement,
                    nextIdRef,
                    items: parseMenuTree(hiddenInput.value, nextIdRef),
                    text: {
                        levelLabel: editorElement.dataset.menuLevelLabel || 'Level',
                        emptyLabel: editorElement.dataset.menuEmptyLabel || 'No menu items yet.',
                        addAfterLabel: editorElement.dataset.menuAddAfterLabel || 'Add below',
                        addChildLabel: editorElement.dataset.menuAddChildLabel || 'Add child',
                        removeLabel: editorElement.dataset.menuRemoveLabel || 'Remove item',
                        moveUpLabel: editorElement.dataset.menuUpLabel || 'Move up',
                        moveDownLabel: editorElement.dataset.menuDownLabel || 'Move down',
                        indentLabel: editorElement.dataset.menuIndentLabel || 'Move deeper',
                        outdentLabel: editorElement.dataset.menuOutdentLabel || 'Move higher',
                        labelField: editorElement.dataset.menuLabelField || 'Label',
                        urlField: editorElement.dataset.menuUrlField || 'URL',
                        reorderGroup: editorElement.dataset.menuReorderGroupLabel || 'Menu item actions'
                    }
                };

                menuEditors.set(editorElement, state);
                renderMenuEditor(state);
            });
        }

        function syncAllMenuEditors() {
            menuEditors.forEach((editorState) => {
                syncMenuEditorHiddenInput(editorState);
            });
        }

        window.syncColor = syncColor;
        window.syncColorTxt = syncColorTxt;

        function toggleCollapseByTarget(targetId, forceExpanded) {
            if (!targetId) {
                return;
            }

            const panel = document.getElementById(targetId);
            if (!(panel instanceof HTMLElement)) {
                return;
            }

            const toggleButtons = document.querySelectorAll('[data-collapse-target="' + targetId + '"]');
            const isExpanded = forceExpanded !== undefined ? forceExpanded : panel.hidden;
            panel.hidden = !isExpanded;

            if (isExpanded && panel.hasAttribute('data-lazy-panel')) {
                panel.removeAttribute('data-lazy-panel');
            }

            toggleButtons.forEach((button) => {
                if (button instanceof HTMLElement) {
                    button.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                }
            });
        }

        function initWidgetOrderControls() {
            document.querySelectorAll('[data-widget-order-control]').forEach((control) => {
                const input = control.querySelector('[data-widget-order-input]');
                const list = control.querySelector('[data-widget-order-list]');

                if (!(input instanceof HTMLInputElement) || !(list instanceof HTMLElement)) {
                    return;
                }

                const syncOrder = () => {
                    const items = Array.from(list.querySelectorAll('[data-widget-order-item]'));
                    input.value = items
                        .map((item) => item instanceof HTMLElement ? (item.dataset.widgetKey || '') : '')
                        .filter(Boolean)
                        .join('\n');

                    items.forEach((item, index) => {
                        const upButton = item.querySelector('[data-widget-order-action="up"]');
                        const downButton = item.querySelector('[data-widget-order-action="down"]');
                        if (upButton instanceof HTMLButtonElement) {
                            upButton.disabled = index === 0;
                        }
                        if (downButton instanceof HTMLButtonElement) {
                            downButton.disabled = index === items.length - 1;
                        }
                    });
                };

                syncOrder();
            });
        }

        function syncAllWidgetOrderControls() {
            document.querySelectorAll('[data-widget-order-control]').forEach((control) => {
                const input = control.querySelector('[data-widget-order-input]');
                const list = control.querySelector('[data-widget-order-list]');

                if (!(input instanceof HTMLInputElement) || !(list instanceof HTMLElement)) {
                    return;
                }

                input.value = Array.from(list.querySelectorAll('[data-widget-order-item]'))
                    .map((item) => item instanceof HTMLElement ? (item.dataset.widgetKey || '') : '')
                    .filter(Boolean)
                    .join('\n');
            });
        }

        initWidgetOrderControls();
        initMenuEditors();

        function closeConfirmModal() {
            if (!confirmOverlay) {
                return;
            }

            confirmOverlay.hidden = true;
            confirmOverlay.setAttribute('aria-hidden', 'true');
            pendingConfirmButton?.focus();
        }

        function openConfirmModal(button) {
            if (!confirmOverlay || !confirmMessage) {
                return;
            }

            pendingConfirmButton = button;
            confirmMessage.textContent = button.dataset.confirmMessage || 'Möchtest du fortfahren?';
            confirmOverlay.hidden = false;
            confirmOverlay.setAttribute('aria-hidden', 'false');
            confirmAccept?.focus();
        }

        confirmCancel?.addEventListener('click', closeConfirmModal);
        confirmOverlay?.addEventListener('click', function (event) {
            if (event.target === confirmOverlay) {
                closeConfirmModal();
            }
        });
        confirmAccept?.addEventListener('click', function () {
            if (!pendingConfirmButton) {
                closeConfirmModal();
                return;
            }

            const submitTargetId = pendingConfirmButton.dataset.confirmSubmitTarget || '';
            const submitTarget = submitTargetId ? document.getElementById(submitTargetId) : null;
            closeConfirmModal();
            submitTarget?.click();
        });

        if (form) {
            form.addEventListener('change', function (event) {
                if (event.target instanceof HTMLInputElement || event.target instanceof HTMLTextAreaElement || event.target instanceof HTMLSelectElement) {
                    markChanged();
                }
            });
            form.addEventListener('input', function (event) {
                if (event.target instanceof HTMLInputElement || event.target instanceof HTMLTextAreaElement || event.target instanceof HTMLSelectElement) {
                    markChanged();
                }
            });
            form.addEventListener('submit', function () {
                syncAllMenuEditors();
                syncAllWidgetOrderControls();
                changed = false;
                hint?.classList.remove('is-visible');
            });
        }

        document.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 's') {
                event.preventDefault();
                if (form) {
                    form.requestSubmit();
                }
            }

            if (event.key === 'Escape' && confirmOverlay && !confirmOverlay.hidden) {
                event.preventDefault();
                closeConfirmModal();
            }
        });

        window.addEventListener('beforeunload', function (event) {
            if (!changed) {
                return;
            }

            event.preventDefault();
            event.returnValue = '';
        });

        function pxOpen() {
            if (!pxDrawer) {
                return;
            }

            pxDrawer.style.display = 'flex';
            pxDrawer.removeAttribute('aria-hidden');
            if (pxIframe && !pxIframe.src && config.siteOrigin) {
                pxIframe.src = config.siteOrigin;
            }
        }

        function pxClose() {
            if (!pxDrawer) {
                return;
            }

            pxDrawer.style.display = 'none';
            pxDrawer.setAttribute('aria-hidden', 'true');
        }

        function pxRefresh() {
            if (!pxIframe || !pxIframe.src) {
                return;
            }

            const currentSrc = pxIframe.src;
            pxIframe.src = '';
            pxIframe.src = currentSrc;
        }

        function pxSetDevice(width) {
            if (!pxIframe) {
                return;
            }

            pxIframe.style.width = width + 'px';
            const labels = {1280:'Desktop (1280 px)', 768:'Tablet (768 px)', 375:'Mobil (375 px)'};
            if (pxLabel) {
                pxLabel.textContent = labels[width] || (width + ' px');
            }

            pxDevBtns.forEach((button) => {
                button.classList.toggle('active', Number(button.dataset.width) === width);
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && pxDrawer && pxDrawer.style.display !== 'none') {
                pxClose();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (!event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) {
                return;
            }

            const target = event.target instanceof HTMLElement ? event.target : null;
            if (!target) {
                return;
            }

            const row = target.closest('[data-menu-row-id]');
            if (!(row instanceof HTMLElement)) {
                return;
            }

            const editorElement = row.closest('[data-menu-editor]');
            if (!(editorElement instanceof HTMLElement)) {
                return;
            }
            const editorState = menuEditors.get(editorElement);
            if (!editorState) {
                return;
            }

            const itemId = row.dataset.menuRowId || '';
            let action = '';
            if (event.key === 'ArrowUp') {
                action = 'up';
            } else if (event.key === 'ArrowDown') {
                action = 'down';
            } else if (event.key === 'ArrowRight') {
                action = 'indent';
            } else if (event.key === 'ArrowLeft') {
                action = 'outdent';
            }

            if (!action) {
                return;
            }

            event.preventDefault();
            executeMenuAction(editorState, action, itemId);
        });
        document.addEventListener('input', function (event) {
            const target = event.target instanceof HTMLElement ? event.target : null;
            if (!target) {
                return;
            }

            if (target instanceof HTMLInputElement && target.matches('[data-menu-input][data-menu-id]')) {
                const editorElement = target.closest('[data-menu-editor]');
                if (!(editorElement instanceof HTMLElement)) {
                    return;
                }
                const editorState = menuEditors.get(editorElement);
                if (!editorState) {
                    return;
                }
                const itemId = target.dataset.menuId || '';
                const inputType = target.dataset.menuInput || '';
                const context = findMenuItemContext(editorState.items, itemId);
                if (!context) {
                    return;
                }
                if (inputType === 'label') {
                    context.item.label = target.value;
                } else if (inputType === 'url') {
                    context.item.url = target.value;
                }
                syncMenuEditorHiddenInput(editorState);
                markChanged();
                return;
            }

            if (target.matches('[data-color-picker]')) {
                syncColor(target.id, target.dataset.syncTargetText || '', target.dataset.syncTargetHidden || '');
                return;
            }

            if (target.matches('[data-color-text]')) {
                syncColorTxt(target.dataset.syncTargetPicker || '', target.id, target.dataset.syncTargetHidden || '');
            }
        });

        document.addEventListener('click', function (event) {
            const clickTarget = event.target instanceof Element ? event.target : null;
            if (!clickTarget) {
                return;
            }

            const menuActionButton = clickTarget.closest('[data-menu-action]');
            if (menuActionButton instanceof HTMLElement) {
                const editorElement = menuActionButton.closest('[data-menu-editor]');
                if (!(editorElement instanceof HTMLElement)) {
                    return;
                }
                const editorState = menuEditors.get(editorElement);
                if (!editorState) {
                    return;
                }
                event.preventDefault();
                executeMenuAction(editorState, menuActionButton.dataset.menuAction || '', menuActionButton.dataset.menuId || '');
                return;
            }

            const collapseToggle = clickTarget.closest('[data-collapse-toggle]');
            if (collapseToggle instanceof HTMLElement) {
                const targetId = collapseToggle.dataset.collapseTarget || '';
                if (targetId) {
                    event.preventDefault();
                    const isExpanded = collapseToggle.getAttribute('aria-expanded') === 'true';
                    toggleCollapseByTarget(targetId, !isExpanded);
                }
                return;
            }

            const confirmTrigger = clickTarget.closest('[data-confirm-message][data-confirm-submit-target]');
            if (confirmTrigger instanceof HTMLElement) {
                event.preventDefault();
                openConfirmModal(confirmTrigger);
                return;
            }

            const widgetActionButton = clickTarget.closest('[data-widget-order-action]');
            if (widgetActionButton instanceof HTMLElement) {
                const item = widgetActionButton.closest('[data-widget-order-item]');
                const list = widgetActionButton.closest('[data-widget-order-list]');
                if (!(item instanceof HTMLElement) || !(list instanceof HTMLElement)) {
                    return;
                }

                const action = widgetActionButton.dataset.widgetOrderAction || '';
                if (action === 'up' && item.previousElementSibling) {
                    list.insertBefore(item, item.previousElementSibling);
                } else if (action === 'down' && item.nextElementSibling) {
                    list.insertBefore(item.nextElementSibling, item);
                } else {
                    return;
                }

                const control = list.closest('[data-widget-order-control]');
                if (!(control instanceof HTMLElement)) {
                    return;
                }
                const input = control.querySelector('[data-widget-order-input]');
                if (!(input instanceof HTMLInputElement)) {
                    return;
                }

                const orderedItems = Array.from(list.querySelectorAll('[data-widget-order-item]'));
                input.value = orderedItems
                    .map((orderedItem) => orderedItem instanceof HTMLElement ? (orderedItem.dataset.widgetKey || '') : '')
                    .filter(Boolean)
                    .join('\n');

                orderedItems.forEach((orderedItem, index) => {
                    const upButton = orderedItem.querySelector('[data-widget-order-action="up"]');
                    const downButton = orderedItem.querySelector('[data-widget-order-action="down"]');
                    if (upButton instanceof HTMLButtonElement) {
                        upButton.disabled = index === 0;
                    }
                    if (downButton instanceof HTMLButtonElement) {
                        downButton.disabled = index === orderedItems.length - 1;
                    }
                });

                markChanged();
                item.focus({preventScroll: true});
                return;
            }

            if (clickTarget.closest('#preview-toggle-btn') || clickTarget.closest('#preview-toggle-btn-secondary')) {
                event.preventDefault();
                pxOpen();
                return;
            }

            if (clickTarget.closest('#px-close-btn')) {
                event.preventDefault();
                pxClose();
                return;
            }

            if (clickTarget.closest('#px-refresh-btn')) {
                event.preventDefault();
                pxRefresh();
                return;
            }

            const deviceButton = clickTarget.closest('.px-dev-btn');
            if (deviceButton instanceof HTMLElement) {
                event.preventDefault();
                pxSetDevice(Number(deviceButton.dataset.width || '1280'));
                return;
            }

            const presetButton = clickTarget.closest('.color-preset-btn');
            if (presetButton instanceof HTMLElement) {
                const preset = COLOR_PRESETS[presetButton.dataset.preset || ''];
                if (!preset) {
                    return;
                }

                Object.entries(preset).forEach(([key, value]) => {
                    const cp = document.getElementById('f_colors_' + key);
                    const txt = document.getElementById('f_colors_' + key + '_txt');
                    const hid = document.getElementById('colors_' + key);
                    if (cp instanceof HTMLInputElement) {
                        cp.value = value;
                    }
                    if (txt instanceof HTMLInputElement) {
                        txt.value = value;
                    }
                    if (hid instanceof HTMLInputElement) {
                        hid.value = value;
                    }
                });

                markChanged();
            }
        });

        function loadFont(slug) {
            if (loadedFonts.has(slug)) {
                return;
            }

            loadedFonts.add(slug);
            const link = document.createElement('link');
            link.rel = 'stylesheet';

            if (config.preferLocalFonts && config.localFontCssMap && config.localFontCssMap[slug]) {
                link.href = config.localFontCssMap[slug];
                document.head.appendChild(link);
                return;
            }

            if (!GOOGLE_FONT_FAMILIES[slug]) {
                return;
            }

            link.href = 'https://fonts.googleapis.com/css2?family=' + GOOGLE_FONT_FAMILIES[slug] + ':wght@400;700&display=swap';
            document.head.appendChild(link);
        }

        ['f_typography_font_family_ui', 'f_typography_font_family_brand', 'f_typography_font_family_code'].forEach((fieldId) => {
            const select = document.getElementById(fieldId);
            if (!select) {
                return;
            }

            const preview = document.createElement('div');
            preview.className = 'phinit-customizer__font-preview';
            preview.textContent = 'AaBbCc 0123 – PowerShell & M365 Administration';
            select.after(preview);

            const updatePreview = (value) => {
                loadFont(value);
                preview.style.fontFamily = FONT_FAMILY_NAMES[value] || 'inherit';
            };

            updatePreview(select.value);
            select.addEventListener('change', function (event) {
                updatePreview(event.target.value);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomizerAdmin, { once: true });
    } else {
        initCustomizerAdmin();
    }
})();