<?php ?>
<!-- File: app/View/OrgGroup/admin_add.ctp -->
<div class="top">
	<h1><?php echo __('Add Org Group'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('OrgGroup');?>
		    <fieldset>
		        <legend><?php echo __('Add Org Group'); ?></legend>
		    	<?php
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save Org Group')); ?>
	</div>
</div>