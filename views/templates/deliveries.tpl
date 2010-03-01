<div id="delivery-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select group deliveries')?>
	</div>
	<div class="ui-widget ui-widget-content container-content" style="min-height:420px;">
		<div id="delivery-tree"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-delivery" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<?if(!get_data('myForm')):?>
	<input type='hidden' name='uri' value="<?=get_data('uri')?>" />
	<input type='hidden' name='classUri' value="<?=get_data('classUri')?>" />
<?endif?>
<script type="text/javascript">
$(document).ready(function(){
	
	if(ctx_extension){
		url = '/' + ctx_extension + '/' + ctx_module + '/';
	}
	getUrl = url + 'getDeliveries';
	setUrl = url + 'saveDeliveries';
	
	new GenerisTreeFormClass('#delivery-tree', getUrl, {
		actionId: 'delivery',
		saveUrl : setUrl,
		checkedNodes : <?=get_data('relatedDeliveries')?>,
		loadCallback: function(){
			$("#delivery-tree  li.node-instance").each(function(){
				$(this).append(
					"<img src='<?=BASE_WWW?>/img/test.png' title='<?=__('Show delvery tests')?>'  class='tests-opener' id='tests-opener_"+$(this).attr('id')+"' />" +
					"<div id='tests-viewer_"+$(this).attr('id')+"' style='display:none;'></div>"
				);
			});
			$("img.tests-opener").click(function(){
				classUri = $(this).parents('.node-class:first').attr('id');
				var deliveryUri = this.id.replace('tests-opener_', '');
				$.post(
					'/taoDelivery/Delivery/getTests', 
					{uri: deliveryUri, classUri: classUri},
					function(response){
						divNode = $("div[id='tests-viewer_" + deliveryUri+"']");
						divNode.html(response);
						divNode.dialog();
					},
					'html'
				);	
			});
		}
	});

});
</script>