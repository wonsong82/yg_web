<?php
$appJS = (defined('WP_DEBUG') && WP_DEBUG) ?
	'http://localhost:8080/app.js' :
	'/static/app.js';

$staticJS = file_exists(ABSPATH . 'static/static-page.js') ?
	'<script src="/static/static-page.css"></script>' : '';

$staticPageJS = file_exists(ABSPATH . "static/static-{$postName}.js") ?
	'<script href="/static/static-' . $postName . '.js"></script>' : '';

?>
   </div> <!-- Page end -->
   <?php wp_footer(); ?>
<div id="root">
	<div class="page-loading">
		<div class="page-loading__spinner">
			<div class="SquareSpinner">
				<span class="tl box"></span>
				<span class="tr box"></span>
				<span class="bl box"></span>
				<span class="br box"></span>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo $appJS ?>"></script>
<?php echo $staticJS ?>
<?php echo $staticPageJS ?>
</body>
</html>
