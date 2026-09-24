<style>
{literal}
.sponsors-logo-gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 16px; margin: 16px 0; }
.sponsors-gallery-choice { position: relative; cursor: pointer; }
.sponsors-gallery-choice input { position: absolute; top: 12px; left: 12px; }
.sponsors-gallery-card { display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 36px 12px 16px; border: 2px solid #dfe4e8; border-radius: 8px; background: #fff; }
.sponsors-gallery-card img { width: 150px; height: 100px; object-fit: contain; }
.sponsors-gallery-choice input:checked + .sponsors-gallery-card { border-color: #006798; background: #f0f8fc; }
.sponsors-gallery-choice input:focus + .sponsors-gallery-card { outline: 2px solid #006798; outline-offset: 3px; }
{/literal}
</style>
<script>
$(function() {ldelim}
    $('#sponsorsBlockGalleryForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
{rdelim});
</script>
<form class="pkp_form" id="sponsorsBlockGalleryForm" method="post" action="{$logoFormUrl|escape}">
    {csrf}
    {include file="common/formErrors.tpl"}
    <p id="sponsorsGalleryHelp">{translate key="plugins.block.sponsorsBlock.galleryHelp"}</p>
    <div class="sponsors-logo-gallery" role="radiogroup" aria-labelledby="sponsorsGalleryHelp">
        {foreach from=$logoCatalog key=id item=entry}
            <label class="sponsors-gallery-choice">
                <input type="radio" name="galleryId" value="{$id|escape}" {if $galleryId == $id}checked{/if} required>
                <span class="sponsors-gallery-card">
                    <img src="{$logoCatalogUrl|escape}/{$entry.file|escape}" alt="">
                    <strong>{$entry.name|escape}</strong>
                </span>
            </label>
        {foreachelse}
            <p>{translate key="plugins.block.sponsorsBlock.galleryEmpty"}</p>
        {/foreach}
    </div>
    {fbvFormButtons submitText="plugins.block.sponsorsBlock.useLogo"}
</form>
