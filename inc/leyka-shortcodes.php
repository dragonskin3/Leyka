<? if( !defined('WPINC') ) die;
/**
 * Leyka template shortcodes
 * 
 **/


/**
 * Scale shortcode
 **/

add_shortcode('leyka_scale', 'leyka_scale_screen' );
function leyka_scale_screen($atts) {
	global $post;
	
    $a = shortcode_atts( array(
        'id'            => 0,
        'show_button'   => 0,
		'button_target' => 'form'
    ), $atts );

    $campaign = ($a['id'] > 0) ? get_post($a['id']) : $post;
	
	if($campaign->post_type != 'leyka_campaign')
		return ''; //wrong campaign data
	
	return leyka_get_scale($campaign, $a);
}

function leyka_get_scale($campaign = null, $args = array()){
	global $post;
	
	$defaults = array(
		'show_button'   => 0,
		'button_target' => 'form'
	);
	
	$args = wp_parse_args($args, $defaults);
	
	if(!$campaign){
		$campaign = $post;
	}
	elseif(is_int($campaign)){
		$campaign = get_post($campaign);
	}
	
	if($campaign->post_type != 'leyka_campaign')
		return ''; //wrong campaign data
	
	$css = 'leyka-scale';
	if($args['show_button'] == 1)
		$css .= ' has-button';
	
	ob_start();
	
	$url = ($args['button_target'] == 'page') ? get_permalink($campaign) : '#leyka-payment-form';
?>
	<div class="<?php echo esc_attr($css);?>">
		<?php leyka_scale_compact($campaign);?>
	<?php if($args['show_button'] == 1) :?>
		<div class="scale-button">
			<a href='<?php echo $url;?>'><?php echo leyka_get_scale_button_label();?></a>
		</div>
	<?php endif;?>
	</div>
<?php
	$out = ob_get_clean();
	
	return apply_filters('leyka_scale_html', $out, $campaign, $args);
}

function leyka_get_scale_button_label(){
	
	return apply_filters('leyka_scale_button_label', _x('Support', '«Support» label at scale button', 'leyka'));
}


/**
 * Campaign vcard shortcode
 **/

add_shortcode('leyka_campaign_card', 'leyka_campaign_card_screen' );
function leyka_campaign_card_screen($atts) {
	global $post;
	
    $a = shortcode_atts( array(
        'id'            => 0,
        'show_title'    => 1,
		'show_thumb'    => 1,
		'show_excerpt'  => 1,
		'show_scale'    => 1,
		'show_button'   => 1,
		'button_target' => 'form'
    ), $atts );

    $campaign = ($a['id'] > 0) ? get_post($a['id']) : $post;
	
	if($campaign->post_type != 'leyka_campaign')
		return ''; //wrong campaign data
	
	return leyka_get_campaign_card($campaign, $a);
}

function leyka_get_campaign_card($campaign = null, $args = array()) {
	global $post;
	
	$defaults = array(
		'show_title'    => 1,
		'show_thumb'    => 1,
		'show_excerpt'  => 1,
		'show_scale'    => 1,
		'show_button'   => 1,
		'button_target' => 'form'
	);
	
	$args = wp_parse_args($args, $defaults);
	
	if(!$campaign){
		$campaign = $post;
	}
	elseif(is_int($campaign)){
		$campaign = get_post($campaign);
	}
	
	if($campaign->post_type != 'leyka_campaign')
		return ''; //wrong campaign data
	
	
	ob_start();
?>
	<div class="leyka-campaign-card">
		<?php if($args['show_thumb'] == 1 && has_post_thumbnail($campaign->ID)):?>
			<div class="lk-thumbnail">
				<a href="<?php echo get_permalink($campaign);?>">
					<?php echo get_the_post_thumbnail($campaign->ID);?>
				</a>
			</div>
		<?php endif;?>
		
		<?php if($args['show_title'] == 1 || $args['show_excerpt'] == 1 ):?>
			<div class="lk-info">
				<?php if($args['show_title'] == 1) :?>
					<h4><a href="<?php echo get_permalink($campaign);?>">
						<?php echo get_the_title($campaign);?>
					</a></h4>
				<?php endif;?>
				
				<?php if($args['show_excerpt'] == 1 && has_excerpt($campaign->ID)) :?>
					<p><?php echo apply_filters('get_the_excerpt', $campaign->post_excerpt);?></p>
				<?php endif;?>
			</div>
		<?php endif;?>
		
		<?php if($args['show_scale'] == 1): ?>
			
			<?php
				echo leyka_get_scale($campaign,
						array('show_button' => $args['show_button'],
							  'button_target' => $args['button_target']
							  )
						);
			?>
			
		<?php
			elseif($args['show_button'] == 1) :
			$url = ($args['button_target'] == 'page') ? get_permalink($campaign) : '#leyka-payment-form'; 
		?>
			<div class="scale-button">
				<a href='<?php echo $url;?>'><?php echo leyka_get_scale_button_label();?></a>
			</div>
			
		<?php endif;?>
	</div>
<?php
	$out = ob_get_clean();
	return apply_filters('leyka_campaign_card_html', $out, $campaign, $args);
}


