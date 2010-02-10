<div id="test-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select group test')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="test-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-test" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){

	new GenerisTreeFormClass('#test-tree', "/taoGroups/Groups/getTests", {
		actionId: 'test',
		saveUrl : '/taoGroups/Groups/saveTests',
		checkedNodes : <?=get_data('relatedTests')?>
	});

});
</script>