<?php

function universityRegisterSearch(): void {
	register_rest_route( 'university/v1', 'search', array(
		'methods'  => WP_REST_Server::READABLE,
		'callback' => 'universitySearchResult'
	) );
}

add_action( 'rest_api_init', 'universityRegisterSearch' );


function universitySearchResult( $data ): array {

	$mainQuery = new WP_Query( array(
		'post_type' => array( 'post', 'page', 'professor', 'event', 'campus', 'program' ),
		's'         => sanitize_text_field( $data['term'] ),
	) );

	$results = array(
		'generalInfo' => array(),
		'professors'  => array(),
		'programs'    => array(),
		'events'      => array(),
		'campuses'    => array()

	);


	while ( $mainQuery->have_posts() ) {
		$mainQuery->the_post();

		if ( get_post_type() == 'post' or get_post_type() == 'page' ) {
			array_push( $results['generalInfo'], array(
				'title'      => get_the_title(),
				'permalink'  => get_the_permalink(),
				'postType'   => get_post_type(),
				'authorName' => get_the_author(),
			) );
		}

		if ( get_post_type() == 'professor' ) {
			array_push( $results['professors'], array(
				'title'      => get_the_title(),
				'permalink'  => get_the_permalink(),
				'postType'   => get_post_type(),
				'authorName' => get_the_author(),
				'image'      => get_the_post_thumbnail_url( 0, 'professorLandscape' )
			) );
		}

		if ( get_post_type() == 'program' ) {
			$relatedCampuses = get_field('related_campus');

			if ($relatedCampuses){
				foreach ( $relatedCampuses as $related_campus ) {
					array_push($results['campuses'], array(
						'title' => get_the_title($related_campus),
						'permalink' => get_the_permalink($related_campus),

					));
				}
			}

			array_push( $results['programs'], array(
				'id'         => get_the_ID(),
				'title'      => get_the_title(),
				'permalink'  => get_the_permalink(),
				'postType'   => get_post_type(),
				'authorName' => get_the_author(),
			) );
		}

		if ( get_post_type() == 'campus' ) {
			array_push( $results['campuses'], array(
				'title'      => get_the_title(),
				'permalink'  => get_the_permalink(),
				'postType'   => get_post_type(),
				'authorName' => get_the_author(),
			) );
		}

		if ( get_post_type() == 'event' ) {
			$eventDate = new DateTime( get_field( 'event_date' ) );

			$description = null;

			if ( has_excerpt() ) {
				$description = get_the_excerpt();
			} else {
				$description = wp_trim_words( get_the_content(), 18 );
			}
			array_push( $results['events'], array(
				'title'       => get_the_title(),
				'permalink'   => get_the_permalink(),
				'postType'    => get_post_type(),
				'authorName'  => get_the_author(),
				'month'       => $eventDate->format( 'M' ),
				'day'         => $eventDate->format( 'd' ),
				'description' => $description,
			) );
		}

	}


	if ( $results['programs'] ) {
		$programsMetaQuery = array(
			'relation' => 'OR',
		);


		foreach ( $results['programs'] as $item ) {
			array_push( $programsMetaQuery, array(
				'key'     => 'related_programs',
				'compare' => 'LINK',
				'value'   => '"' . $item['id'] . '"',
			) );
		}

		$programsRelationShipQuery = new WP_Query( array(
			'post_type'  => array('professor', 'event'),
			'meta_query' => $programsMetaQuery,
		) );

		while ( $programsRelationShipQuery->have_posts() ) {
			$programsRelationShipQuery->the_post();


			if ( get_post_type() == 'event' ) {
				$eventDate = new DateTime( get_field( 'event_date' ) );

				$description = null;

				if ( has_excerpt() ) {
					$description = get_the_excerpt();
				} else {
					$description = wp_trim_words( get_the_content(), 18 );
				}
				array_push( $results['events'], array(
					'title'       => get_the_title(),
					'permalink'   => get_the_permalink(),
					'postType'    => get_post_type(),
					'authorName'  => get_the_author(),
					'month'       => $eventDate->format( 'M' ),
					'day'         => $eventDate->format( 'd' ),
					'description' => $description,
				) );
			}


			if ( get_post_type() == 'professor' ) {
				array_push( $results['professors'], array(
					'title'      => get_the_title(),
					'permalink'  => get_the_permalink(),
					'postType'   => get_post_type(),
					'authorName' => get_the_author(),
					'image'      => get_the_post_thumbnail_url( 0, 'professorLandscape' )
				) );
			}
		}

		$results['professors'] = array_values( array_unique( $results['professors'], SORT_REGULAR ) );
		$results['events'] = array_values( array_unique( $results['events'], SORT_REGULAR ) );

	}

	return $results;
}