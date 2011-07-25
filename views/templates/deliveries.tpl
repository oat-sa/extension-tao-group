<div id="delivery-container" class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Select group deliveries')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="delivery-tree"></div>
		<div class="breaker"></div>
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
	
	getUrl = "<?=_url('getDeliveries')?>";
	setUrl = "<?=_url('saveDeliveries')?>";
	getTests = "<?=_url('getDeliveriesTests', 'Delivery', 'taoDelivery')?>";
	
	new GenerisTreeFormClass('#delivery-tree', getUrl, {
		actionId: 		'delivery',
		saveUrl: 		setUrl,
		checkedNodes: 	<?=get_data('relatedDeliveries')?>,
		paginate: 		10,
		loadCallback: function(){
			$.postJson(getTests, {}, function(response){
				if(response.data){
					var tests = response.data;

					$("#delivery-tree  li.node-instance").each(function(){
						deliveryUri = $(this).attr('id');
						if(tests[deliveryUri]){
							testContent = '';
							testContent += "<img src='<?=BASE_WWW?>/img/test.png'  class='tests-opener' id='tests-opener_"+deliveryUri+"' />";
							testContent += "<div id='tests-viewer_"+deliveryUri+"' class='ui-state-highlight' style='display:none;'>" ;
							testContent += __('Related tests') + ":<br />";
							for(test in tests[deliveryUri]){
								testContent += " - " + tests[deliveryUri][test]['label'] + "<br />";
							}
							testContent += "</div>";
							$(this).append(testContent);
						}
					});
					$(".tests-opener").mouseover(function(){
						$("div[id='" + this.id.replace('tests-opener_', 'tests-viewer_')+"']").show();
					});
					$(".tests-opener").mouseout(function(){
						$("div[id='" + this.id.replace('tests-opener_', 'tests-viewer_')+"']").hide();
					});
				}
			});
		}
	});

});
</script>