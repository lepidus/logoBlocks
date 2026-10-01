<script>
{literal}
$(function() {
	// OJS's default AjaxFormHandler serializes text fields only.
	// Keep its modal lifecycle while submitting the upload as multipart data.
	if (!$.pkp.controllers.form.SponsorsBlockFormHandler) {
		$.pkp.controllers.form.SponsorsBlockFormHandler = function($form, options) {
			this.parent($form, options);
		};
		$.pkp.classes.Helper.inherits($.pkp.controllers.form.SponsorsBlockFormHandler,
			$.pkp.controllers.form.AjaxFormHandler);
		$.pkp.controllers.form.SponsorsBlockFormHandler.prototype.submitForm = function() {
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
	$('#sponsorsBlockLogoForm').pkpHandler('$.pkp.controllers.form.SponsorsBlockFormHandler');
});
{/literal}
</script>
<form class="pkp_form" id="sponsorsBlockLogoForm" method="post" enctype="multipart/form-data" action="{$logoFormUrl|escape}" data-preview-url="{$logoPreviewUrl|escape}">
	{csrf}
	{include file="common/formErrors.tpl"}
	<p class="sponsorsBlockNetworkError pkp_form_error" role="alert" style="display: none;">{translate key="plugins.block.sponsorsBlock.networkError"}</p>
	{fbvFormArea id="sponsorsBlockLogoDetails"}
		{fbvElement type="text" id="name" value=$name label="plugins.block.sponsorsBlock.name" required=true maxlength="200"}
		{fbvElement type="text" id="acronym" value=$acronym label="plugins.block.sponsorsBlock.acronym" maxlength="30"}
		{fbvElement type="text" id="url" value=$url label="plugins.block.sponsorsBlock.urlWithHelp" placeholder="plugins.block.sponsorsBlock.urlPlaceholder"}
	{/fbvFormArea}
	{include file=$logoPreviewTemplate previewFormId="sponsorsBlockLogoForm"}
	{fbvFormButtons submitText="common.save"}
</form>
