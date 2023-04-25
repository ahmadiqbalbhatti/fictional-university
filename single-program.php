<?php
get_header();
while ( have_posts() ) {
	the_post();
	@pageBanner( array(
		'title'    => '',
		'subtitle' => '',
	) );
	?>


	<div class="container container--narrow page-section">
		<div class="metabox metabox--position-up metabox--with-home-link">
			<p>
				<a class="metabox__blog-home-link"
				   href="<?php echo get_post_type_archive_link( 'program' ) ?>">
					<i class="fa fa-home" aria-hidden="true"></i>
					All Programs
				</a>
				<span class="metabox__main"> <?php the_title() ?></span>
			</p>
		</div>
		<div class="generic-content">
			<?php
			$mainBodyContent = get_post_meta( get_the_ID(), 'main_body_content', true );
			echo $mainBodyContent;
			$relatedProfessors = new WP_Query( array(
				'posts_per_page' => 2,
				'post_type'      => 'professor',
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_query'     => array(
					array(
						'key'     => 'related_programs',
						'compare' => 'LIKE',
						'value'   => '"' . get_the_ID() . '"',
					)
				)
			) );


			if ( $relatedProfessors->have_posts() ) {
				echo '<hr class="section-break">';
				echo '<h3 class="headline headline--medium">' . get_the_title() . ' Professor(s) </h3>';
				echo '<ul class="professor-cards">';
				while ( $relatedProfessors->have_posts() ) {
					$relatedProfessors->the_post();
					?>
					<li class="professor-card__list-item">
						<a class="professor-card" href="<?php the_permalink(); ?>">
							<img src="<?php the_post_thumbnail_url( 'professorLandscape' ); ?>"
							     class="professor-card__image"/>
							<span class="professor-card__name"><?php the_title(); ?></span>
						</a>
					</li>
					<?php
				}
				echo '</ul>';

			}

			wp_reset_postdata();
			$today = date( 'Ymd' );

			$homepageEvents = new WP_Query( array(
				'posts_per_page' => 2,
				'post_type'      => 'event',
				'meta_key'       => 'event_date',
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
				'meta_query'     => array(
					array(
						'key'     => 'event_date',
						'compare' => '>=',
						'value'   => $today,
						'type'    => 'numeric',
					),
					array(
						'key'     => 'related_programs',
						'compare' => 'LIKE',
						'value'   => '"' . get_the_ID() . '"',
					)
				)
			) );


			if ( $homepageEvents->have_posts() ) {
				echo '<hr class="section-break">';
				echo '<h3 class="headline headline--medium"> Upcoming ' . get_the_title() . ' Event(s) </h3>';
				while ( $homepageEvents->have_posts() ) {
					$homepageEvents->the_post();
					get_template_part( 'template-parts/event' );
				}
			}

			wp_reset_postdata();

			$relatedCampuses = get_field( 'related_campus' );
			if ( $relatedCampuses ) {
				echo '<hr class="section-break">';

				echo '<h3 class="headline headline--medium">' . get_the_title() . ' is Available at These Campus(es): </h3>';
				echo "<ul class='min-list link-list'>";
//				echo $relatedCampuses;

				foreach ( $relatedCampuses as $related_campus ) {
					?>
					<li>
						<a href="<?php echo get_the_permalink( $related_campus ); ?>"><?php echo get_the_title
							( $related_campus ); ?></a>
					</li>
					<?php
				}
				echo "</ul>";
			} else{
				echo '<h3 class="headline headline--medium" style="color: orangered">' . get_the_title()
				     . ' is not Available at any Campus. </h3>';

			}
			?>
		</div>
	</div>
	<?php
}
get_footer();
?>