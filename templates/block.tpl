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
<div class="pkp_block block_sponsorsBlock{if $sponsorsBlockGrayscale} block_sponsorsBlock_grayscale{/if}">
	<h2 class="title">{$sponsorsBlockTitle|escape}</h2>
	<div class="content">
		<ul style="list-style: none; margin: 0; padding: 0;">
			{foreach from=$sponsorsBlockLogos item=logo}
				<li style="margin: 0 0 20px; padding: 0;">
					<div style="display: flex; align-items: center; min-height: 50px;">
						{if $logo.url}<a href="{$logo.url|escape}" target="_blank" rel="noopener noreferrer" style="display: block; width: 100%;">{/if}
						<img src="{$sponsorsBlockPublicUrl|escape}/{$logo.file|escape}" alt="{$logo.name|escape}" title="{$logo.name|escape}" style="display: block; width: auto; max-width: 100%; height: auto; max-height: 100px;" loading="lazy">
						{if $logo.url}</a>{/if}
					</div>
					{if ($sponsorsDescriptionPlacement == "sidebar" || $sponsorsDescriptionPlacement == "both") && !empty($logo.description)}
						<p class="sponsorsBlockDescription" style="margin: 8px 0 0; white-space: pre-line; overflow-wrap: anywhere;">{$logo.description|escape}</p>
					{/if}
				</li>
			{/foreach}
		</ul>
	</div>
</div>
