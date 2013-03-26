<? include(TAO_TPL_PATH . 'form_context.tpl') ?>

<? if (tao_helpers_funcACL_funcACL::hasAccess('taoGroups', 'Groups', 'getMembers')): ?>
<? include('subjects.tpl') ?>
<? endif; ?>

<? if (tao_helpers_funcACL_funcACL::hasAccess('taoDelivery', 'Delivery', 'getDeliveriesTests')): ?>
<? include('deliveries.tpl') ?>
<? endif; ?>

<div class="main-container medium">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<? include(TAO_TPL_PATH . 'footer.tpl') ?>