/**
 * Media picker and 16:9 crop for Crosstalk Card widget.
 * If the selected image is not 16:9 aspect ratio, shows a panel to select a 16:9 region.
 */
(function () {
    'use strict';

    var RATIO = 16 / 9;
    var RATIO_TOLERANCE = 0.02;

    function isRatio59(width, height) {
        if (!width || !height) return false;
        var r = width / height;
        return Math.abs(r - RATIO) <= RATIO_TOLERANCE;
    }

    function get59BoxSize(imgWidth, imgHeight) {
        var boxWidth, boxHeight;
        if (imgWidth / imgHeight > RATIO) {
            boxHeight = imgHeight;
            boxWidth = imgHeight * RATIO;
        } else {
            boxWidth = imgWidth;
            boxHeight = imgWidth / RATIO;
        }
        return { width: boxWidth, height: boxHeight };
    }

    function initSelectImage() {
        document.addEventListener('click', function (e) {
            if (e.target.closest && (e.target.closest('.widget-control-save') || e.target.closest('.widget-control-actions a[href="#"]'))) return;
            var btn = e.target.closest && e.target.closest('.crosstalk-card-widget-select-image');
            if (!btn) return;
            if (e.button !== 0) return;
            var wrap = btn.closest('.crosstalk-card-widget-image-wrap');
            if (!wrap) return;
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') return;
            e.preventDefault();

            var form = wrap.closest('.widget-content') || wrap.closest('.widget-form');
            var input = wrap.querySelector('input[name*="image_id"]');
            var preview = wrap.querySelector('.crosstalk-card-widget-preview');
            var removeBtn = wrap.querySelector('.crosstalk-card-widget-remove-image');
            var cardBlock = wrap.closest('.crosstalk-cards-card-block');
            var panel = cardBlock ? cardBlock.querySelector('.crosstalk-card-crop-panel') : null;
            if (!panel && form) panel = form.querySelector('.crosstalk-card-crop-panel');
            if (!panel) panel = wrap.closest('p').nextElementSibling;
            if (panel && !panel.classList.contains('crosstalk-card-crop-panel')) panel = null;
            var cropContainer = cardBlock || form;
            var cropXInput = cropContainer ? cropContainer.querySelector('.crosstalk-crop-x') : (panel ? panel.querySelector('.crosstalk-crop-x') : null);
            var cropYInput = cropContainer ? cropContainer.querySelector('.crosstalk-crop-y') : (panel ? panel.querySelector('.crosstalk-crop-y') : null);

            var frame = wp.media({
                library: { type: 'image' },
                multiple: false
            });
            frame.on('select', function () {
                var att = frame.state().get('selection').first().toJSON();
                var w = att.width;
                var h = att.height;
                if (!w || !h) {
                    var full = att.sizes && att.sizes.full;
                    if (full) {
                        w = full.width;
                        h = full.height;
                    }
                }
                if (input) input.value = att.id;
                if (preview) {
                    preview.innerHTML = '<img src="' + (att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url) + '" alt="" style="max-width:100%;height:auto;display:block;">';
                }
                if (removeBtn) removeBtn.style.display = '';

                if (panel) {
                    if (w && h && isRatio59(w, h)) {
                        panel.style.display = 'none';
                        if (cropXInput) cropXInput.value = '';
                        if (cropYInput) cropYInput.value = '';
                    } else {
                        showCropPanel(panel, att.url, w, h, cropXInput, cropYInput);
                        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }
            });
            setTimeout(function () {
                try { frame.open(); } catch (err) { }
            }, 10);
        });
    }

    function showCropPanel(panel, imageUrl, imageWidth, imageHeight, cropXInput, cropYInput) {
        var wrap = panel.querySelector('.crosstalk-card-crop-wrap');
        var img = panel.querySelector('.crosstalk-card-crop-image');
        var box = panel.querySelector('.crosstalk-card-crop-box');
        var applyBtn = panel.querySelector('.crosstalk-card-apply-crop');
        if (!wrap || !img || !box) return;

        panel.style.display = 'block';
        img.src = imageUrl;
        img.onload = function () {
            var w = imageWidth || img.naturalWidth;
            var h = imageHeight || img.naturalHeight;
            if (!w || !h) return;
            var displayWidth = img.offsetWidth;
            var displayHeight = img.offsetHeight;
            var scale = displayWidth / w;
            var size = get59BoxSize(w, h);
            var boxW = Math.round(size.width * scale);
            var boxH = Math.round(size.height * scale);
            if (boxW <= 0 || boxH <= 0) return;
            var maxLeft = Math.max(0, displayWidth - boxW);
            var maxTop = Math.max(0, displayHeight - boxH);

            box.style.width = boxW + 'px';
            box.style.height = boxH + 'px';
            box.style.left = '0px';
            box.style.top = '0px';

            var drag = {
                active: false,
                startX: 0,
                startY: 0,
                startLeft: 0,
                startTop: 0
            };

            function onMouseMove(e) {
                if (!drag.active) return;
                var dx = e.clientX - drag.startX;
                var dy = e.clientY - drag.startY;
                var left = Math.max(0, Math.min(maxLeft, drag.startLeft + dx));
                var top = Math.max(0, Math.min(maxTop, drag.startTop + dy));
                box.style.left = left + 'px';
                box.style.top = top + 'px';
                box.dataset.left = left;
                box.dataset.top = top;
            }
            function onMouseUp() {
                drag.active = false;
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }

            box.onmousedown = function (e) {
                e.preventDefault();
                drag.active = true;
                drag.startX = e.clientX;
                drag.startY = e.clientY;
                drag.startLeft = parseInt(box.style.left, 10) || 0;
                drag.startTop = parseInt(box.style.top, 10) || 0;
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            };

            if (cropXInput && cropYInput) {
                var savedX = parseFloat(cropXInput.value);
                var savedY = parseFloat(cropYInput.value);
                if (!isNaN(savedX) && !isNaN(savedY) && savedX >= 0 && savedX <= 100 && savedY >= 0 && savedY <= 100) {
                    var centerX = (savedX / 100) * displayWidth;
                    var centerY = (savedY / 100) * displayHeight;
                    var left = Math.max(0, Math.min(maxLeft, centerX - boxW / 2));
                    var top = Math.max(0, Math.min(maxTop, centerY - boxH / 2));
                    box.style.left = left + 'px';
                    box.style.top = top + 'px';
                    box.dataset.left = left;
                    box.dataset.top = top;
                }
            }

            applyBtn.onclick = function () {
                var left = parseInt(box.dataset.left || box.style.left, 10) || 0;
                var top = parseInt(box.dataset.top || box.style.top, 10) || 0;
                var centerX = ((left + boxW / 2) / displayWidth) * 100;
                var centerY = ((top + boxH / 2) / displayHeight) * 100;
                var centerXVal = Math.round(centerX * 10) / 10;
                var centerYVal = Math.round(centerY * 10) / 10;
                if (cropXInput) {
                    cropXInput.value = centerXVal;
                    cropXInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                if (cropYInput) {
                    cropYInput.value = centerYVal;
                    cropYInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                panel.style.display = 'none';
                var cardBlock = panel.closest('.crosstalk-cards-card-block');
                var form = panel.closest('.widget-content') || panel.closest('.widget-form');
                var preview = (cardBlock || form) && (cardBlock || form).querySelector('.crosstalk-card-widget-preview');
                if (preview && img && img.src) {
                    var wrapper = document.createElement('span');
                    wrapper.style.cssText = 'display:block; overflow:hidden; aspect-ratio:16/9; max-width:100%;';
                    var previewImg = document.createElement('img');
                    previewImg.src = img.src;
                    previewImg.alt = '';
                    previewImg.style.cssText = 'width:100%; height:100%; object-fit:cover; object-position:' + centerXVal + '% ' + centerYVal + '%; display:block;';
                    wrapper.appendChild(previewImg);
                    preview.innerHTML = '';
                    preview.appendChild(wrapper);
                }
            };
        };
    }

    function initRemoveImage() {
        document.addEventListener('click', function (e) {
            if (e.target.closest && (e.target.closest('.widget-control-save') || e.target.closest('.widget-control-actions a[href="#"]'))) return;
            var btn = e.target.closest && e.target.closest('.crosstalk-card-widget-remove-image');
            if (!btn) return;
            e.preventDefault();
            var wrap = btn.closest('.crosstalk-card-widget-image-wrap');
                if (!wrap) return;
                var form = wrap.closest('.widget-content') || wrap.closest('.widget-form');
                var cardBlock = wrap.closest('.crosstalk-cards-card-block');
                var input = wrap.querySelector('input[name*="image_id"]');
                var preview = wrap.querySelector('.crosstalk-card-widget-preview');
                var panel = cardBlock ? cardBlock.querySelector('.crosstalk-card-crop-panel') : (form ? form.querySelector('.crosstalk-card-crop-panel') : null);
                var cropContainer = cardBlock || form;
                var cropXInput = cropContainer ? cropContainer.querySelector('.crosstalk-crop-x') : null;
                var cropYInput = cropContainer ? cropContainer.querySelector('.crosstalk-crop-y') : null;
                if (input) input.value = '0';
                if (preview) preview.innerHTML = '';
                btn.style.display = 'none';
                if (panel) panel.style.display = 'none';
                if (cropXInput) cropXInput.value = '';
                if (cropYInput) cropYInput.value = '';
        });
    }

    function init() {
        initSelectImage();
        initRemoveImage();
    }

    var inited = false;
    function runWhenReady() {
        if (inited) return;
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') return;
        inited = true;
        init();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runWhenReady);
    } else {
        runWhenReady();
    }
    window.addEventListener('load', runWhenReady);

    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('widget-added widget-updated', function () {
            inited = false;
            setTimeout(runWhenReady, 150);
        });
    }
})();
