<?php 

$page_options = array(
	$this->Html->link(__('Add Org Group'), array('action' => 'add')),
);

// content
$th = array(
	'OrgGroup.name' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();

foreach ($org_groups as $i => $org_group)
{
	$actions = array(
		'view' => $this->Html->link(__('View'), array('action' => 'view', $org_group['OrgGroup']['id'])),
	);
	if(isset($org_group['OrgGroup']['id']) and $org_group['OrgGroup']['id'])
	{
		$actions['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $org_group['OrgGroup']['id']));
		$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $org_group['OrgGroup']['id']),array('confirm' => __('Are you sure?')));
	}
	$actions = implode('', $actions);
	
	$td[$i] = array(
		'OrgGroup.name' => $this->Html->link($org_group['OrgGroup']['name'], array('action' => 'view', $org_group['OrgGroup']['id'])),
		'actions' => array(
			$actions, 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Org Groups'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));