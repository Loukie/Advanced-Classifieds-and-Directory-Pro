<?php

/**
 * This template displays the current user's payment history.
 */
?>

<div class="acadp acadp-user acadp-favourite-listings">    
	<div class="table-responsive">
		<table class="table table-striped">
    		<thead>
    			<tr>
        			<th><?php _e( 'Order ID', 'advanced-classifieds-and-directory-pro' ); ?></th>
            		<th><?php _e( 'Details', 'advanced-classifieds-and-directory-pro' ); ?></th>
                	<th><?php _e( 'Amount', 'advanced-classifieds-and-directory-pro' ); ?></th>
                	<th><?php _e( 'Type', 'advanced-classifieds-and-directory-pro' ); ?></th>
                	<th><?php _e( 'Transaction ID', 'advanced-classifieds-and-directory-pro' ); ?></th>
                	<th><?php _e( 'Date', 'advanced-classifieds-and-directory-pro' ); ?></th>
                	<th><?php _e( 'Status', 'advanced-classifieds-and-directory-pro' ); ?></th>
        		</tr>
			</thead>
        
        	<!-- the loop -->
        	<?php if( $acadp_query->have_posts() ) : ?>
				<?php while( $acadp_query->have_posts() ) : $acadp_query->the_post(); $post_meta = get_post_meta( $post->ID ); ?>
    				<tr>
        				<td><?php printf( '<a href="%s" target="_blank">%d</a>', acadp_get_payment_receipt_page_link( $post->ID ), $post->ID ); ?></td>
            			<td>
               				<?php
								$listing_id = $post_meta['listing_id'][0];
								printf( '<p><a href="%s">%s:%d</a></p>', get_permalink( $listing_id ), get_the_title( $listing_id ), $listing_id );

								$order_details = apply_filters( 'acadp_order_details', array(), $post->ID );
								foreach( $order_details as $order_detail ) {
									echo '<div># '.$order_detail['label'].'</div>';
								}
							
								if( isset( $post_meta['featured'] ) ) {
									$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
									echo '<div># '.$featured_listing_settings['label'].'</div>';
								}
							?>
            			</td>
                		<td>
                			<?php
								$amount = acadp_format_payment_amount( $post_meta['amount'][0] );					
								$value = acadp_payment_currency_filter( $amount );
								echo $value;
							?>
                		</td>
                    	<td>
                    		<?php
								$gateway = $post_meta['payment_gateway'][0];
								if( 'free' == $gateway ) {
									_e( 'Free Submission', 'advanced-classifieds-and-directory-pro' );
								} else {
									$gateway_settings = get_option( 'acadp_gateway_'.$gateway.'_settings' );				
									echo ! empty( $gateway_settings['label'] ) ? $gateway_settings['label'] : $gateway;
								}
							?>	
                    	</td>
                    	<td><?php if( isset( $post_meta['transaction_id'] ) ) echo $post_meta['transaction_id'][0]; ?></td>
                		<td>
                			<?php
								$date = strtotime( $post->post_date );
								echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $date );
							?>
                		</td>
                		<td><?php echo acadp_get_payment_status_i18n( $post_meta['payment_status'][0] ); ?></td>
        			</tr>
    			<?php endwhile; ?>
        	<?php endif; ?>
    	</table>
    </div>
    
    <!-- pagination here -->
    <?php the_acadp_pagination( $acadp_query->max_num_pages, "", $paged ); ?>
</div>