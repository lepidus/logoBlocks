<style>
{literal}
.sponsors-logo-preview { margin: 24px 0; }
.sponsors-logo-editor { display: flex; align-items: flex-start; gap: 24px; }
.sponsors-current-logo { flex: 0 0 200px; }
.sponsors-current-logo img { display: block; width: 150px; height: auto; max-width: 100%; max-height: 150px; object-fit: contain; }
.pkp_form .sponsors-update-logo { display: block; margin-top: 12px; }
.sponsors-logo-upload { flex: 1; min-width: 0; }
.sponsors-logo-upload[hidden] { display: none; }
.sponsors-upload-row { display: grid; grid-template-columns: 200px minmax(0, 1fr); grid-template-areas: "preview crop"; align-items: center; gap: 20px 24px; }
.sponsors-upload-row.has-upload { grid-template-areas: "picker info" "preview crop"; }
.sponsors-upload-picker { grid-area: picker; min-width: 0; }
.pkp_form .sponsors-select-logo { position: relative; display: inline-flex; width: 100%; align-items: center; justify-content: center; gap: 8px; box-sizing: border-box; padding: 10px 16px; border: 1px solid #aaa; border-radius: 6px; background: #f5f5f5; color: #222; cursor: pointer; font-weight: normal; }
.sponsors-select-logo span { white-space: nowrap; }
.sponsors-select-logo svg { flex-shrink: 0; }
.sponsors-select-logo:hover { background: #e9eef2; }
.sponsors-select-logo:focus-within { outline: 2px solid #006798; outline-offset: 3px; }
.pkp_form .sponsors-select-logo input[type="file"] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; margin: 0; padding: 0; }
.sponsors-upload-filename { display: block; margin-top: 8px; overflow-wrap: anywhere; color: #555; font-size: 13px; }
.sponsors-preview-frame { display: flex; align-items: center; justify-content: center; grid-area: preview; box-sizing: border-box; width: 200px; height: 180px; padding: 12px; border: 1px solid #aaa; border-radius: 8px; background: #fff; box-shadow: inset 0 0 0 6px #f5f5f5; }
.sponsors-preview-result { margin: 0; max-width: 100%; }
.sponsors-preview-result img { display: block; width: auto; height: auto; max-width: 100%; max-height: 100px; }
.sponsors-preview-placeholder { color: #777; }
.sponsors-upload-info { grid-area: info; align-self: start; min-width: 0; }
.sponsors-upload-info p { margin: 0; line-height: 1.5; white-space: pre-line; }
.sponsors-crop-control { grid-area: crop; min-width: 0; }
.pkp_form .sponsors-crop-switch { position: relative; display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal; }
.pkp_form .sponsors-crop-switch input { position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 42px; height: 24px; margin: 0; opacity: 0; cursor: pointer; z-index: 1; }
.sponsors-switch-track { display: inline-block; flex: 0 0 42px; width: 42px; height: 24px; border: 1px solid #64717a; border-radius: 14px; background: #737e86; box-sizing: border-box; }
.sponsors-switch-track::before { content: ''; display: block; width: 18px; height: 18px; margin: 2px; border-radius: 50%; background: #fff; }
.sponsors-crop-switch input:checked + .sponsors-switch-track { background: #006798; border-color: #006798; }
.sponsors-crop-switch input:checked + .sponsors-switch-track::before { transform: translateX(18px); }
.sponsors-crop-switch input:focus-visible + .sponsors-switch-track { outline: 2px solid #006798; outline-offset: 3px; }
.sponsors-crop-switch input:disabled ~ span { opacity: .55; cursor: default; }
.sponsors-crop-help { margin: 8px 0 0; color: #555; }
@media (max-width: 600px) {
    .sponsors-logo-editor { flex-direction: column; }
    .sponsors-logo-upload { width: 100%; }
    .sponsors-logo-editor.is-editing .sponsors-upload-row { grid-template-columns: minmax(0, 1fr); }
    .sponsors-upload-row { grid-template-columns: minmax(0, 1fr); grid-template-areas: "preview" "crop"; gap: 16px; }
    .sponsors-upload-row.has-upload { grid-template-areas: "picker" "info" "preview" "crop"; }
    .sponsors-upload-picker { max-width: 200px; }
    .sponsors-preview-frame { max-width: 100%; }
}
{/literal}
</style>
<div class="sponsors-logo-preview">
	<div class="sponsors-logo-editor{if $currentLogoUrl} is-editing{/if}">
		{if $currentLogoUrl}
			<div class="sponsors-current-logo">
				<div class="sponsors-preview-frame">
					<img src="{$currentLogoUrl|escape}" alt="{$name|escape}" width="150">
				</div>
				<button type="button" class="pkp_button sponsors-update-logo" aria-expanded="false" aria-controls="{$previewFormId|escape}-upload">{translate key="plugins.block.sponsorsBlock.updateLogo"}</button>
			</div>
		{/if}
		<div class="sponsors-logo-upload" id="{$previewFormId|escape}-upload" {if $currentLogoUrl}hidden{/if}>
	<div class="sponsors-upload-row{if $previewFormId == 'sponsorsBlockLogoForm'} has-upload{/if}">
		{if $previewFormId == 'sponsorsBlockLogoForm'}
			<div class="sponsors-upload-picker">
				<label class="sponsors-select-logo">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false"><path d="M12 16V3m-5 5 5-5 5 5M4 15v6h16v-6"/></svg>
					<span>{translate key="plugins.block.sponsorsBlock.selectLogo"}</span>
					<input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/gif" aria-describedby="{$previewFormId|escape}-imageHelp">
				</label>
				<span class="sponsors-upload-filename" aria-live="polite"></span>
			</div>
		{/if}
		{if $previewFormId == 'sponsorsBlockLogoForm'}
			<div class="sponsors-upload-info" id="{$previewFormId|escape}-imageHelp">
				<p>{translate key="plugins.block.sponsorsBlock.imageSummary"}</p>
			</div>
		{/if}
		<div class="sponsors-preview-frame">
			<svg class="sponsors-preview-placeholder" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true" focusable="false"><rect x="2" y="2" width="20" height="20" rx="2"/><circle cx="8" cy="8" r="2"/><path d="m3 19 6-6 4 4 4-6 5 8"/></svg>
			<figure class="sponsors-preview-result" style="display: none;">
				<figcaption class="pkp_screen_reader">{translate key="plugins.block.sponsorsBlock.preview"}</figcaption>
				<img alt="{translate key="plugins.block.sponsorsBlock.preview"}" width="150">
			</figure>
		</div>
		{if $previewFormId == 'sponsorsBlockLogoForm'}
		<div class="sponsors-crop-control">
			<label class="sponsors-crop-switch">
				<input type="checkbox" role="switch" name="crop" value="1" aria-describedby="{$previewFormId|escape}-cropHelp" {if $crop}checked{/if}>
				<span class="sponsors-switch-track" aria-hidden="true"></span>
				<span>{translate key="plugins.block.sponsorsBlock.crop"}</span>
			</label>
			<p class="sponsors-crop-help" id="{$previewFormId|escape}-cropHelp">{translate key="plugins.block.sponsorsBlock.cropSummary"}</p>
		</div>
		{/if}
	</div>
	<p class="sponsors-preview-loading" role="status" style="display: none;">{translate key="plugins.block.sponsorsBlock.previewLoading"}</p>
	<p class="sponsors-preview-error pkp_form_error" role="alert" style="display: none;">{translate key="plugins.block.sponsorsBlock.previewError"}</p>
		</div>
	</div>
</div>
<script>
$(function() {ldelim}
	var $form = $('#{$previewFormId|escape:"javascript"}');
{literal}
	var $crop = $form.find('[name="crop"]');
	var $result = $form.find('.sponsors-preview-result');
	var $image = $result.find('img');
	var $placeholder = $form.find('.sponsors-preview-placeholder');
	var $loading = $form.find('.sponsors-preview-loading');
	var $error = $form.find('.sponsors-preview-error');
	var pending = null;
	var revision = 0;

	$form.on('click', '.sponsors-update-logo', function() {
		$form.find('.sponsors-logo-upload').prop('hidden', false);
		$(this).attr('aria-expanded', 'true');
		$form.find('[name="logo"]').trigger('focus');
	});

	function updatePreview() {
		var requestRevision = ++revision;
		if (pending) {
			pending.abort();
			pending = null;
		}
		$loading.hide();
		$error.hide();
		var fileInput = $form.find('[name="logo"]')[0];
		$form.find('.sponsors-upload-filename').text(fileInput && fileInput.files.length ? fileInput.files[0].name : '');
		var hasImage = fileInput ? fileInput.files.length > 0 : $form.find('[name="galleryId"]:checked').length > 0;
		$crop.prop('disabled', !hasImage);
		if (!hasImage) {
			$placeholder.show();
			$result.hide();
			return;
		}
		$result.hide();
		$placeholder.show();
		$loading.show();
		pending = $.ajax({
			url: $form.attr('data-preview-url'),
			type: 'POST',
			data: new FormData($form[0]),
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function(response) {
				if (requestRevision !== revision || !$.contains(document, $form[0])) { return; }
				if (response.status && typeof response.content === 'string' && response.content.indexOf('data:image/png;base64,') === 0) {
					$placeholder.hide();
					$image.attr('src', response.content);
					$result.show();
				} else {
					$error.show();
				}
			},
			error: function(xhr, status) {
				if (requestRevision === revision && status !== 'abort') { $error.show(); }
			},
			complete: function() {
				if (requestRevision === revision) {
					$loading.hide();
					pending = null;
				}
			}
		});
	}
	$form.on('change', '[name="logo"], [name="galleryId"], [name="crop"]', updatePreview);
	updatePreview();
});
{/literal}
</script>
