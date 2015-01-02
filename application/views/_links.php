<div class="container">
	<ul class="nav nav-tabs">
	<?php foreach($menu as $link_text => $link_url):?>
	<li><a href="<?php echo $link_url?>"><?php echo $link_text?></a><li>
	<?php endforeach; ?>
	</ul>
</div>