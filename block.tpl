{**
 * block.tpl
 *
 *}
<div class="block" id="sidebarSponsors" style="text-align:center">
        <span class="blockTitle">{translate key="plugins.block.sponsors.title"}</span>
        <div>
        {foreach from=$sponsors item=sponsor}
                {if $sponsor.link}<a href={$sponsor.link}>{/if}<img src="{$sponsorsDir}/{$sponsor.logo|escape}" width="100%" border="0">{if $sponsor.link}</a>{/if}
        {/foreach}
	</div>
</div>
