<?php 
$page_options = array();

if($org_group['OrgGroup']['id'])
{
	$page_options['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $org_group['OrgGroup']['id']));
	$page_options['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $org_group['OrgGroup']['id']),array('confirm' => __('Are you sure?')));
}

$details = array(
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($org_group['OrgGroup']['created'])),
	array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($org_group['OrgGroup']['modified'])),
);


$stats = array();
$tabs = array();
$stats[] = array(
	'id' => 'users',
	'name' => __('Users'), 
	'ajax_count_url' => array('controller' => 'users', 'action' => 'org_group', $org_group['OrgGroup']['id']),
	'tab' => array('tabs', count($tabs)+1), // the tab to display
);

$tabs[] = array(
	'key' => 'users',
	'title' => __('Users'),
	'url' => array('controller' => 'users', 'action' => 'org_group', $org_group['OrgGroup']['id']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('Org Group'), $org_group['OrgGroup']['name']),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));