<style>
{literal}
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
<form class="pkp_form" id="sponsorsBlockSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="blocks" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="common/formErrors.tpl"}
	{fbvFormArea id="sponsorsBlockTitleArea"}
		{fbvElement type="text" id="title" value=$title label="plugins.block.sponsorsBlock.title" multilingual=true required=true maxlength="200"}
	{/fbvFormArea}
	{load_url_in_div id="sponsorsBlockGridContainer" url=$sponsorsBlockGridUrl}
	<section id="sponsorsBlockCustomizationArea" aria-labelledby="sponsorsBlockCustomizationTitle">
		<h3 id="sponsorsBlockCustomizationTitle">{translate key="plugins.block.sponsorsBlock.customization"}</h3>
		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="showAboveFooter" value="1" checked=$showAboveFooter label="plugins.block.sponsorsBlock.showAboveFooter"}
		{/fbvFormSection}
		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="grayscaleLogos" value="1" checked=$grayscaleLogos label="plugins.block.sponsorsBlock.grayscaleLogos"}
		{/fbvFormSection}
		{fbvFormButtons submitText="common.save"}
	</section>
</form>
