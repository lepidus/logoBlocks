{**
 * settingsForm.tpl
 *
 * Copyright (c) 2003-2012 John Willinsky
 * Contributed by Lepidus Tecnologia
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * $Id$
 *}
{strip}
{assign var="pageTitle" value="plugins.block.sponsors.manager.SponsorsSettings"}
{include file="common/header.tpl"}
{/strip}
<script type="text/javascript">
{literal}
$(document).ready(function() { 
	$("#ordem").tableDnD({
	    onDrop: function(table, row) {
            var rows = table.tBodies[0].rows;
            for (var i=0; i<rows.length; i++) {
            	$("input", rows[i]).each(function() {
            		this.name = this.name.replace(/\d+/g, i);
            	});
            }
	    },
	    dragHandle: "dragHandle"
	});
	// $("#ordem tr").hover(function() {
 //          $(this.cells[0]).addClass('showDragHandle');
 //    }, function() {
 //          $(this.cells[0]).removeClass('showDragHandle');
 //    });
});
{/literal}
</script>

<div id="sponsorsSettings">
<div id="description">{translate key="plugins.block.sponsors.manager.settings.description"}</div>
 
<div class="separator"></div>

<br />

{include file="common/formErrors.tpl"}
<form name="uploadForm" method="post" enctype="multipart/form-data" action="{plugin_url path="settings"}">

	<br/>

	<h2>{translate key="plugins.block.sponsors.manager.settings.sponsorUpload"}</h2>

	<table width="100%" class="data">
		<tr valign="top">
			<td width="20%" class="label">{fieldLabel name="logo" key="plugins.block.sponsors.manager.settings.sponsorLogo"}</td>
			<td width="80%" class="value">
				<input type="file" name="logo" class="uploadField" />
			</td>
		</tr>
		<tr valign="top">
			<td width="20%" class="label">{fieldLabel name="logo" key="plugins.block.sponsors.manager.settings.sponsorUrl"}</td>
			<td width="80%" class="value"><input type="text" name="link" id="link" value="{$link|escape}" size="40" maxlength="1200" class="textField" /></td>
		</tr>
	</table>
	<input type="submit" name="uploadLogo" value="{translate key="common.upload"}" class="button" /> 

</form>

<div class="separator"></div><br/>

{if $sponsors}
<h2>{translate key="plugins.block.sponsors.manager.settings.sponsorOrder"}</h2>

<form name="sponsorOrder" method="post" action="{plugin_url path="settings"}">

	<br/>

	<table width="100%" id="ordem" class="data">
		{foreach from=$sponsors item=sponsor key=i}
			<tr valign="middle">
				<td width="20px" class="dragHandle showDragHandle"></td>
				<td width="20%" class="value"><img class="logo" width="100px" src="{$sponsorsDir}/{$sponsor.logo}"/><input type="hidden" name="sponsors[{$i}][logo]" value="{$sponsor.logo}" /></td>
				<td width="55%" class="value">{fieldLabel name="logo" key="plugins.block.sponsors.manager.settings.sponsorUrl"} <input type="text" name="sponsors[{$i}][link]" id="link" value="{$sponsor.link|default:""|escape}" size="40" maxlength="1200" class="textField" /></td>
				<td width="20%" class="label"><a href="{plugin_url path="remover"}?sponsors={$i}">Remover</a></td>
			</tr>
		{/foreach}
	</table>

	<br/>

	<input type="submit" name="saveOrder" class="button defaultButton" value="{translate key="common.save"}"/><input type="button" class="button" value="{translate key="common.cancel"}" onclick="history.go(-1)"/>
</form>
{/if}

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>


{include file="common/footer.tpl"}
