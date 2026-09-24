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
	<p>{translate key="plugins.block.sponsorsBlock.gridHelp"}</p>
	<p>{translate key="plugins.block.sponsorsBlock.orderHelp"}</p>
	{load_url_in_div id="sponsorsBlockGridContainer" url=$sponsorsBlockGridUrl}
	{fbvFormArea id="sponsorsBlockPlacementArea"}
		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="showAboveFooter" value="1" checked=$showAboveFooter label="plugins.block.sponsorsBlock.showAboveFooter"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormArea id="sponsorsBlockAppearanceArea"}
		{fbvFormSection list=true}
			{fbvElement type="checkbox" id="grayscaleLogos" value="1" checked=$grayscaleLogos label="plugins.block.sponsorsBlock.grayscaleLogos"}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons}
</form>
