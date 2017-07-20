<?php 
?>
<div class="top">
	<h1><?php echo __('Update User Landing/Help Page'); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('User', array('id' => 'helpPage')); ?>
		    <fieldset>
		       <?php
				echo $this->Form->input('user_info_md', array(
					'type' => 'textarea',
					'label' => __('Markdown/HTML for the landing page content.'),
					'between' => __('This uses basic markdown syntax, but can also support html tags.'),
					'id' => 'md-editor',
				));
				echo $this->Form->input('user_info_html', array(
					'type' => 'hidden',
					'id' => 'userInfoHtml',
				));
				?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Landing/Help Page'))); ?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	$("#md-editor").markdown({
		iconlibrary: 'fa',
		height: 400,
		fullscreen: false,
		onBlur: function(e) {
			$('#userInfoHtml').val(e.parseContent());
		},
		onChange: function(e) {
			$('#userInfoHtml').val(e.parseContent());
		},
	});
});
//]]>
</script>