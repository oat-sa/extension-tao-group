<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>


<div class="main-container flex-container-main-form">
	<h2><?=get_data('formTitle')?></h2>
	<div class="form-content">
		<?=get_data('myForm')?>
	</div>
</div>

<div class="data-container-wrapper flex-container-remainder">
    <?=get_data('memberForm')?>

    <?=get_data('deliveryForm')?>
</div>

<?php
Template::inc('footer.tpl', 'tao');
?>
