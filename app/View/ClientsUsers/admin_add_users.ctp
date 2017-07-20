<?php ?>
<!-- File: app/View/ClientsUsers/admin_add_users.ctp -->
<div class="top">
	<h1><?php echo __('Add %s to this %s', __('Users'), __('Client')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('Client');?>
	    <fieldset>
	        <legend><?php echo __('Add %s to this %s', __('Users'), __('Client')); ?></legend>
	    	<?php
				echo $this->Form->input('client_id');
				echo $this->Form->input('user_ids', array(
					'label' => __('Select Users'),
					'type' => 'select',
					'multiple' => true,
					'options' => $users,
					'size' => 20,
					'searchable' => true,
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Add %s', __('Users'))); ?>
	</div>
</div>