/**
 * Payment form shortcode 
 **/
add_shortcode('leyka_payment_form', 'leyka_payment_form_screen' );
function leyka_payment_form_screen($atts) {
	global $post;
	
    $a = shortcode_atts( array(
        'id'          => 0,
        'template'    => null,		
    ), $atts );

    $campaign = ($a['id'] > 0) ? get_post($a['id']) : $post;
	
	if($campaign->post_type != 'leyka_campaign')
		return ''; //wrong campaign data
	
	return leyka_get_payment_form($campaign, $a);
}

function leyka_get_payment_form($campaign = null, $args = array()) {
	global $post;
		
	$defaults = array(
		'template'  => null //ex. radios / toggles
	);
	
	$args = wp_parse_args($args, $defaults);
	
	if(!$campaign){
		$campaign = $post;
	}
	elseif(is_int($campaign)){
		$campaign = get_post($campaign);
	}
	
	if($campaign->post_type != 'leyka_campaign')
		return ''; //wrong campaign data
	
	return get_leyka_payment_form_template_html($campaign, $args['template']);
}



/**
 * Donation tickers shortcode
 **/

add_shortcode('leyka_donors_list', 'leyka_donors_list_screen' );
function leyka_donors_list_screen($atts) {		
	
    $a = shortcode_atts( array(
        'id'           => 'all', //could be also 0 (obtained from context) or real ID
        'num'          => leyka_get_donors_list_per_page(),
		'show_purpose' => 1,
		'show_name'    => 1,
		'show_date'    => 1,
    ), $atts );
    
	
	return leyka_get_donors_list($a['id'], $a);
}

function leyka_get_donors_list_per_page(){
	
	return apply_filters('leyka_donors_list_per_page', 25);
}


function leyka_get_donors_list($campaign_id = 'all', $args = array()) {
	global $post;
	
	$defaults = array(
		'num'          => leyka_get_donors_list_per_page(),
		'show_purpose' => 1,
		'show_name'    => 1,
		'show_date'    => 1,
	);
	
	$args = wp_parse_args($args, $defaults);
	
	if($campaign_id == 0){
		$campaign_id = $post->ID;
	}
	
	//get donations: funded amount > 0  
	$d_args = array(
		'post_type' => 'leyka_donation',
		'post_status' => 'funded',
		'posts_per_page' => $args['num'],
		'meta_query' => array(
			array(
				'key'     => 'leyka_donation_amount',
				'value'   => 0,
				'compare' => '>',
				'type'    => 'NUMERIC'
			)
		)
	);
	
	if($campaign_id != 'all'){
		$d_args['meta_query']['relation'] = 'AND';
		$d_args['meta_query'][] = array(
			'key'   => 'leyka_campaign_id',
			'value' => $campaign_id
		);		
	}
		
	$query = new WP_Query($d_args);
	if(!$query->have_posts())
		return '';
	

	ob_start();
?>
	<div class="leyka-donors-list">
	<?php
		foreach($query->posts as $qp):
			$donation = new Leyka_Donation($qp);			
			
			$amount = number_format($donation->sum, 0, '.', ' ');
			
			$html = "<div class='ldl-item'>";	
			$html .= "<div class='amount'>{$amount} {$donation->currency_label}</div>";
			
			if($args['show_purpose'] == 1) {
				$html .= "<div class='purpose'>".$donation->campaign_payment_title."</div>"; // correct property?
			}
		
		
			$meta = array();
			if($args['show_name'] == 1){				
				$name = $donation->donor_name;
				$name = (!empty($name)) ? $name : __('Anonymous', 'leyka');				
				$meta[] = '<span>'.$name.'</span>';
			}
			
			if($args['show_date'] == 1){
				$meta[] = '<time>'.$donation->date_funded.'</time>'; //correct property?
			}
		
			if(!empty($meta)) {			
				$html .= "<div class='meta'>".implode(' / ', $meta)."</div>"; 
			}
		
			$html .= "</div>";
			
			echo apply_filters('leyka_donors_list_item_html', $html, $campaign_id, $args);
		
		endforeach;
	?>
	</div>
<?php
	$out = ob_get_clean();
	return $out;
}
 