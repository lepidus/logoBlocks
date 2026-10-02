{assign var=logo value=$row->getData()}
{assign var=rowId value="component-"|concat:$row->getGridId():"-row-":$row->getId()}
<tr id="{$rowId|escape}" class="gridRow sponsors-logo-row" data-logo-id="{$row->getId()|escape}">
	<td colspan="{$columns|@count}">
		<div class="sponsors-logo-heading">
			<button type="button" class="sponsors-drag-handle" aria-label="{translate|escape key="plugins.block.logoBlocks.moveLogo"}" title="{translate|escape key="plugins.block.logoBlocks.orderHelp"}">
				<span aria-hidden="true" class="sponsors-drag-dots"></span>
			</button>
			<button type="button" class="sponsors-toggle-actions" aria-label="{translate|escape key="grid.settings"}" aria-expanded="false" aria-controls="{$rowId|escape}-actions">
				<span aria-hidden="true" class="sponsors-actions-arrow"></span>
			</button>
			<div class="sponsors-logo-image">{$cells[1]}</div>
			<div class="sponsors-logo-details">
				<div class="sponsors-logo-name">{$cells[0]}</div>
				{if $logo.url}
				<div class="sponsors-logo-link">
					<svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-2 2M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l2-2M8 16l8-8" />
					</svg>
					<div class="sponsors-logo-url" tabindex="0">{$cells[2]}</div>
				</div>
				{else}
					<div class="sub_label">{$cells[2]}</div>
				{/if}
			</div>
		</div>
		<div id="{$rowId|escape}-actions" class="sponsors-logo-actions" hidden>
			{foreach from=$row->getActions($smarty.const.GRID_ACTION_POSITION_DEFAULT) item=action}
				{include file="linkAction/linkAction.tpl" action=$action contextId=$rowId}
			{/foreach}
		</div>
	</td>
</tr>
