<?php ?>
<!-- File: app/View/Users/login.ctp -->

<div class="top">
	<h1><?php echo _('Login'); ?></h1>
	<?php if(isset($client['Client']['client_name'])): ?>
	<h3><?php 
	$clientLink = $this->Html->link($client['Client']['client_name'], $client['Client']['redirect_uri']);
	echo __('Please login here to access %s.', $clientLink); 
	?></h3>
	<?php endif; ?>
</div>
<div class="center">
	<div class="left">
	<div class="users form">
	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('User');?>
	    <fieldset>
	        <legend><?php echo __('Please enter your email and password'); ?></legend>
	    <?php
	        echo $this->Form->input('email');
	        echo $this->Form->input('password');
	        
	        if(isset($OAuthServerParams) and is_array($OAuthServerParams))
	        {
	        	foreach ($OAuthServerParams as $key => $value)
	        	{
				echo $this->Form->hidden(h($key), array('value' => h($value)));
			}
	        }
	    ?>
	    </fieldset>
	<?php echo $this->Form->end(__('Login'));?>
	</div>
	</div>
	<div class="right">
		<?php echo $this->element('Utilities.login_banner'); ?>
	</div>
</div>

