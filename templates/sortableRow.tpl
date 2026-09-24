{assign var=rowId value="component-"|concat:$row->getGridId():"-row-":$row->getId()}
<tr id="{$rowId|escape}" class="gridRow sponsors-logo-row" data-logo-id="{$row->getId()|escape}">
    {foreach from=$columns item=column name=logoColumns}
        <td>
            {if $smarty.foreach.logoColumns.first}
                <div class="sponsors-logo-heading">
                    <button type="button" class="sponsors-drag-handle" aria-label="{translate|escape key="plugins.block.sponsorsBlock.moveLogo"}" title="{translate|escape key="plugins.block.sponsorsBlock.orderHelp"}">
                        <span aria-hidden="true" class="sponsors-drag-dots"></span>
                    </button>
                    <button type="button" class="sponsors-toggle-actions" aria-label="{translate|escape key="grid.settings"}" aria-expanded="false" aria-controls="{$rowId|escape}-actions">
                        <span aria-hidden="true" class="sponsors-actions-arrow"></span>
                    </button>
                    {$cells[$smarty.foreach.logoColumns.index]}
                </div>
                <div id="{$rowId|escape}-actions" class="sponsors-logo-actions" hidden>
                    {foreach from=$row->getActions($smarty.const.GRID_ACTION_POSITION_DEFAULT) item=action}
                        {include file="linkAction/linkAction.tpl" action=$action contextId=$rowId}
                    {/foreach}
                </div>
            {else}
                {$cells[$smarty.foreach.logoColumns.index]}
            {/if}
        </td>
    {/foreach}
</tr>
