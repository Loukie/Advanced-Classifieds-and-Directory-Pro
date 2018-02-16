(function( $ ) {
	'use strict';
	
	/**
     *  Render a Google Map onto the selected jQuery element.
     *
	 *  @since    1.0.0
	 */
	function acadp_render_map( $el ) {

		// var
		var $markers = $el.find('.marker');

		// vars
		var args = {
			zoom	    : parseInt( acadp.zoom_level ),
			center	    : new google.maps.LatLng( 0, 0 ),
			mapTypeId   : google.maps.MapTypeId.ROADMAP,
			zoomControl : true,
			scrollwheel : false
		};

		// create map	        	
		var map = new google.maps.Map( $el[0], args );

		// add a markers reference
		map.markers = [];
		
		// set map type
		map.type = $el.data('type');
	
		// add markers
		$markers.each(function() {
							   
			acadp_add_marker( $( this ), map );
			
		});

		// center map
		acadp_center_map( map );
		
		// update map when contact details fields are updated in the custom post type 'acadp_listings'
		if( map.type == 'form' ) {
			
			var geoCoder = new google.maps.Geocoder();		
			
			$( '.acadp-map-field', '#acadp-contact-details' ).on('blur', function() {

				var address = [];
				
				address.push( $( '#acadp-address' ).val() );
				
				var location = '';
				$( 'select', '#acadp-contact-details' ).each(function() {
					var _default  = $( this ).find( 'option:first' ).text();
					var _selected = $( this ).find( 'option:selected' ).text();
					if( _selected != _default ) location = _selected;
				});
				if( '' == location ) location = $( '#acadp-default-location' ).val();
				address.push( location );
								
				address.push( $( '#acadp-zipcode' ).val() );
				
				address = address.filter( function( v ) { return v !== '' } );
				address = address.join();
			
				geoCoder.geocode({'address': address}, function(results, status) {
															
   					if( status == google.maps.GeocoderStatus.OK ) {
						
						var point = results[0].geometry.location;									
						map.markers[0].setPosition( point );
						acadp_center_map( map );
						acadp_update_latlng( point );	
						
    				};
				
   				});

			});
				
			$( '#acadp-address' ).trigger( 'blur' );
			
		} else if( map.type == 'markerclusterer' ) {
			
			var markerCluster = new MarkerClusterer( map, map.markers, { imagePath: acadp.plugin_url+'public/images/m' } );
			
		};

	};	
	
	/**
	 *  Add a marker to the selected Google Map.
	 *
	 *  @since    1.0.0
	 */
	function acadp_add_marker( $marker, map ) {

		// var
		var latlng = new google.maps.LatLng( $marker.data( 'latitude' ), $marker.data( 'longitude' ) );

		// check to see if any of the existing markers match the latlng of the new marker
		if( map.markers.length ) {
    		for( var i = 0; i < map.markers.length; i++ ) {
        		var existing_marker = map.markers[i];
        		var pos = existing_marker.getPosition();

        		// if a marker already exists in the same position as this marker
        		if( latlng.equals( pos ) ) {
            		// update the position of the coincident marker by applying a small multipler to its coordinates
            		var latitude  = latlng.lat() + ( Math.random() - .5 ) / 1500; // * (Math.random() * (max - min) + min);
            		var longitude = latlng.lng() + ( Math.random() - .5 ) / 1500; // * (Math.random() * (max - min) + min);
            		latlng = new google.maps.LatLng( latitude, longitude );
        		}
    		}
		}
		
		// create marker
		var marker = new google.maps.Marker({
			position  : latlng,
			map		  : map,
			draggable : ( map.type == 'form' ) ? true : false
		});

		// add to array
		map.markers.push( marker );
	
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
		if( map.type == 'form' ) {
			google.maps.event.addListener(marker, "dragend", function() {
																  
  				var point = marker.getPosition();
				map.panTo(point);
				acadp_update_latlng(point);
			
			});	
		};

	};	

	/**
	 *  Center the map, showing all markers attached to this map.
     *
	 *  @since    1.0.0
	 */
	function acadp_center_map( map ) {

		// vars
		var bounds = new google.maps.LatLngBounds();

		// loop through all markers and create bounds
		$.each( map.markers, function( i, marker ){

			var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

			bounds.extend( latlng );

		});

		// only 1 marker?
		if( map.markers.length == 1 ) {
			
			// set center of map
	    	map.setCenter( bounds.getCenter() );
	    	map.setZoom( parseInt( acadp.zoom_level ) );
			
		} else {
			
			// fit to bounds
			map.fitBounds( bounds );
			
		};

	};
	
	/**
	 *  Set the latitude and longitude values from the address.
     *
	 *  @since    1.0.0
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
		
		if( $.fn.sortable ) {
						
			var $sortable_element = $('#acadp-images tbody');
			
			if( $sortable_element.hasClass('ui-sortable') ) {
				$sortable_element.sortable('destroy');
			};
			
			$sortable_element.sortable({
				handle: '.acadp-handle'
			});
					
		};
		
	}
	
	/**
	 *  Check if the user have permission to upload image.
     *
	 *  @since    1.0.0
	 *
	 *  @return   bool     True if can upload image, false if not.
	 */
	function acadp_can_upload_image() {
		
		var limit = acadp_images_limit();
		var uploaded = acadp_images_uploaded_count();	
		
		if( ( limit > 0 && uploaded >= limit ) || $( '#acadp-progress-image-upload' ).hasClass( 'uploading' ) ) {
			return false;
		}
		
		return true;
		
	}
	
	/**
	 *  Get the maximum number of images the user can upload in the current listing.
     *
	 *  @since     1.5.8
	 *
	 *  @return    int      Number of images.
	 */
	function acadp_images_limit() {
		
		var limit = $( '#acadp-upload-image' ).attr( 'data-limit' );

		if( typeof limit !== typeof undefined && limit !== false ) {
  			limit = parseInt( limit );
		} else {
			limit = parseInt( acadp.maximum_images_per_listing );
		}
		
		return limit;
		
	}
	
	/**
	 *  Get the number of images user had uploaded for the current listing.
     *
	 *  @since     1.5.8
	 *
	 *  @return    int      Number of images.
	 */
	function acadp_images_uploaded_count() {
		return $( '.acadp-image-field' ).length;		
	}
	
	/**
	 *  Enable or disable image upload
     *
	 *  @since    1.0.0
	 */
	function acadp_enable_disable_image_upload() {
		
		if( acadp_can_upload_image() ) {			
			$( '#acadp-upload-image' ).removeAttr( 'disabled' );			
		} else {			
			$( '#acadp-upload-image' ).attr( 'disabled', 'disabled' );			
		};
		
	}
	
	/**
	 * Called when the page has loaded.
	 *
	 * @since    1.0.0
	 */
	$(function() {
		
		// load custom fields of the selected category in the search form
		$( 'body' ).on( 'change', '.acadp-category-search', function() {
							
			var $search_elem = $( this ).closest ( 'form' ).find( ".acadp-custom-fields-search" );
			
			if( $search_elem.length ) {
				
				$search_elem.html( '<div class="acadp-spinner"></div>' );
				
				var data = {
					'action'  : 'acadp_custom_fields_search',
					'term_id' : $( this ).val(),
					'style'   : $search_elem.data( 'style' )
				};
				
				$.post( acadp.ajax_url, data, function(response) {
					$search_elem.html( response );
				});
			
			};
			
		});
		
		// add "required" attribute to the category field in the listing form [fallback for versions prior to 1.5.5]
		$( '#acadp_category' ).attr( 'required', 'required' );
		
		// load custom fields of the selected category in the custom post type "acadp_listings"
		$( 'body' ).on( 'change', '.acadp-category-listing', function() {
			
			$( '.acadp-listing-form-submit-btn' ).prop( 'disabled', true );
			$( '#acadp-custom-fields-listings' ).html( '<div class="acadp-spinner"></div>' );
			
			var data = {
				'action'  : 'acadp_public_custom_fields_listings',
				'post_id' : $( '#acadp-custom-fields-listings' ).data('post_id'),
				'term_id' : $(this).val()
			};
			
			$.post( acadp.ajax_url, data, function( response ) {
				$( '#acadp-custom-fields-listings' ).html( response );
				$( '.acadp-listing-form-submit-btn' ).prop( 'disabled', false );
			});
			
		});	
		
		// slick slider
		if( $.fn.slick ) {
			
			$( '.acadp-slider-for' ).on( 'init', function( slick ) {
            	$( this ).fadeIn( 1000 );
			}).slick({
				rtl            : ( parseInt( acadp.is_rtl ) ? true : false ),
  				asNavFor       : '.acadp-slider-nav',
				arrows         : false,
  				fade           : true,
				slidesToShow   : 1,
  				slidesToScroll : 1,
				adaptiveHeight : true
			});
		
			$( '.acadp-slider-nav' ).on( 'init', function( slick ) {
            	$( this ).fadeIn( 1000 );
			}).slick({
				rtl            : ( parseInt( acadp.is_rtl ) ? true : false ),
				asNavFor       : '.acadp-slider-for',
				nextArrow      : '<div class="acadp-slider-next"><span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span></div>',
				prevArrow      : '<div class="acadp-slider-prev"><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span></div>',
  				focusOnSelect  : true,
				slidesToShow   : 5,
  				slidesToScroll : 5,
				responsive: [
					{
					  breakpoint: 1024,
					  settings: {
						slidesToShow: 3,
						slidesToScroll: 3,
					  }
					},
					{
					  breakpoint: 600,
					  settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					  }
					}
				]
			});
		
		};
		
		// render map in the custom post type "acadp_listings"
		$( '.acadp-map' ).each(function() {
									  
			acadp_render_map( $( this ) );

		});
		
		// display the media uploader when "Upload Image" button clicked in the custom post type "acadp_listings"		
		$( '#acadp-upload-image' ).on( 'click', function( e ) {
 
            e.preventDefault();
			
			if( acadp_can_upload_image() ) {
				$( '#acadp-upload-image-hidden' ).trigger('click');
			};            
 
        });
		
		// upload image 
		$( "#acadp-upload-image-hidden" ).change( function() {
			
			var selected = $( this )[0].files.length;
			if( ! selected ) return false;			
			
			var limit = acadp_images_limit();
			var uploaded = acadp_images_uploaded_count();
			var remaining = limit - uploaded;
			if( limit > 0 && selected > remaining ) {
				alert( acadp.upload_limit_alert_message.replace( /%d/gi, remaining ) );
				return false;
			};
		
			$( '#acadp-progress-image-upload' ).addClass( 'uploading' ).html( '<div class="acadp-spinner"></div>' );
			acadp_enable_disable_image_upload();
						
			var options = {
				dataType : 'json',
				url      : acadp.ajax_url,
        		success  : function( json, statusText, xhr, $form ) {
					// do extra stuff after submit
					$( '#acadp-progress-image-upload' ).removeClass( 'uploading' ).html( '' );
					
					$.each( json, function( key, value ) {
							
						if( ! value['error'] ) {
							var html = '<tr class="acadp-image-row">' + 
								'<td class="acadp-handle"><span class="glyphicon glyphicon-th-large"></span></td>' +          	
								'<td class="acadp-image">' + 
									'<img src="' + value['url'] + '" />' + 
									'<input type="hidden" class="acadp-image-field" name="images[]" value="' + value['id'] + '" />' + 
								'</td>' + 
								'<td>' + 
									value['url'] + '<br />' + 
									'<a href="javascript:;" class="acadp-delete-image" data-attachment_id="' + value['id'] + '">' + acadp.delete_label + '</a>' + 
								'</td>' +                 
							'</tr>';					
							$( '#acadp-images' ).append( html );
						};
						
					});

					acadp_sort_images();
					acadp_enable_disable_image_upload();
				},
				error  : function( data ) {
					$( '#acadp-progress-image-upload' ).removeClass( 'uploading' ).html( '' );
					acadp_enable_disable_image_upload();
				}
    		}; 

    		// submit form using 'ajaxSubmit' 
    		$('#acadp-form-upload').ajaxSubmit(options);
										 
		});	

		// make the isting images sortable in the custom post type "acadp_listings"
		acadp_sort_images();
		
		// Delete the selected image when "Delete Permanently" button clicked in the custom post type "acadp_listings"	
		$( '#acadp-images' ).on( 'click', 'a.acadp-delete-image', function( e ) {
														 
            e.preventDefault();
								
			var $this = $( this );
			
			var data = {
				'action'        : 'acadp_public_delete_attachment_listings',
				'attachment_id' : $this.data('attachment_id')
			};
			
			$.post( acadp.ajax_url, data, function( response ) {
				$this.closest( 'tr' ).remove();
				acadp_enable_disable_image_upload();
			});
			
		});
		
		// Toggle password fields in user account form
		$( '#acadp-change-password', '#acadp-user-account' ).on( 'change', function() {
			
			var $checked = $( this ).is( ":checked" );
			
			if( $checked ) {
				$( '.acadp-password-fields', '#acadp-user-account' ).show().find( 'input[type="password"]' ).attr( "disabled", false );
				
			} else {
				$( '.acadp-password-fields', '#acadp-user-account' ).hide().find( 'input[type="password"]' ).attr( "disabled", "disabled" );
			};
			
		}).trigger( 'change' );
			
		// Validate ACADP forms
		if( $.fn.validator ) {			
			
			// Validate login, forgot password, password reset, user account forms
			var acadp_login_submitted = false;
			
			$( '#acadp-login-form, #acadp-forgot-password-form, #acadp-password-reset-form, #acadp-user-account' ).validator({
				disable : false
			}).on( 'submit', function( e ) {
				
				if( acadp_login_submitted ) return false;
				acadp_login_submitted = true;
					
				// Check for errors				
				if( e.isDefaultPrevented() ) {				 	
					acadp_login_submitted = false; // Re-enable the submit event
				};
			 
			});
			
			// Validate registration form
			var acadp_register_submitted = false;
			
			$( '#acadp-register-form' ).validator({
				disable : false
			}).on( 'submit', function( e ) {
				
				if( acadp_register_submitted ) return false;
				acadp_register_submitted = true;
					
				// Check for errors
				var error = 1;
				
				if( ! e.isDefaultPrevented() ) {
				 
				 	error = 0;
					
			 		if( acadp.recaptcha_registration > 0 ) {
				 
			 			var response = grecaptcha.getResponse( acadp.recaptchas['registration'] );
				
						if( 0 == response.length ) {
							$( '#acadp-registration-g-recaptcha-message' ).addClass('text-danger').html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['registration'] );
					
							error = 1;
						};
			
					};
			
				};
				
				if( error ) {					
					acadp_register_submitted = false; // Re-enable the submit event					
					return false;					
				};
			 
			});
			
			// Validate listing form
			var acadp_listing_submitted = false;
			
			$( '#acadp-post-form' ).validator({
				'custom'  : {
					cb_required : function( $el ) {
						var class_name = $el.data('cb_required');
						return $( "input."+class_name+":checked").length > 0 ? true : false;
					}
				},
				errors : {
      				cb_required : "You must select atleast one option."
   				},
				disable : false
			}).on( 'submit', function( e ) {
				
				if( acadp_listing_submitted ) return false;
				acadp_listing_submitted = true;
					
				// Check for errors
				var error = 1;
				
				if( ! e.isDefaultPrevented() ) {
					
					error = 0;
				 
			 		if( acadp.recaptcha_listing > 0 ) {
				 
			 			var response = grecaptcha.getResponse( acadp.recaptchas['listing'] );
				
						if( 0 == response.length ) {
							$( '#acadp-listing-g-recaptcha-message' ).addClass('text-danger').html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['listing'] );
					
							error = 1;
						};
			
					};
			
				};
				
				if( error ) {
					
					$( "#acadp-post-errors" ).show();
					
					$( 'html, body' ).animate({
        				scrollTop: $( "#acadp-post-form" ).offset().top - 50
    				}, 500 );
					
					acadp_listing_submitted = false; // Re-enable the submit event
					
					return false;
					
				} else {
					
					$( "#acadp-post-errors" ).hide();
					
				};
			 
			});
			
			// Validate report abuse form
			var acadp_report_abuse_submitted = false;
			
			$( '#acadp-report-abuse-form' ).validator({
				disable : false
			}).on( 'submit', function( e ) {
									
				if( acadp_report_abuse_submitted ) return false;
				acadp_report_abuse_submitted = true;
				
				// Check for errors
				if( ! e.isDefaultPrevented() ) {
			 
			 		e.preventDefault();
					
			 		var response = '';
					
			 		if( acadp.recaptcha_report_abuse > 0 ) {
				 
			 			response = grecaptcha.getResponse( acadp.recaptchas['report_abuse'] );
				
						if( 0 == response.length ) {
							$( '#acadp-report-abuse-message-display' ).addClass('text-danger').html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['report_abuse'] );
				
							acadp_report_abuse_submitted = false; // Re-enable the submit event							
							return false;
						};
			
					};
			 
			 		// Post via AJAX			 
			 		var data = {
						'action'  : 'acadp_public_report_abuse',
						'post_id' : $( '#acadp-post-id' ).val(),
						'message' : $( '#acadp-report-abuse-message' ).val(),
						'g-recaptcha-response' : response
					};
			
					$.post( acadp.ajax_url, data, function( response ) {
						if( 1 == response.error ) {
							$( '#acadp-report-abuse-message-display' ).addClass('text-danger').html( response.message );
						} else {
							$( '#acadp-report-abuse-message' ).val('');
							$( '#acadp-report-abuse-message-display' ).addClass('text-success').html( response.message );
						};
				
						if( acadp.recaptcha_report_abuse > 0 ) {
							grecaptcha.reset( acadp.recaptchas['report_abuse'] );
						};
						
						acadp_report_abuse_submitted = false; // Re-enable the submit event
					}, 'json' );
			
				};
																		  
			});
			
			// Validate contact form
			var acadp_contact_submitted = false;
			
			$( '#acadp-contact-form' ).validator({
				disable : false
			}).on( 'submit', function( e ) {
							
				if( acadp_contact_submitted ) return false;
				acadp_contact_submitted = true;
				
				// Check for errors
				if( ! e.isDefaultPrevented() ) {
			 
			 		e.preventDefault();
					
			 		var response = '';
					
			 		if( acadp.recaptcha_contact > 0 ) {
				 
			 			response = grecaptcha.getResponse( acadp.recaptchas['contact'] );
				
						if( 0 == response.length ) {
							$( '#acadp-contact-message-display' ).addClass('text-danger').html( acadp.recaptcha_invalid_message );
							grecaptcha.reset( acadp.recaptchas['contact'] );
				
							acadp_contact_submitted = false; // Re-enable the submit event						
							return false;
						};
				
					};
					
					$( '#acadp-contact-message-display' ).append('<div class="acadp-spinner"></div>');
					
			 		// Post via AJAX
			 		var data = {
						'action'  : 'acadp_public_send_contact_email',
						'post_id' : $( '#acadp-post-id' ).val(),
						'name'    : $( '#acadp-contact-name' ).val(),
						'email'   : $( '#acadp-contact-email' ).val(),
						'message' : $( '#acadp-contact-message' ).val(),
						'g-recaptcha-response' : response
					};
			
					$.post( acadp.ajax_url, data, function( response ) {
						if( 1 == response.error ) {
							$( '#acadp-contact-message-display' ).addClass('text-danger').html( response.message );
						} else {
							$( '#acadp-contact-message' ).val('');
							$( '#acadp-contact-message-display' ).addClass('text-success').html( response.message );
						};
				
						if( acadp.recaptcha_contact > 0 ) {
							grecaptcha.reset( acadp.recaptchas['contact'] );
						};
						
						acadp_contact_submitted = false; // Re-enable the submit event
					}, 'json' );
					
				};
				
			});
		
		};
		
		// Report abuse [on modal closed]
		$('#acadp-report-abuse-modal').on( 'hidden.bs.modal', function( e ) {
																	   
			$( '#acadp-report-abuse-message' ).val('');
			$( '#acadp-report-abuse-message-display' ).html('');
			
		});
		
		// Contact form [on modal closed]
		$('#acadp-contact-modal').on( 'hidden.bs.modal', function( e ) {
																  
			$( '#acadp-contact-message' ).val('');
			$( '#acadp-contact-message-display' ).html('');
			
		});
		
		// Add or Remove from favourites
		$( '#acadp-favourites' ).on( 'click', 'a.acadp-favourites', function( e ) {
													   
			 e.preventDefault();
			 
			 var $this = $( this );
			 
			 var data = {
				'action'  : 'acadp_public_add_remove_favorites',
				'post_id' : $this.data('post_id')
			};
			
			$.post( acadp.ajax_url, data, function( response ) {
				$( '#acadp-favourites' ).html( response );
			});
																		   
		});
		
		// Alert users to login (only if applicable)
		$( '.acadp-require-login' ).on( 'click', function( e ) {
														  
			 e.preventDefault();			 
			 alert( acadp.user_login_alert_message );
			 
		});
		
		// Calculate and update total amount in the checkout form
		$( '.acadp-checkout-fee-field' ).on( 'change', function() {
	
			var total_amount = 0,
			    fee_fields   = 0;
				
			$( "#acadp-checkout-form-data input[type='checkbox']:checked, #acadp-checkout-form-data input[type='radio']:checked" ).each(function() {
				total_amount += parseFloat( $( this ).data('price') );
				++fee_fields;
			});
			
			$( '#acadp-checkout-total-amount' ).html( '<div class="acadp-spinner"></div>' );
			
			if( 0 == fee_fields ) {
				$( '#acadp-checkout-total-amount' ).html( '0.00' );
				$( '#acadp-payment-gateways, #acadp-cc-form, #acadp-checkout-submit-btn' ).hide();
				return;
			};
			
			var data = {
				'action' : 'acadp_checkout_format_total_amount',
				'amount' : total_amount
			};
			
			$.post( acadp.ajax_url, data, function(response) {
												   
				$( '#acadp-checkout-total-amount' ).html( response );
				
				var amount = parseFloat( $( '#acadp-checkout-total-amount' ).html() );
				
				if( amount > 0 ) {
					$( '#acadp-payment-gateways, #acadp-cc-form' ).show();
					$( '#acadp-checkout-submit-btn' ).val( acadp.proceed_to_payment_btn_label ).show();
				} else {
					$( '#acadp-payment-gateways, #acadp-cc-form' ).hide();
					$( '#acadp-checkout-submit-btn' ).val( acadp.finish_submission_btn_label ).show();
				}
				
			});
			
		}).trigger('change');
		
		// Validate checkout form
		var acadp_checkout_submitted = false;
		
		$( '#acadp-checkout-form' ).on( 'submit', function() {
					
			if( acadp_checkout_submitted ) return false;
			acadp_checkout_submitted = true;
			
		});
		
		// Populate ACADP child terms dropdown
		$( '.acadp-terms' ).on( 'change', 'select', function( e ) {
								
			e.preventDefault();
			 
			var $this    = $( this );
			var taxonomy = $this.data( 'taxonomy' );
			var parent   = $this.data( 'parent' );
			var value    = $this.val();
			var classes  = $this.attr( 'class' );
			
			$this.closest( '.acadp-terms' ).find( 'input.acadp-term-hidden' ).val( value );
			$this.parent().find( 'div:first' ).remove();
			
			if( parent != value ) {
				$this.parent().append( '<div class="acadp-spinner"></div>' );
				
				var data = {
					'action'   : 'acadp_public_dropdown_terms',
					'taxonomy' : taxonomy,
					'parent'   : value,
					'class'    : classes
				};
				
				$.post( acadp.ajax_url, data, function( response ) {
					$this.parent().find( 'div:first' ).remove();
					$this.parent().append( response );
				});
			};
			
		});
	
	});

})( jQuery );



