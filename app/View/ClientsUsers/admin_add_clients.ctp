<?php ?>
<div class="top">
	<h1><?php echo __('Add %s to this %s', __('Clients'), __('User')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('User');?>
	    <fieldset>
	        <legend><?php echo __('Add %s to this %s', __('Clients'), __('User')); ?></legend>
	    	<?php
				echo $this->Form->input('id', array('type' => 'hidden'));
				echo $this->Form->input('client_ids', array(
					'label' => __('Select Clients'),
					'type' => 'select',
					'multiple' => true,
					'options' => $clients,
					'size' => 20,
					'searchable' => true,
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Add %s', __('Clients'))); ?>
	</div>
</div>