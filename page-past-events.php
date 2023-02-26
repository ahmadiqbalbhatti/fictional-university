<?php
get_header();

?>

    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo
		get_theme_file_uri( '/images/ocean.jpg' ) ?>)
                "></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title">
                <!--				--><?php
				//				//				if ( is_category() ) {
				//				//					single_cat_title();
				//				//				} else if (is_author()){
				//				//                   echo "Post by "; the_author();
				//				//                }
				//				the_archive_title();
				//
				//				?>
                Past Events
            </h1>
            <div class="page-banner__intro">
                <p>A recap of our past event</p>
            </div>
        </div>
    </div>

    <div class="container container--narrow page-section">

		<?php
		$today           = date( 'Ymd' );
		$pastEventsQuery = new WP_Query( array(
			'paged'          => get_query_var('paged', 1),
//			'posts_per_page' => 1,
			'post_type'      => 'event',
			'meta_key'       => 'event_date',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => 'event_date',
					'compare' => '<',
					'value'   => $today,
					'type'    => 'numeric',
				)
			)
		) );
		while ( $pastEventsQuery->have_posts() ) {
			$pastEventsQuery->the_post(); ?>

            <div class="event-summary">
                <a class="event-summary__date t-center" href="#">
                        <span class="event-summary__month"><?php
	                        $eventDate = new DateTime( get_field( 'event_date' ) );
	                        echo $eventDate->format( 'M' );
	                        ?></span>
                    <span class="event-summary__day"><?php echo $eventDate->format( 'd' );
						?></span>
                </a>
                <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a
                                href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                    <p><?php echo wp_trim_words( get_the_content(), 12 ) ?> <a href="<?php
						the_permalink();
						?>" class="nu
                            gray">Learn more</a></p>
                </div>
            </div>
			<?php
		}
		echo paginate_links( array(
			'total' => $pastEventsQuery->max_num_pages,
		) );
		?>
    </div>
<?php


get_footer();

?>