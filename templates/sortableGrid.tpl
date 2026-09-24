<style>
{literal}
.pkp_controllers_grid.sponsors-sortable-grid > table { border-collapse: separate; border-spacing: 0 12px; background: #edf0f2; padding: 0 10px 4px; border: 0; }
.sponsors-sortable-grid > table > thead > tr > th:first-child { white-space: nowrap; }
.sponsors-sortable-grid .sponsors-logo-row > td { background: #fff; border-top: 1px solid #dfe4e8; border-bottom: 1px solid #dfe4e8; padding: 14px 16px; vertical-align: middle; overflow-wrap: anywhere; }
.sponsors-sortable-grid .sponsors-logo-row > td:first-child { border-left: 1px solid #dfe4e8; border-radius: 10px 0 0 10px; }
.sponsors-sortable-grid .sponsors-logo-row > td:last-child { border-right: 1px solid #dfe4e8; border-radius: 0 10px 10px 0; }
.sponsors-logo-heading { display: flex; align-items: center; gap: 12px; }
.sponsors-drag-handle { border: 0; background: transparent; padding: 8px; cursor: grab; flex: 0 0 auto; }
.sponsors-drag-handle:active { cursor: grabbing; }
.sponsors-drag-handle:focus-visible, .sponsors-toggle-actions:focus-visible { outline: 2px solid #006798; border-radius: 4px; }
.sponsors-drag-handle:disabled { cursor: wait; opacity: .5; }
.sponsors-drag-dots { display: block; width: 8px; height: 14px; background: radial-gradient(circle, #94a3b8 1.2px, transparent 1.5px) 0 0 / 4px 4px; }
.sponsors-logo-actions { display: flex; gap: 12px; margin: 12px 0 0 36px; }
.sponsors-logo-actions[hidden] { display: none; }
.sponsors-toggle-actions { border: 0; background: transparent; padding: 8px; cursor: pointer; flex: 0 0 auto; }
.sponsors-actions-arrow { display: block; width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 7px solid #006798; }
.sponsors-toggle-actions[aria-expanded="true"] .sponsors-actions-arrow { transform: rotate(90deg); }
.sponsors-order-status { min-height: 1.5em; margin: 8px 0; color: #526477; }
.sponsors-order-status.is-error { color: #b42318; }
.sponsors-sortable-grid .ui-sortable-helper { box-shadow: 0 4px 12px #0002; }
.sponsors-sortable-grid.is-saving .pkp_controllers_linkAction { pointer-events: none; opacity: .5; }
{/literal}
</style>
<script>
{literal}
(function ($) {
    if ($.pkp.controllers.grid.SponsorsBlockGridHandler) return;
    $.pkp.controllers.grid.SponsorsBlockGridHandler = function ($grid, options) {
        this.parent($grid, options);
        $grid.addClass('sponsors-sortable-grid');
        var $focusedHandle = null;
        var $body = $grid.find('tbody').not('.empty').first();
        var $messages = $grid.prevAll('.sponsors-order-messages').first();
        var $status = $('<p class="sponsors-order-status" role="status" aria-live="polite"></p>').appendTo($grid);
        var saving = false;
        var previousRows = [];
        var rows = function () { return $body.children('.sponsors-logo-row').not('.ui-sortable-placeholder'); };
        var restore = function () {
            previousRows.forEach(function (row) { $body.append(row); });
        };
        var lock = function (value) {
            saving = value;
            $grid.toggleClass('is-saving', value).attr('aria-busy', value ? 'true' : 'false');
            $grid.find('.sponsors-drag-handle').prop('disabled', value);
            if ($body.hasClass('ui-sortable')) $body.sortable(value ? 'disable' : 'enable');
        };
        var persist = function () {
            var ids = rows().map(function () { return $(this).attr('data-logo-id'); }).get();
            lock(true);
            $status.removeClass('is-error').text($messages.attr('data-saving'));
            $.ajax({
                url: options.saveSequenceUrl, type: 'POST', dataType: 'json',
                data: {data: JSON.stringify(ids), csrfToken: options.csrfToken}
            }).done(function (response) {
                if (response && response.status === true) {
                    $status.text($messages.attr('data-saved'));
                } else {
                    restore();
                    $status.addClass('is-error').text($messages.attr('data-error'));
                }
            }).fail(function () {
                restore();
                $status.addClass('is-error').text($messages.attr('data-error'));
            }).always(function () {
                lock(false);
                if ($focusedHandle) { $focusedHandle.trigger('focus'); $focusedHandle = null; }
            });
        };
        // Block row actions while a sequence is being saved, including keyboard clicks.
        $grid[0].addEventListener('click', function (event) {
            if (saving && $(event.target).closest('.pkp_controllers_linkAction').length) {
                event.preventDefault(); event.stopImmediatePropagation();
            }
        }, true);
        $grid.on('click.sponsorsActions', '.sponsors-toggle-actions', function () {
            var $toggle = $(this);
            var expanded = $toggle.attr('aria-expanded') !== 'true';
            $toggle.attr('aria-expanded', expanded ? 'true' : 'false');
            $toggle.closest('.sponsors-logo-row').find('.sponsors-logo-actions').prop('hidden', !expanded);
        });
        if ($.fn.sortable) {
            $body.sortable({
                placeholder: 'sponsors-sort-placeholder', items: '> .sponsors-logo-row', handle: '.sponsors-drag-handle',
                cancel: 'input,textarea,select,option', axis: 'y', tolerance: 'pointer',
                start: function () { previousRows = rows().get(); },
                helper: function (event, $row) {
                    $row.children().each(function () { $(this).width($(this).width()); });
                    return $row;
                },
                update: persist
            });
        }
        $grid.on('keydown.sponsorsOrder', '.sponsors-drag-handle', function (event) {
            if (saving || (event.key !== 'ArrowUp' && event.key !== 'ArrowDown')) return;
            event.preventDefault();
            var $row = $(this).closest('.sponsors-logo-row');
            var $neighbor = event.key === 'ArrowUp' ? $row.prev('.sponsors-logo-row') : $row.next('.sponsors-logo-row');
            if (!$neighbor.length) return;
            previousRows = rows().get();
            if (event.key === 'ArrowUp') $row.insertBefore($neighbor); else $row.insertAfter($neighbor);
            $focusedHandle = $(this);
            persist();
        });
    };
    $.pkp.classes.Helper.inherits($.pkp.controllers.grid.SponsorsBlockGridHandler, $.pkp.controllers.grid.GridHandler);
})(jQuery);
{/literal}
</script>
<div class="sponsors-order-messages" hidden data-saving="{translate|escape key="plugins.block.sponsorsBlock.orderSaving"}" data-saved="{translate|escape key="plugins.block.sponsorsBlock.orderSaved"}" data-error="{translate|escape key="plugins.block.sponsorsBlock.invalidOrder"}"></div>
{include file="controllers/grid/grid.tpl"}
