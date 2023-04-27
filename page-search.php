<?php
get_header();
while ( have_posts() ) {
	the_post();
	pageBanner( array(
		'title'    => '',
		'subtitle' => '',
		'photo'    => '',

	) );
	?>


	<div class="container container--narrow page-section">

		<?php
		$theParentID = wp_get_post_parent_id( get_the_ID() );
		if ( $theParentID ) {

			?>
			<div class="metabox metabox--position-up metabox--with-home-link">
				<p>
					<a class="metabox__blog-home-link"
					   href="<?php echo get_permalink( $theParentID ); ?>">
						<i class="fa fa-home" aria-hidden="true"></i>
						Back to
						<?php echo get_the_title( $theParentID ); ?>
					</a>
					<span class="metabox__main"><?php the_title(); ?></span>
				</p>
			</div>
			<?php
		}
		?>

		<?php
		$testArray = get_pages( array(
			'child_of' => get_the_ID(),
		) );
		if ( $theParentID or $testArray ) { ?>
			<div class="page-links">
				<h2 class="page-links__title"><a
						href="<?php echo get_permalink( $theParentID ) ?>"><?php echo
						get_the_title
						( $theParentID )
						?></a></h2>
				<ul class="min-list">
					<?php

					if ( $theParentID ) {
						$theChildrenOf = $theParentID;
					} else {
						$theChildrenOf = get_the_ID();
					}
					wp_list_pages( array(
						'title_li'    => null,
						'child_of'    => $theChildrenOf,
						'sort_column' => 'menu_order',
					) );
					?>
				</ul>
			</div>

		<?php } ?>

		<div class="generic-content">
			<!--			--><?php //the_content(); ?>
			<?php
			get_search_form();
			?>
		</div>
	</div>

	<?php
}

get_footer();
?>