{if $sponsorsBlockGrayscale}
	<style>
		.block_sponsorsBlock_grayscale img {ldelim}
			filter: grayscale(100%);
		{rdelim}
		.block_sponsorsBlock_grayscale img:hover,
		.block_sponsorsBlock_grayscale a:hover img,
		.block_sponsorsBlock_grayscale a:focus img {ldelim}
			filter: grayscale(0%);
		{rdelim}
	</style>
{/if}
<section id="sponsorsBlockAboveFooter-{$sponsorsBlockId|escape}" class="pkp_block block_sponsorsBlock block_sponsorsBlock_above_footer{if $sponsorsBlockGrayscale} block_sponsorsBlock_grayscale{/if}" aria-labelledby="sponsorsBlockAboveFooterTitle-{$sponsorsBlockId|escape}" style="clear: both; box-sizing: border-box; width: 100%; margin: 0; padding: 0;">
	<div class="pkp_structure_content" style="box-sizing: border-box; padding: 20px 30px 30px;">
		<h2 id="sponsorsBlockAboveFooterTitle-{$sponsorsBlockId|escape}" class="title">{$sponsorsBlockTitle|escape}</h2>
		<div class="content">
			<ul style="display: flex; flex-wrap: wrap; align-items: center; gap: 30px; list-style: none; margin: 0; padding: 0;">
				{foreach from=$sponsorsBlockLogos item=logo}
					<li style="margin: 0; padding: 0; max-width: 100%;">
						{if $logo.url}<a href="{$logo.url|escape}" target="_blank" rel="noopener noreferrer">{/if}
						<img src="{$sponsorsBlockPublicUrl|escape}/{$logo.file|escape}" alt="{$logo.name|escape}" title="{$logo.name|escape}" width="150" style="display: block; width: 150px; max-width: 100%; height: auto;" loading="lazy">
						{if $logo.url}</a>{/if}
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
</section>
