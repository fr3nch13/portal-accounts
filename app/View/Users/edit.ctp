<?php ?>
<!-- File: app/View/Users/edit.ctp -->

<div class="top">
	<h1><?php echo __('Edit Settings'); ?></h1>
</div>

<div class="center">
	<div class="tabs">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php echo __('Edit Details'); ?></a></li>
				<li><a href="#tabs-2"><?php echo __('Change Password'); ?></a></li>
			</ul>
			
			<div id="tabs-1">
				<div class="form">
					<?php echo $this->Form->create('User');?>
					<fieldset>
						<!--<legend><?php echo __('Edit Details'); ?></legend>-->
					<?php
						echo $this->Form->input('id', array('type' => 'hidden'));
						echo $this->Form->input('name');
						//echo $this->Form->input('email');
						echo $this->Form->input('paginate_items', array(
							'between' => $this->Html->para('form_info', __('How many items should show up in a list by default.')),
							'options' => array(
								'10' => __('10'),
								'25' => __('25'),
								'50' => __('50'),
								'100' => __('100'),
								'150' => __('150'),
								'200' => __('200'),
								'500' => __('500 - May Load Slowly'),
								'1000' => __('1000 - May Load Slowly'),
							),
							'default' => '25',
						));
						echo $this->Form->input('UsersSetting.id', array('type' => 'hidden'));
					?>
					</fieldset>
					<?php echo $this->Form->end(__('Save Details'));?>
				</div>
			</div>
	
			<div id="tabs-2">
				<div class="form">
				<?php echo $this->Form->create('User', array('url' => array('action' => 'password')));?>
					<fieldset>
						<legend><?php echo __('Change Password'); ?></legend>
						<?php
							echo $this->Form->input('id', array('type' => 'hidden'));
							echo $this->Form->input('email', array('type' => 'hidden'));
							echo $this->Form->input('password', array('type' => 'password', 'id' => 'password'));
							echo $this->Form->input('confirm_password', array('type' => 'password', 'id' => 'confirm_password'));
							echo $this->Html->tag('div', '', array('id' => 'password_message'));
						?>
					</fieldset>
				<?php echo $this->Form->end(__('Save Password'));?>
				</div>
			</div>
			
		</div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
	$(document).ready(function () {
		
		$( "#tabs" ).tabs({select:function (event, ui) {window.location.hash = ui.tab.hash;}});
		
		$('#confirm_password, #password').on('blur', function( event ){
			var password = $('#password').val();
			var confirm_password = $('#confirm_password').val();
			var submitBtn = $(this).parents('form').find('input[type=submit]');

			var msg = '';
			if(password.length < 10)
			{
				msg = 'Your password is too short.';
			}
			else if(password != confirm_password)
			{
				msg = 'Your passwords don\'t match.';
			}
			
			if(msg)
			{
				$('#password_message').text(msg);
				submitBtn.attr('disabled','disabled');
			}
			else
			{
				$('#password_message').text(msg);
				submitBtn.removeAttr('disabled');
			}
		});
	});
	
//]]>
</script>