/**
 *  load reCAPTCHA explicitly.
 *
 *  @since    1.0.0
 */
var acadp_on_recaptcha_load = function() {

	if( '' != acadp.recaptcha_site_key ) {
		
		// Add reCAPTCHA in registration form
		if( jQuery( "#acadp-registration-g-recaptcha" ).length ) {
			
			if( acadp.recaptcha_registration > 0 ) {
				acadp.recaptchas['registration'] = grecaptcha.render( 'acadp-registration-g-recaptcha', {
    				'sitekey' : acadp.recaptcha_site_key
    			});
				
				jQuery( "#acadp-registration-g-recaptcha" ).addClass( 'acadp-margin-bottom' );
			};
			
		} else {
			
			acadp.recaptcha_registration = 0;
			
		};
		
		// Add reCAPTCHA in listing form
		if( jQuery( "#acadp-listing-g-recaptcha" ).length ) {
			
			if( acadp.recaptcha_listing > 0 ) {
				acadp.recaptchas['listing'] = grecaptcha.render( 'acadp-listing-g-recaptcha', {
    				'sitekey' : acadp.recaptcha_site_key
    			});
				
				jQuery( "#acadp-listing-g-recaptcha" ).addClass( 'acadp-margin-bottom' );
			};
			
		} else {
			
			acadp.recaptcha_listing = 0;
			
		};
		
		// Add reCAPTCHA in contact form
		if( jQuery( "#acadp-contact-g-recaptcha" ).length ) {
			
			if( acadp.recaptcha_contact > 0 ) {
				acadp.recaptchas['contact'] = grecaptcha.render( 'acadp-contact-g-recaptcha', {
    				'sitekey' : acadp.recaptcha_site_key
    			});
			};
		
		} else {
			
			acadp.recaptcha_contact = 0;
			
		};
		
		// Add reCAPTCHA in report abuse form
		if( jQuery( "#acadp-report-abuse-g-recaptcha" ).length ) {
			
			if( acadp.recaptcha_report_abuse > 0 ) {
				acadp.recaptchas['report_abuse'] = grecaptcha.render( 'acadp-report-abuse-g-recaptcha', {
    				'sitekey' : acadp.recaptcha_site_key
    			});
			};
		
		} else {
			
			acadp.recaptcha_report_abuse = 0;
			
		};
	
	};
	
};
