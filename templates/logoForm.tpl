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
<form class="pkp_form" id="sponsorsBlockLogoForm" method="post" enctype="multipart/form-data" action="{$logoFormUrl|escape}">
    {csrf}
    {include file="common/formErrors.tpl"}
    <p class="sponsorsBlockNetworkError pkp_form_error" role="alert" style="display: none;">{translate key="plugins.block.sponsorsBlock.networkError"}</p>
    {fbvFormArea id="sponsorsBlockLogoDetails"}
        {fbvElement type="text" id="name" value=$name label="plugins.block.sponsorsBlock.name" required=true maxlength="200"}
        {fbvElement type="text" id="url" value=$url label="plugins.block.sponsorsBlock.url"}
        {if $currentLogoUrl}
            <p><img src="{$currentLogoUrl|escape}" alt="{$name|escape}" width="150" style="max-width: 100%; height: auto;"></p>
            <p>{translate key="plugins.block.sponsorsBlock.replaceHelp"}</p>
        {/if}
        <p><label for="logo">{translate key="plugins.block.sponsorsBlock.logo"}</label>
        <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/gif"></p>
        <p>{translate key="plugins.block.sponsorsBlock.imageHelp"}</p>
    {/fbvFormArea}
    {fbvFormButtons}
</form>
