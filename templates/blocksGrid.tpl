<style>
{literal}
.sponsors-block-switch {
    box-sizing: border-box;
    display: inline-flex;
    align-items: center;
    flex-shrink: 0;
    width: 34px;
    height: 20px;
    padding: 2px;
    border: 0;
    border-radius: 12px;
    background: #777;
    cursor: pointer;
}
.sponsors-block-switch[aria-checked="true"] { background: #10a64a; }
.sponsors-block-switch > span {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #fff;
    transform: translateX(0);
}
.sponsors-block-switch[aria-checked="true"] > span { transform: translateX(14px); }
.sponsors-block-switch:focus-visible { outline: 2px solid #006798; outline-offset: 3px; }
.sponsors-block-switch:disabled { opacity: .6; cursor: wait; }
.sponsors-block-visibility-error { color: #b21e32; margin: 8px 0 0; }

.sponsors-block-drag-handle { border: 0; background: transparent; padding: 8px; cursor: grab; flex-shrink: 0; }
.sponsors-block-drag-handle:active { cursor: grabbing; }
.sponsors-block-drag-handle:focus-visible { outline: 2px solid #006798; border-radius: 4px; }
.sponsors-block-drag-handle:disabled { cursor: wait; opacity: .5; }
.sponsors-block-drag-dots { display: block; width: 8px; height: 14px; background: radial-gradient(circle, #94a3b8 1.2px, transparent 1.5px) 0 0 / 4px 4px; }
.sponsors-block-order-status { min-height: 1.5em; margin: 8px 0; color: #526477; }
.sponsors-block-order-status.is-error { color: #b42318; }
.sponsors-sortable-blocks.is-saving .pkp_controllers_linkAction { pointer-events: none; opacity: .5; }
.sponsors-blocks-default-note {
    margin: 0 10px 20px;
    padding: 12px 14px;
    border-left: 4px solid #b7d8ea;
    background: #f5fbff;
    color: #526477;
}
.sponsors-blocks-default-note p { margin: 0; }
{/literal}
</style>
<script>
{literal}
(function ($) {
	if ($.pkp.controllers.grid.SponsorsBlocksGridHandler) return;
	$.pkp.controllers.grid.SponsorsBlocksGridHandler = function ($grid, options) {
		this.parent($grid, options);
		$grid.addClass('sponsors-sortable-blocks');
		// Keep the notice inside the element replaced when the grid refreshes.
		$grid.prevAll('.sponsors-blocks-default-note').first().prependTo($grid);
		var $focusedHandle = null;
		var $body = $grid.find('tbody').not('.empty').first();
		var $messages = $grid.prevAll('.sponsors-block-order-messages').first();
		var $status = $('<p class="sponsors-block-order-status" role="status" aria-live="polite"></p>').appendTo($grid);
		var saving = false;
		var previousRows = [];
		var rows = function () { return $body.children('.sponsors-block-row').not('.ui-sortable-placeholder'); };
		var restore = function () {
			previousRows.forEach(function (row) { $body.append(row); });
		};
		var lock = function (value) {
			saving = value;
			$grid.toggleClass('is-saving', value).attr('aria-busy', value ? 'true' : 'false');
			$grid.find('.sponsors-block-drag-handle').prop('disabled', value);
			if ($body.hasClass('ui-sortable')) $body.sortable(value ? 'disable' : 'enable');
		};
		var persist = function () {
			var ids = rows().map(function () { return $(this).attr('data-block-id'); }).get();
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
		if ($.fn.sortable) {
			$body.sortable({
				placeholder: 'sponsors-sort-placeholder', items: '> .sponsors-block-row', handle: '.sponsors-block-drag-handle',
				cancel: 'input,textarea,select,option', axis: 'y', tolerance: 'pointer',
				start: function () { previousRows = rows().get(); },
				helper: function (event, $row) {
					$row.children().each(function () { $(this).width($(this).width()); });
					return $row;
				},
				update: persist
			});
		}
		$grid.on('keydown.sponsorsOrder', '.sponsors-block-drag-handle', function (event) {
			if (saving || (event.key !== 'ArrowUp' && event.key !== 'ArrowDown')) return;
			event.preventDefault();
			var $row = $(this).closest('.sponsors-block-row');
			var $neighbor = event.key === 'ArrowUp' ? $row.prev('.sponsors-block-row') : $row.next('.sponsors-block-row');
			if (!$neighbor.length) return;
			previousRows = rows().get();
			if (event.key === 'ArrowUp') $row.insertBefore($neighbor); else $row.insertAfter($neighbor);
			$focusedHandle = $(this);
			persist();
		});
	};
	$.pkp.classes.Helper.inherits($.pkp.controllers.grid.SponsorsBlocksGridHandler, $.pkp.controllers.grid.GridHandler);
})(jQuery);
{/literal}
</script>
<div class="sponsors-blocks-default-note" role="note">
	<p>{translate key="plugins.block.sponsorsBlock.defaultBlockNotice"}</p>
</div>
<div class="sponsors-block-order-messages" hidden data-saving="{translate|escape key="plugins.block.sponsorsBlock.orderSaving"}" data-saved="{translate|escape key="plugins.block.sponsorsBlock.orderSaved"}" data-error="{translate|escape key="plugins.block.sponsorsBlock.invalidOrder"}"></div>
{include file="controllers/grid/grid.tpl"}
