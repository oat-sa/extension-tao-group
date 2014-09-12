<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>

<?=get_data('memberForm')?>

<?=get_data('deliveryForm')?>

<div class="main-container">
	<h2><?=get_data('formTitle')?></h2>
	<div class="form-content">
		<?=get_data('myForm')?>
	</div>
</div>