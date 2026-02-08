/**
 * Home Page Cards widget: add card, remove card, drag to reorder.
 */
(function () {
    'use strict';

    function getListAndTemplate(btn) {
        var list = btn.closest('.crosstalk-cards-list');
        if (!list) {
            var widget = btn.closest('.widget-content') || btn.closest('.widget-form');
            var addWrap = btn.closest('.crosstalk-cards-add-wrap') || btn.closest('p');
            if (addWrap && addWrap.previousElementSibling && addWrap.previousElementSibling.classList.contains('crosstalk-cards-list')) {
                list = addWrap.previousElementSibling;
            } else if (widget) {
                list = widget.querySelector('.crosstalk-cards-list');
            }
        }
        var widget = btn.closest('.widget-content') || btn.closest('.widget-form');
        var template = widget ? widget.querySelector('.crosstalk-cards-template') : null;
        if (!template) template = document.querySelector('.crosstalk-cards-template');
        var base = list ? list.getAttribute('data-cards-base') : '';
        return { list: list, template: template, base: base };
    }

    function reindexCards(list) {
        if (!list || !list.getAttribute('data-cards-base')) return;
        var base = list.getAttribute('data-cards-base');
        var blocks = list.querySelectorAll('.crosstalk-cards-card-block');
        blocks.forEach(function (block, newIndex) {
            block.setAttribute('data-index', newIndex);
            block.querySelectorAll('input, textarea, select').forEach(function (el) {
                var name = el.getAttribute('name');
                if (!name || name.indexOf(base) !== 0) return;
                var suffix = name.slice(base.length);
                var match = suffix.match(/^\[(\d+|__INDEX__)\]/);
                if (match) {
                    el.setAttribute('name', base + '[' + newIndex + ']' + suffix.substring(match[0].length));
                }
            });
        });
        updateTitlePreviews(list);
    }

    function updateTitlePreviews(list) {
        if (!list) return;
        list.querySelectorAll('.crosstalk-cards-card-block').forEach(function (block) {
            var input = block.querySelector('.crosstalk-card-field-title');
            var preview = block.querySelector('.crosstalk-cards-card-title-preview');
            if (preview && input) {
                preview.textContent = input.value.trim() !== '' ? input.value.trim() : '(No title)';
            }
        });
    }

    function initAddCard() {
        document.addEventListener('click', function (e) {
            if (e.target.closest && (e.target.closest('.widget-control-save') || e.target.closest('.widget-control-actions a[href="#"]'))) return;
            var btn = e.target.closest && e.target.closest('.crosstalk-cards-add');
            if (!btn) return;
            e.preventDefault();
            var ctx = getListAndTemplate(btn);
            if (!ctx.list || !ctx.template) return;
            var nextIndex = ctx.list.querySelectorAll('.crosstalk-cards-card-block').length;
            var html = ctx.template.textContent || ctx.template.innerHTML;
            html = html.replace(/__INDEX__/g, nextIndex);
            var wrap = document.createElement('div');
            wrap.innerHTML = html.trim();
            var newBlock = wrap.firstChild;
            var addWrap = ctx.list.querySelector('.crosstalk-cards-add-wrap');
            if (newBlock) {
                newBlock.setAttribute('data-index', nextIndex);
                if (addWrap) {
                    ctx.list.insertBefore(newBlock, addWrap);
                } else {
                    ctx.list.appendChild(newBlock);
                }
                var emptyMsg = ctx.list.querySelector('.crosstalk-cards-empty-msg');
                if (emptyMsg) emptyMsg.remove();
            }
            reindexCards(ctx.list);
        });
    }

    function initRemoveCard() {
        document.addEventListener('click', function (e) {
            if (e.target.closest && (e.target.closest('.widget-control-save') || e.target.closest('.widget-control-actions a[href="#"]'))) return;
            var btn = e.target.closest && e.target.closest('.crosstalk-cards-remove');
            if (!btn) return;
            e.preventDefault();
            var block = btn.closest('.crosstalk-cards-card-block');
            var list = block && block.parentElement;
            if (!block || !list || !list.classList.contains('crosstalk-cards-list')) return;
            block.remove();
            reindexCards(list);
            showEmptyPlaceholder(list);
        });
    }

    function showEmptyPlaceholder(list) {
        if (!list) return;
        var existing = list.querySelector('.crosstalk-cards-empty-msg');
        var addWrap = list.querySelector('.crosstalk-cards-add-wrap');
        if (list.querySelectorAll('.crosstalk-cards-card-block').length === 0) {
            if (!existing) {
                var msg = document.createElement('p');
                msg.className = 'crosstalk-cards-empty-msg description';
                msg.style.cssText = 'margin:0 0 8px 0; padding:8px 10px; background:#f0f0f1; border-radius:4px;';
                msg.textContent = 'No cards yet. Click "+ Add another card" below to add one.';
                if (addWrap) {
                    list.insertBefore(msg, addWrap);
                } else {
                    list.appendChild(msg);
                }
            }
        } else if (existing) {
            existing.remove();
        }
    }

    function initTitlePreview() {
        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('crosstalk-card-field-title')) {
                var block = e.target.closest('.crosstalk-cards-card-block');
                var preview = block && block.querySelector('.crosstalk-cards-card-title-preview');
                if (preview) {
                    preview.textContent = e.target.value.trim() !== '' ? e.target.value.trim() : '(No title)';
                }
            }
        }, true);
    }

    function initToggleDetails() {
        document.addEventListener('click', function (e) {
            if (e.target.closest && (e.target.closest('.widget-control-save') || e.target.closest('.widget-control-actions a[href="#"]'))) return;
            var btn = e.target.closest && e.target.closest('.crosstalk-cards-toggle');
            if (!btn) return;
            e.preventDefault();
            var block = btn.closest('.crosstalk-cards-card-block');
            if (!block) return;
            var body = block.querySelector('.crosstalk-cards-card-body');
            var icon = block.querySelector('.crosstalk-cards-toggle-icon');
            if (!body) return;
            block.classList.toggle('crosstalk-cards-card-block--collapsed');
            var isCollapsed = block.classList.contains('crosstalk-cards-card-block--collapsed');
            if (isCollapsed) {
                body.style.display = 'none';
                btn.setAttribute('aria-expanded', 'false');
                btn.setAttribute('aria-label', 'Expand details');
                btn.title = 'Expand details';
                if (icon) icon.textContent = '▶';
            } else {
                body.style.display = '';
                btn.setAttribute('aria-expanded', 'true');
                btn.setAttribute('aria-label', 'Collapse details');
                btn.title = 'Collapse details';
                if (icon) icon.textContent = '▼';
            }
        });
    }

    function initSortable() {
        if (typeof jQuery === 'undefined' || !jQuery.fn.sortable) return;
        jQuery(document).on('widget-added widget-updated', function () {
            jQuery('.crosstalk-cards-list').each(function () {
                var list = this;
                if (list._sortableInited) return;
                list._sortableInited = true;
                jQuery(list).sortable({
                    items: '> .crosstalk-cards-card-block',
                    handle: '.crosstalk-cards-drag',
                    placeholder: 'crosstalk-cards-placeholder',
                    tolerance: 'pointer',
                    update: function () {
                        reindexCards(list);
                    }
                });
            });
        });
        jQuery(document).ready(function () {
            jQuery('.crosstalk-cards-list').each(function () {
                var list = this;
                if (list._sortableInited) return;
                list._sortableInited = true;
                jQuery(list).sortable({
                    items: '> .crosstalk-cards-card-block',
                    handle: '.crosstalk-cards-drag',
                    placeholder: 'crosstalk-cards-placeholder',
                    tolerance: 'pointer',
                    update: function () {
                        reindexCards(list);
                    }
                });
            });
        });
    }

    function init() {
        initAddCard();
        initRemoveCard();
        initTitlePreview();
        initToggleDetails();
        initSortable();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
