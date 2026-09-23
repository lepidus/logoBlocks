<section id="sponsorsBlockAboveFooter" class="pkp_block block_sponsorsBlock block_sponsorsBlock_above_footer" aria-labelledby="sponsorsBlockAboveFooterTitle" style="clear: both; box-sizing: border-box; width: 100%; margin: 0; padding: 0;">
    <hr style="border: 0; border-top: 1px solid #ddd; margin: 0;">
    <div class="pkp_structure_content" style="box-sizing: border-box; padding: 20px 30px 30px;">
    <h2 id="sponsorsBlockAboveFooterTitle" class="title">{$sponsorsBlockTitle|escape}</h2>
    <div class="content">
        <ul style="display: flex; flex-wrap: wrap; align-items: center; gap: 30px; list-style: none; margin: 0; padding: 0;">
            {foreach from=$sponsorsBlockLogos item=logo}
                <li style="margin: 0; padding: 0; max-width: 100%;">
                    {if $logo.url}<a href="{$logo.url|escape}">{/if}
                    <img src="{$sponsorsBlockPublicUrl|escape}/{$logo.file|escape}" alt="{$logo.name|escape}" width="150" style="display: block; width: 150px; max-width: 100%; height: auto;" loading="lazy">
                    {if $logo.url}</a>{/if}
                </li>
            {/foreach}
        </ul>
    </div>
    </div>
</section>
