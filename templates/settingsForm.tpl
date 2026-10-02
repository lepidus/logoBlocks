<style>
{literal}
#sponsorsBlockSettingsForm .sponsors-block-default-note {
	margin: 0 0 16px;
	color: #666;
	font-size: 12px;
}
#sponsorsBlockSettingsForm #sponsorsBlockCustomizationArea {
	margin: 24px 0 0;
	padding: 16px;
	border: 1px solid #d5d5d5;
	border-radius: 3px;
	background: #fff;
}
#sponsorsBlockCustomizationArea > h3 {
	margin: 0 0 14px;
	padding: 0 0 8px;
	border-bottom: 1px solid #e2e2e2;
	font-size: 14px;
}
#sponsorsBlockCustomizationArea > .section {
	margin-bottom: 12px;
}
#sponsorsBlockSettingsForm #sponsorsBlockCustomizationArea > .form_buttons {
	display: flex;
	justify-content: flex-end;
	align-items: center;
	gap: 8px;
	margin: 16px 0 0;
	padding: 10px 0 0;
	border-top: 1px solid #e2e2e2;
	background: transparent;
}
{/literal}
</style>
<script>
	$(function() {ldelim}
		$('#sponsorsBlockSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="sponsorsBlockSettingsForm" method="post" action="{$sponsorsSettingsUrl|escape}">
	{csrf}
	{include file="common/formErrors.tpl"}
	{fbvFormArea id="sponsorsBlockTitleArea"}
		{fbvElement type="text" id="title" value=$title label="plugins.block.logoBlocks.title" multilingual=true maxlength="200"}
	{/fbvFormArea}
	{if $sponsorsNewBlock}
		<p>{translate key="plugins.block.logoBlocks.createHelp"}</p>
	{else}
		{load_url_in_div id="sponsorsBlockGridContainer" url=$sponsorsBlockGridUrl}
	{/if}
	<section id="sponsorsBlockCustomizationArea" aria-labelledby="sponsorsBlockCustomizationTitle">
		<h3 id="sponsorsBlockCustomizationTitle">{translate key="plugins.block.logoBlocks.customization"}</h3>
		{fbvFormSection list=true}
			{fbvElement type="select" id="placement" from=$sponsorsBlockPlacementOptions selected=$placement label="plugins.block.logoBlocks.placementRequired" required=true}
		{/fbvFormSection}
		{fbvFormSection list=true}
			{fbvElement type="select" id="descriptionPlacement" from=$sponsorsDescriptionPlacementOptions selected=$descriptionPlacement label="plugins.block.logoBlocks.descriptionPlacement" required=true}
		{/fbvFormSection}
		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="grayscaleLogos" value="1" checked=$grayscaleLogos label="plugins.block.logoBlocks.grayscaleLogos"}
		{/fbvFormSection}
		{fbvFormButtons submitText="common.save"}
	</section>
</form>
