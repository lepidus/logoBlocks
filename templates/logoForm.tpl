<style>
{literal}
#sponsorsBlockLogoDetails > div + div {
	margin-top: 12px;
}
{/literal}
</style>
<script>
{literal}
$(function() {
	// OJS's default AjaxFormHandler serializes text fields only.
	// Keep its modal lifecycle while submitting the upload as multipart data.
	if (!$.pkp.controllers.form.LogoBlocksFormHandler) {
		$.pkp.controllers.form.LogoBlocksFormHandler = function($form, options) {
			this.parent($form, options);
		};
		$.pkp.classes.Helper.inherits($.pkp.controllers.form.LogoBlocksFormHandler,
			$.pkp.controllers.form.AjaxFormHandler);
		$.pkp.controllers.form.LogoBlocksFormHandler.prototype.submitForm = function() {
			var $form = this.getHtmlElement();
			var data = new FormData($form[0]);
			var handler = this;
			this.disableFormControls();
			$.ajax({
				url: $form.attr('action'),
				type: 'POST',
				data: data,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: this.callbackWrapper(this.handleResponse),
				error: function() {
					handler.enableFormControls();
					handler.hideSpinner();
					$form.find('.sponsorsBlockNetworkError').show();
				}
			});
		};
	}
	$('#sponsorsBlockLogoForm').pkpHandler('$.pkp.controllers.form.LogoBlocksFormHandler');
});
{/literal}
</script>
<form class="pkp_form" id="sponsorsBlockLogoForm" method="post" enctype="multipart/form-data" action="{$logoFormUrl|escape}" data-preview-url="{$logoPreviewUrl|escape}">
	{csrf}
	{include file="common/formErrors.tpl"}
	<p class="sponsorsBlockNetworkError pkp_form_error" role="alert" style="display: none;">{translate key="plugins.block.logoBlocks.networkError"}</p>
	{fbvFormArea id="sponsorsBlockLogoDetails"}
		{fbvElement type="text" id="name" value=$name label=$logoNameLabel required=true maxlength="200"}
		{fbvElement type="text" id="acronym" value=$acronym label="plugins.block.logoBlocks.acronym" maxlength="30"}
		{fbvElement type="text" id="description" value=$description label="plugins.block.logoBlocks.logoDescription" maxlength="100"}
		{fbvElement type="text" id="url" value=$url label="plugins.block.logoBlocks.urlWithHelp" placeholder="plugins.block.logoBlocks.urlPlaceholder"}
	{/fbvFormArea}
	{include file=$logoPreviewTemplate previewFormId="sponsorsBlockLogoForm"}
	{fbvFormButtons submitText="common.save"}
</form>
