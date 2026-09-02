<?php
	 
	
?>
<nav class="page" aria-label="pagination">
	<ul class="pagination">
		<?php if($current_page > 1) : ?>
		<li>
		<a href="?p=<?php echo $current_page -1 ?>">
			<span aria-hidden="true">&laquo;</span>
			<span class="visuallyhidden">ensemble de pages précédent</span>
		</a>
		</li>
		<?php endif; ?>
		<li>
		<a href=""><span class="visuallyhidden">page </span><?php echo $current_page?></a>
		</li>
		<?php if($current_page < $page_number ) : ?>
		<li>
		<a href="?p=<?php echo $current_page + 1 ?>">
			<span class="visuallyhidden">ensemble de pages suivant</span
			><span aria-hidden="true">&raquo;</span>
		</a>
		</li>
		<?php endif; ?>
	</ul>
</nav>