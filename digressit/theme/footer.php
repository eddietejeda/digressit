<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>

<?php global $digressit_version; ?>

<div class="clear"></div>

<div id="footer" role="contentinfo">
<!-- If you'd like to support WordPress, having the "powered by" link somewhere on your blog is the best way; it's our only promotion or advertising. -->

	<?php  $current_version = (DIGRESSIT_VERSION) ? "(version ".DIGRESSIT_VERSION.")" : null; ?>
	<p>Powered by <a href="http://digress.it/">digress.it</a> <?php echo $current_version; ?>
	<?php wp_footer() ?>	
	</p>

</div>
</body>
</html>