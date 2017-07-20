<?php 

?>
<div class="top">
	<h1><?php echo __('Edit User'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('User');?>
		<fieldset>
			<legend><?php echo __('Edit User Details'); ?></legend>
			<?php
				echo $this->Form->input('id', array(
					'type' => 'hidden'
				));
				echo $this->Form->input('name', array(
					'div' => array('class' => 'half'),
				));
				echo $this->Form->input('email', array(
					'div' => array('class' => 'half'),
				));
				echo $this->Html->divClear();
				echo $this->Form->input('adaccount', array(
					'label' => __('AD Account'),
					'div' => array('class' => 'third'),
				));
				echo $this->Form->input('phone', array(
					'label' => __('Phone Number'),
					'div' => array('class' => 'third'),
				));
				echo $this->Html->divClear();
				echo $this->Form->input('role', array(
					'div' => array('class' => 'third'),
					'options' => $this->Wrap->userRoles(),
					'default' => 'regular',
 				));
				echo $this->Form->input('org_group_id', array(
					'div' => array('class' => 'third'),
					'description' => __('Changing this will NOT update the below values.'),
 				));
	        	echo $this->Form->input('paginate_items', array(
					'div' => array('class' => 'third'),
	        		'description' => __('How many items should show up in a list by default.'),
	        		'options' => array(
	        			'10' => '10',
	        			'25' => '25',
	        			'50' => '50',
	        			'100' => '100',
	        			'150' => '150',
	        			'200' => '200',
	        		),
	        		'selected' => '25',
	        	));
			?>
		</fieldset>
		<fieldset>
<?php

$th = array();
$th['Client.client_name'] = array('content' => __('Name'));
$th['Client.allow'] = array('content' => __('Allow Access'));
$th['Client.active'] = array('content' => __('Active'));
$th['Client.role'] = array('content' => __('Role'));
$th['Client.org_group_id'] = array('content' => __('Org Group'));
$th['Client.client_id'] = array('content' => __('ID'));
$th['Client.redirect_uri'] = array('content' => __('Default Redirect URI'));

$td = array();
foreach ($clients as $i => $client)
{
	$td[$i] = array();
	$td[$i]['Client.client_name'] = $this->Html->link($client['Client']['client_name'], array('plugin' => 'o_auth_server', 'controller' => 'clients', 'action' => 'view', $client['Client']['client_id']));
	$td[$i]['Client.allow'] = $this->Form->input('ClientsUser.'.$client['Client']['client_id'].'.allow', array(
		'div' => false,
		'label' => false,
		'type' => 'checkbox',
		'checked' => (isset($activeClientsUsers[$client['Client']['client_id']])?true:false),
		'class' => array('input-name-allow'),
	));
	$td[$i]['Client.active'] = $this->Form->input('ClientsUser.'.$client['Client']['client_id'].'.active', array(
		'div' => false,
		'label' => false,
		'type' => 'checkbox',
		'checked' => (isset($activeClientsUsers[$client['Client']['client_id']]) and $activeClientsUsers[$client['Client']['client_id']]?true:false),
		'class' => array('input-name-active'),
	));
	$td[$i]['Client.role'] = $this->Form->input('ClientsUser.'.$client['Client']['client_id'].'.role', array(
		'div' => false,
		'label' => false,
		'type' => 'select',
		'options' => $client['Client']['roles'],
		'value' => (isset($roleClientsUsers[$client['Client']['client_id']])?$roleClientsUsers[$client['Client']['client_id']]:'regular'),
		'class' => array('input-name-role'),
	));
	$td[$i][] = $this->Form->input('ClientsUser.'.$client['Client']['client_id'].'.org_group_id', array(
		'div' => false,
		'label' => false,
		'type' => 'select',
		'options' => $orgGroups,
		'value' => (isset($orgGroupClientsUsers[$client['Client']['client_id']])?$orgGroupClientsUsers[$client['Client']['client_id']]:0),
		'class' => array('org_group_id_child', 'input-name-org_group_id'),
	));
	$td[$i]['Client.client_id'] = $client['Client']['client_id'];
	$td[$i]['Client.redirect_uri'] = $client['Client']['redirect_uri'];
}

if($td)
{
	// make a change all line
	$changeAll = array();
	$changeAll['Client.client_name'] = __('Change All:');
	$changeAll['Client.allow'] = $this->Form->input('ClientsUser.all.allow', array(
		'div' => false,
		'label' => false,
		'type' => 'checkbox',
		'checked' => false,
	));
	$changeAll['Client.active'] = $this->Form->input('ClientsUser.all.active', array(
		'div' => false,
		'label' => false,
		'type' => 'checkbox',
		'checked' => false,
	));
	$changeAll['Client.role'] = false;
	$changeAll['Client.org_group_id'] = $this->Form->input('ClientsUser.all.org_group_id', array(
		'div' => false,
		'label' => false,
		'type' => 'select',
		'options' => $orgGroups,
		'class' => array('org_group_id_parent'),
	));
	$changeAll['Client.client_id'] = false;
	$changeAll['Client.redirect_uri'] = false;
	
	array_unshift($td, $changeAll);
}

echo $this->element('Utilities.table', array(
	'th' => $th,
	'td' => $td,
	'use_filter' => false,
	'use_search' => false,
	'use_pagination' => false,
	'show_refresh_table' => false,
	'use_row_highlighting' => false,
	'use_jsordering' => false,
)); 
?>
		</fieldset>
		<?php echo $this->Form->end(__('Save User Details'));?>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	///// for the elements that can set all
	$('#ClientsUserAllAllow').on('change', function() {
		var parentVal = $(this).prop('checked');
		$('input.input-name-allow').each(function(){
			$(this).prop('checked', parentVal);
		});
	});
	$('#ClientsUserAllActive').on('change', function() {
		var parentVal = $(this).prop('checked');
		$('input.input-name-active').each(function(){
			$(this).prop('checked', parentVal);
		});
	});
	$('#ClientsUserAllOrgGroupId').on('change', function() {
		var parentVal = $(this).val();
		$('td select.input-name-org_group_id').each(function(){
			$(this).val(parentVal);
			$(this).trigger("chosen:updated");
		});
	});
	
	$('#UserAdaccount, #UserEmail').on('blur', function(event)
	{
		if(!$(this).val())
			return true;
		// disable all un-disabled fields
		var thisForm = $(this).parents('form');
		thisForm.find('input:enabled').addClass('temp-disabled').prop( "disabled", true );
		thisForm.find('select:enabled').addClass('temp-disabled').prop( "disabled", true );
		thisForm.find('select[searchable]').trigger("chosen:updated");
		
		var formInstance = $(this).parents('form').data('nihfo-objectForm');
		formInstance.ajax({
			url: '<?= $this->Html->url($this->Html->urlModify(array("controller"=> "contacts_ad_accounts", "action" => "user_info", "plugin" => "contacts", "admin" => false))) ?>.json',
			dataType: 'json',
			method: 'POST',
			data: { username: $('#UserAdaccount').val(), email: $('#UserEmail').val() },
			success: function(data) {
				if(data.result.name && $('#UserName').length && !$('#UserName').val())
					$('#UserName').val(data.result.name);
				if(data.result.email && $('#UserEmail').length && !$('#UserEmail').val())
					$('#UserEmail').val(data.result.email);
				if(data.result.adaccount && $('#UserAdaccount').length && !$('#UserAdaccount').val())
					$('#UserAdaccount').val(data.result.adaccount);
				if(data.result.phone && $('#UserPhone').length && !$('#UserPhone').val())
					$('#UserPhone').val(data.result.phone);
			},
			complete: function(data, textStatus, jqXHR) {
				thisForm.find('input:disabled.temp-disabled').prop( "disabled", false ).removeClass('temp-disabled');
				thisForm.find('select:disabled.temp-disabled').prop( "disabled", false ).removeClass('temp-disabled');
				thisForm.find('select[searchable]').trigger("chosen:updated");
			}
		});
	});
	

});
//]]>
</script>