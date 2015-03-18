<div id="sidebar-bottom">
	<aside id="sidebar-1" class="sidebar">

		<?php
			if (is_active_sidebar('sidebar 1')) {
				dynamic_sidebar('sidebar 1');
			}
		?>

	</aside>

	<aside id="sidebar-2" class="sidebar">

		<?php
			if (is_active_sidebar('sidebar 2')) {
				dynamic_sidebar('sidebar 2');
			}
		?>

	</aside>
</div>
