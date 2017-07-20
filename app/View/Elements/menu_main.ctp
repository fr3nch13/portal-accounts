<?php  
?>
<?php if (AuthComponent::user('id')): ?>
<ul class="sf-menu">
	<li><?php echo $this->Html->link(__('View %s', __('Account Details')), array('controller' => 'users', 'action' => 'view', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
	<?php echo $this->Common->loadPluginMenuItems(); ?>
	
	<li>
		<?php echo $this->Html->link(__('Contacts'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Associated Accounts'), array('controller' => 'assoc_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'assoc_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'assoc_accounts', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'assoc_accounts', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('AD Accounts'), array('controller' => 'ad_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'ad_accounts', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'ad_accounts', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'ad_accounts', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'ad_accounts', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orginazional Chart'), array('controller' => 'ad_accounts', 'action' => 'orgchart', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('SACs'), array('controller' => 'sacs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'sacs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'sacs', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'sacs', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'sacs', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orginazional Chart'), array('controller' => 'sacs', 'action' => 'orgchart', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('Branches'), array('controller' => 'branches', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'branches', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'branches', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'branches', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'branches', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orginazional Chart'), array('controller' => 'branches', 'action' => 'orgchart', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('Divisions'), array('controller' => 'divisions', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'divisions', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'divisions', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'divisions', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orphans'), array('controller' => 'divisions', 'action' => 'orphans', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orginazional Chart'), array('controller' => 'divisions', 'action' => 'orgchart', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('ORG/ICs'), array('controller' => 'orgs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All'), array('controller' => 'orgs', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Duplicates'), array('controller' => 'orgs', 'action' => 'duplicates', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Empty'), array('controller' => 'orgs', 'action' => 'empties', 'admin' => false, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Orginazional Chart'), array('controller' => 'orgs', 'action' => 'orgchart', 'admin' => false, 'plugin' => false)); ?></li>
				</ul>
			</li>
		</ul>
	</li>
	<?php if (AuthComponent::user('id') and AuthComponent::user('role') == 'admin'): ?>
	<li>
		<?php echo $this->Html->link(__('Admin'), '#', array('class' => 'top')); ?>
		<ul>
			<li><?php echo $this->Html->link(__('Clients'), array('controller' => 'clients', 'action' => 'index', 'admin' => true, 'plugin' => 'o_auth_server')); ?></li>
			<li><?php echo $this->Html->link(__('Org Groups'), array('controller' => 'org_groups', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
			<li>
				<?php echo $this->Html->link(__('Users'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('All %s', __('Users')), array('controller' => 'users', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Login History'), array('controller' => 'login_histories', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->link(__('App Admin'), '#', array('class' => 'sub')); ?>
				<ul>
					<li><?php echo $this->Html->link(__('Config'), array('controller' => 'users', 'action' => 'config', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Edit Landing/Help page'), array('controller' => 'users', 'action' => 'edit_help', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Statistics'), array('controller' => 'users', 'action' => 'stats', 'admin' => true, 'plugin' => false)); ?></li>
					<li><?php echo $this->Html->link(__('Process Times'), array('controller' => 'proctimes', 'action' => 'index', 'admin' => true, 'plugin' => 'utilities')); ?></li> 
				</ul>
			</li>
			<?php echo $this->Common->loadPluginMenuItems('admin'); ?>
		</ul>
	</li>
	<?php endif; ?>
</ul>
<?php endif; ?>