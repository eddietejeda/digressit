<div id="footer">
	
	<div class="foot">
	<?php if(has_action('custom_footer')): ?>
		<?php do_action('custom_footer'); ?>
		<?php wp_footer(); ?>
	<?php else: ?>
		<span>Powered by <a href="http://Digress.it"><b>Digress.it</b></a></span>
		<?php wp_footer(); ?>
		
	<?php endif; ?>
	</div>
</div>


</div> <!-- wrapper -->

</body>
</html> 

