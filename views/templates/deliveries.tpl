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
<script type="text/javascript">
$(document).ready(function(){
	require(['require', 'jquery', 'generis.tree.select'], function(req, $, GenerisTreeSelectClass) {
		new GenerisTreeSelectClass('#delivery-tree', '<?=get_data('dataUrl')?>', {
			actionId: 		'delivery',
			saveUrl: '<?=get_data('saveUrl')?>',
			saveData: {
				resourceUri: '<?=get_data('resourceUri')?>',
				propertyUri: '<?=get_data('propertyUri')?>'
			},
			checkedNodes: <?=json_encode(tao_helpers_Uri::encodeArray(get_data('values')))?>,
					serverParameters: {
						openNodes: <?=json_encode(get_data('openNodes'))?>,
						rootNode: <?=json_encode(get_data('rootNode'))?>
					},
			paginate: 10,
			loadCallback: function(){
				$.postJson("<?=_url('getDeliveriesTests', 'Delivery', 'taoDelivery')?>", {}, function(response){
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
});
</script>