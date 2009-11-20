<script type="text/javascript">
$(function(){

	<?if(get_data('reload') === true):?>	
		
	loadControls();
	
	<?else:?>
	
	initActions();
	
	<?endif?>
});
</script>