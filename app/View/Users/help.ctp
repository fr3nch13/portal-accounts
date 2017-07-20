<?php

$page_options = array();

if($this->Common->roleCheck('admin'))
{
	$page_options[] = $this->Html->link(__('Edit Content'), array('action' => 'edit_help', 'admin' => true));
}

if($fail_reason)
{
	$fail_reason = base64_decode($fail_reason);
	$fail_reason = $this->Html->tag('h3', $fail_reason, array('class' => 'fail_reason'));
}

$this->start('page_content');

echo $fail_reason;
echo $this->Html->tag('p', '&nbsp;');
echo $this->Html->tag('div', $helpContent, array('class' => 'md-preview md-content-rendered'));

$this->end();

echo $this->element('Utilities.page_generic', array(
	'page_title' => __('NIH Focused Ops Portal Help.'),
	'page_content' => $this->fetch('page_content'),
	'page_options' => $page_options,
));