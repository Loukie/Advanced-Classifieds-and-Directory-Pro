(function( $ ) {
	'use strict';
	
	/**
     * Render a Google Map onto the selected jQuery element.
     *
	 * @since    1.0.0
	 */
	function acadp_render_map( $el ) {

		// var
		var $marker = $el.find('.marker');

		// vars
		var args = {
			zoom	    : parseInt( acadp.zoom_level ),
			center	    : new google.maps.LatLng(0, 0),
			mapTypeId   : google.maps.MapTypeId.ROADMAP,
			scrollwheel : false
		};

		// create map	        	
		var map = new google.maps.Map( $el[0], args );

		// add a markers reference
		map.marker = null;
	
		// add marker
		acadp_add_marker( $marker, map );

		// center map
		acadp_center_map( map );
		
		// Update map when contact details fields are updated in the custom post type "acadp_listings"
		$( '.acadp-map-field', '#acadp-contact-details' ).on('blur', function() {

			var address = [];
			address.push( $( '#acadp-address' ).val() );
			address.push( $( '#acadp_location' ).find('option:selected').text() );
			address.push( $( '#acadp-zipcode' ).val() );			
			address = address.filter( function( v ) { return v !== '' } );
			address = address.join();
			
			acadp_get_latlng_from_address( address, map );

		});

	};
	
	
	/**
	 * Add a marker to the selected Google Map.
	 *
	 * @since    1.0.0
	 */
	function acadp_add_marker( $marker, map ) {

		var lat = $( '#acadp-latitude' ).val();
		var lng = $( '#acadp-longitude' ).val();
		
		if( ! lat && ! lng ) {
			
			acadp_get_latlng_from_address( $( '#acadp_location option:selected' ).text(), map );
			
			lat = $( '#acadp-latitude' ).val();
			lng = $( '#acadp-longitude' ).val();
			
		};
		
		var latlng = new google.maps.LatLng( lat, lng );

		// create marker
		var marker = new google.maps.Marker({
			position  : latlng,
			map		  : map,
			draggable : true
		});

		// add to array
		map.marker = marker;
	
		// if marker contains HTML, add it to an infoWindow
		if( $marker.html() ) {
			// create info window
			var infowindow = new google.maps.InfoWindow({
				content	: $marker.html()
			});

			// show info window when marker is clicked
			google.maps.event.addListener(marker, 'click', function() {
	
				infowindow.open( map, marker );

			});
		};
		
		// update latitude and longitude values in the form when marker is moved
		google.maps.event.addListener(marker, "dragend", function() {
																  
  			var point = marker.getPosition();
			map.panTo(point);
			acadp_update_latlng(point);
			
		});		

	};
	

	/**
	 * Center the map, showing all markers attached to this map.
     *
	 * @since    1.0.0
	 */
	function acadp_center_map( map ) {

		// vars
		var bounds = new google.maps.LatLngBounds();

		// create bounds
		if( map.marker != null ) {
			
			var latlng = new google.maps.LatLng( map.marker.position.lat(), map.marker.position.lng() );
		
			bounds.extend( latlng );
			
		}

		map.setCenter( bounds.getCenter() );
	    map.setZoom( parseInt( acadp.zoom_level ) );

	};
	
	
	/**
	 * Set the latitude and longitude values from the address.
     *
	 * @since    1.0.0
	 */
	function acadp_get_latlng_from_address( address, map ) {
		
		var geoCoder = new google.maps.Geocoder();
		
		geoCoder.geocode({'address': address}, function(results, status) {
															
   			if( status == google.maps.GeocoderStatus.OK ) {
					
				var point = results[0].geometry.location;									
				map.marker.setPosition( point );
				acadp_center_map( map );
				acadp_update_latlng( point );	
				
    		};
				
   		});
		
	};
	
	/**
	 * Update the latitude and logitude values in the custom post type "acadp_listings".
     *
	 * @since    1.0.0
	 */
	function acadp_update_latlng( point ) {
		
		$( '#acadp-latitude' ).val( point.lat() );
		$( '#acadp-longitude' ).val( point.lng() );
			
	};
	
	/**
	 *  Make images inside the listing form sortable.
     *
	 *  @since    1.0.0
	 */
	function acadp_sort_images() {
		
		var $sortable_element = $('#acadp-images tbody');
			
		if( $sortable_element.hasClass('ui-sortable') ) {
			$sortable_element.sortable('destroy');
		};
			
		$sortable_element.sortable({
			handle: '.acadp-handle'
		});
		
	};	
	
	/**
 	 * Display the media uploader for selecting an image.
 	 *
 	 * @since    1.0.0
 	 */
	function acadp_render_media_uploader( page ) {
 
    	var file_frame, image_data, json;
 
     	// If an instance of file_frame already exists, then we can open it rather than creating a new instance
    	if( undefined !== file_frame ) {
 
        	file_frame.open();
        	return;
 
    	}; 

     	// Here, use the wp.media library to define the settings of the media uploader
    	file_frame = wp.media.frames.file_frame = wp.media({
        	frame    : 'post',
        	state    : 'insert',
        	multiple : false
    	});
 
     	// Setup an event handler for what to do when an image has been selected
    	file_frame.on( 'insert', function() {
 
        	// Read the JSON data returned from the media uploader
    		json = file_frame.state().get( 'selection' ).first().toJSON();
		
			// First, make sure that we have the URL of an image to display
    		if( 0 > $.trim( json.url.length ) ) {
        		return;
    		};
		
			// After that, set the properties of the image and display it
			if( 'listings' == page ) {
				
				var html = '<tr class="acadp-image-row">' + 
								'<td class="acadp-handle"><span class="dashicons dashicons-screenoptions"></span></td>' +          	
								'<td class="acadp-image">' + 
									'<img src="' + json.url + '" />' + 
									'<input type="hidden" name="images[]" value="' + json.id + '" />' + 
								'</td>' + 
								'<td>' + 
									json.url + '<br />' + 
									'<a href="post.php?post=' + json.id + '&action=edit" target="_blank">' + acadp.edit + '</a> | ' + 
									'<a href="javascript:;" class="acadp-delete-image" data-attachment_id="' + json.id + '">' + acadp.delete_permanently + '</a>' + 
								'</td>' +                 
							'</tr>';
			
				$( '#acadp-images' ).append( html );
				
				acadp_sort_images();
			
			} else {
				
				$( '#acadp-categories-image-id' ).val( json.id );
				$( '#acadp-categories-image-wrapper' ).html( '<img src="' + json.url + '" />' );
				
			}
 
    	});
 
    	// Now display the actual file_frame
    	file_frame.open();
 
	};
	

	/**
	 * Called when the page has loaded.
	 *
	 * @since    1.0.0
	 */
	$(function() {

		// Dismiss upgrade notice
		$( '#acadp-upgrade-notice' ).on( 'click', '.notice-dismiss', function( e ) {
																			  
			e.preventDefault();
			
			var data = {
				'action' : 'acadp_dismiss_admin_notice'
			};
			
			$.post( ajaxurl, data );
			
		});
		
		// Show or hide field options when field type changed in the custom post type "acadp_fields"
		$( '.field-type select', '#acadp-field-details' ).on( 'change', function() {
								
			var num_fields = $('.field-options').length;
			var option = $(this).val();
			$( '.field-options', '#acadp-field-details' ).fadeOut( 200, function() {
				if( --num_fields > 0 ) return;
				$( '.field-option-' + option, '#acadp-field-details' ).fadeIn( 200 );
			});
			
		}).change();
		
		// Load custom fields of the selected category in the custom post type "acadp_listings"
		$( '#acadp_category' ).on( 'change', function() {
								
			$( '#acadp-custom-fields-list' ).html( '<div class="spinner"></div>' );
			
			var data = {
				'action'  : 'acadp_custom_fields_listings',
				'post_id' : $( '#acadp-custom-fields-list' ).data('post_id'),
				'term_id' : $(this).val()
			};
			
			$.post( ajaxurl, data, function(response) {
				$( '#acadp-custom-fields-list' ).html( response );
			});
			
		});		
		
		// Render map in the custom post type "acadp_listings"	
		$('.acadp-map').each(function() {
									  
			acadp_render_map( $(this) );

		});		
		
		// Display the media uploader when "Upload Image" button clicked in the custom post type "acadp_listings"		
		$( '#acadp-upload-image' ).on( 'click', function( e ) {
 
            e.preventDefault();
 
            acadp_render_media_uploader( 'listings' );
 
        });
		
		// Make the isting images sortable in the custom post type "acadp_listings"
		acadp_sort_images();
		
		// Delete the selected image when "Delete Permanently" button clicked in the custom post type "acadp_listings"	
		$( '#acadp-images' ).on( 'click', 'a.acadp-delete-image', function( e ) {
														 
            e.preventDefault();
								
			var $this = $( this );
			
			var data = {
				'action'        : 'acadp_delete_attachment',
				'attachment_id' : $this.data('attachment_id')
			};
			
			$.post( ajaxurl, data, function(response) {
				$this.closest( 'tr' ).remove();
			});
			
		});
		
		// Display the media uploader when "Upload Image" button clicked in the custom taxonomy "acadp_categories"		
		$( '#acadp-categories-upload-image' ).on( 'click', function( e ) {
 
            e.preventDefault();
 
            acadp_render_media_uploader( 'categories' );
 
        });
		
		// Delete the image when "Remove Image" button clicked in the custom taxonomy "acadp_categories"	
		$( '#acadp-categories-remove-image' ).on( 'click', function( e ) {
														 
            e.preventDefault();
			
			var id = parseInt( $( '#acadp-categories-image-id' ).val() );
			
			if( id > 0 ) {
				
				var data = {
					'action'        : 'acadp_delete_attachment',
					'attachment_id' : id
				};
				
				$.post( ajaxurl, data, function(response) {
					$( '#acadp-categories-image-id' ).val( '' );
					$( '#acadp-categories-image-wrapper' ).html( '' );
				});
				
			};
			
		});
		
		// Clear the image field after the custom taxonomy "acadp_categories" term was created.
		$( document ).ajaxComplete(function( event, xhr, settings ) {
			
			if( $( "#acadp-categories-image-id" ).length ) {
				
				var queryStringArr = settings.data.split( '&' );
			   
				if( $.inArray( 'action=add-tag', queryStringArr ) !== -1 ) {
					var xml = xhr.responseXML;
					var response = $( xml ).find( 'term_id' ).text();
					if( response != "" ) {
						$( '#acadp-categories-image-id' ).val( '' );
						$( '#acadp-categories-image-wrapper' ).html( '' );
					};
				};
			
			};
			
		});
	
	});

})( jQuery );