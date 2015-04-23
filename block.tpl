{**
 * block.tpl
 *
 *}
<div class="block" id="sidebarPatrs" style="text-align:center">
    <span class="blockTitle">{translate key="plugins.block.sponsors.title"}</span>
    <div>
        {foreach from=$sponsors item=sponsor}
        <div>
        	{if $sponsor.logo}
            {if $sponsor.link}<a href={$sponsor.link}>{/if}
              	<img src="{$sponsorsDir}/{$sponsor.logo|escape}"  border="0">
            {if $sponsor.link}</a>{/if}
            {else}
            	<div class="group">{$sponsor.group[$currentLocale]}</div>
            {/if}
        </div>
        {/foreach}
	</div>
</div>
