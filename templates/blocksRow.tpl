{assign var=rowId value="component-"|concat:$row->getGridId():"-row-":$row->getId()}
{assign var=blockData value=$row->getData()}
<tr id="{$rowId|escape}" class="gridRow sponsors-block-row" data-block-id="{$row->getId()|escape}">
	<td>
		<div style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <span style="display: inline-flex; align-items: center; gap: 12px;">
                <button type="button" class="sponsors-block-drag-handle" aria-label="{translate|escape key="plugins.block.logoBlocks.moveBlock"} — {$blockData.title|escape}" title="{translate|escape key="plugins.block.logoBlocks.blockOrderHelp"}">
                    <span aria-hidden="true" class="sponsors-block-drag-dots"></span>
                </button>
                {$cells[0]}
            </span>
			<span class="sponsors-block-edit" style="display: inline-flex; align-items: center; gap: 12px;">
				{foreach from=$row->getActions($smarty.const.GRID_ACTION_POSITION_DEFAULT) item=action}
					{include file="linkAction/linkAction.tpl" action=$action contextId=$rowId anyhtml=true}
				{/foreach}
                <button type="button" class="sponsors-block-switch" role="switch"
                    aria-checked="{if $blockData.visible}true{else}false{/if}"
                    aria-label="{translate|escape key="plugins.block.logoBlocks.visibility"} — {$blockData.title|escape}"
                    title="{translate|escape key="plugins.block.logoBlocks.visibility"}"
                    data-url="{$blockData.visibilityUrl|escape}" data-csrf="{$blockData.csrfToken|escape}"
                    data-error="{translate|escape key="plugins.block.logoBlocks.visibilityError"}">
                    <span aria-hidden="true"></span>
                </button>
			</span>
		</div>
		<p class="sponsors-block-visibility-error" role="alert" hidden></p>
	</td>
</tr>

<script>
$(function() {ldelim}
    var $row = $('#{$rowId|escape:jqselector}');
    {literal}
    $row.find('.sponsors-block-switch').on('click', function() {
        var $button = $(this);
        var $error = $row.find('.sponsors-block-visibility-error');
        var visible = $button.attr('aria-checked') !== 'true';
        $button.prop('disabled', true).attr('aria-busy', 'true');
        $error.prop('hidden', true).text('');
        $.ajax({
            url: $button.attr('data-url'), type: 'POST', dataType: 'json',
            data: {visible: visible ? '1' : '0', csrfToken: $button.attr('data-csrf')}
        }).done(function(response) {
            if (response && response.status) {
                $button.attr('aria-checked', visible ? 'true' : 'false');
            } else {
                $error.text($button.attr('data-error')).prop('hidden', false);
            }
        }).fail(function() {
            $error.text($button.attr('data-error')).prop('hidden', false);
        }).always(function() {
            $button.prop('disabled', false).removeAttr('aria-busy');
        });
    });
    {/literal}
{rdelim});
</script>
