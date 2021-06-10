<?php
/**
 * The sidebar containing the main home columns widget areas
 *
 */
?>

<div id="top-widget">
	<?php dynamic_sidebar( 'homepage-top-widget-area' ); ?>
</div>
<div id="home-cols">
	<div id="home-cols-inner">
		<?php 
			/**
			 * Display widgets dragged in 'Homepage Columns 1' widget areas
			 */
		?>
		<div class="col3a">
			<?php dynamic_sidebar( 'homepage-column-1-widget-area' ); ?>
		</div><!-- .col3a -->
		<?php 
			/**
			 * Display widgets dragged in 'Homepage Columns 2' widget areas
			 */
		?>
		<div class="col3b">
			<?php dynamic_sidebar( 'homepage-column-2-widget-area' ); ?>					
		</div><!-- .col3b -->
		<?php 
			/**
			 * Display widgets dragged in 'Homepage Columns 3' widget areas
			 */
		?>
		<div class="col3c">
			<?php dynamic_sidebar( 'homepage-column-3-widget-area' ); ?>
		</div><!-- .col3c -->
		
		<div class="clear">
		</div><!-- .clear -->
	</div><!-- #home-cols-inner -->
</div><!-- #home-cols -->