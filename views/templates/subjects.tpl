<div id="subject-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select group testees')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="subject-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-subject" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	
	new GenerisTreeFormClass('#subject-tree', "/taoGroups/Groups/getMembers", {
		actionId: 'subject',
		saveUrl : '/taoGroups/Groups/saveMembers',
		checkedNodes : <?=get_data('relatedSubjects')?>
	});
	
});
</script>