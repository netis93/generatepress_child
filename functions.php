<?php
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */


// add_filter( 'generate_typography_default_fonts', function( $fonts ) {
//     $fonts[] = 'TT Norms';

//     return $fonts;
// } );

// add_filter( 'upload_mimes', function( $mimes ) {
//     $mimes['woff']  = 'application/x-font-woff';
//     $mimes['woff2'] = 'application/x-font-woff2';
//     $mimes['ttf']   = 'application/x-font-ttf';
//     $mimes['svg']   = 'image/svg+xml';
//     $mimes['eot']   = 'application/vnd.ms-fontobject';

//     return $mimes;
// } );

// Use cloudflare CDN instead of maxcdn as maxcdn is intermittently blocked by Malaysia MCMC
function uniiq_instagram_admin_style() {
	wp_dequeue_style('sb_instagram_font_awesome');
	wp_deregister_style('sb_instagram_font_awesome');
	wp_enqueue_style( 'sb_instagram_font_awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css' );
}
add_action( 'admin_enqueue_scripts', 'uniiq_instagram_admin_style', 11 );

/*enable gutenberg for single product pagge*/
// Edit Woo Product with Gutenberg
function db_enable_gutenberg_single_product($editable, $post_type) {
	if($post_type == 'product'){
		$editable = true;
	}
	
	return $editable;
}
add_filter('use_block_editor_for_post_type', 'db_enable_gutenberg_single_product', 10, 2);
/*end of enable gutenberg for single product page*/



// // remove additional information tabs
remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs');

//Remove related products output
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

//add custom description 
add_action( 'woocommerce_single_product_summary', 'custom_description_output', 10 );
  
function custom_description_output() {
?>
   <div class="woocommerce-tabs">
   <?php the_content(); ?>
<?php
}

//code to redirect for custom woo page for shop
add_action('template_redirect', 'redirect_woo_shop_custom_page');function redirect_woo_shop_custom_page()
{
    if (function_exists('is_shop') && is_shop()) {
        wp_redirect('https://uniiqkombucha.com/shop/');
        exit;
    }
}

//code to change add to cart to learn more
add_filter( 'woocommerce_loop_add_to_cart_link', 'replacing_add_to_cart_button', 10, 2 );
function replacing_add_to_cart_button( $button, $product  ) {
    $button_text = __("Shop More", "woocommerce");
    $button = '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';

    return $button;
}

// function wooc_add_phone_number_field() {
//     return apply_filters( 'woocommerce_forms_field', array(
//         'wooc_user_phone' => array(
//             'type'        => 'text',
// 			'required'    => true,
//             'label'       => __( '', ' woocommerce' ),
//             'placeholder' => __( 'Phone number', 'woocommerce' ),

//         ),
//     ) );
// }
// add_action( 'woocommerce_register_form', 'wooc_add_field_to_registeration_form', 15 );
// function wooc_add_field_to_registeration_form() {
//     $fields = wooc_add_phone_number_field();
//     foreach ( $fields as $key => $field_args ) {
//         woocommerce_form_field( $key, $field_args );
//     } }

// add_action( 'woocommerce_created_customer', 'wooc_save_extra_register_fields' );
// function wooc_save_extra_register_fields( $customer_id ) {
//     if (isset($_POST['wooc_user_phone'])){
//         update_user_meta( $customer_id, 'wooc_user_phone', sanitize_text_field( $_POST['wooc_user_phone'] ) );
//     }
// }





add_shortcode( 'wc_reg_form_custom', 'separate_registration_form' );
function separate_registration_form() {
if ( is_admin() ) return;
if ( is_user_logged_in() ) return;
 
ob_start();
 
// NOTE: THE FOLLOWING <FORM></FORM> IS COPIED FROM woocommerce\templates\myaccount\form-login.php
// IF WOOCOMMERCE RELEASES AN UPDATE TO THAT TEMPLATE, YOU MUST CHANGE THIS ACCORDINGLY
do_action( 'woocommerce_before_customer_login_form' );
?>
<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >
<?php do_action( 'woocommerce_register_form_start' ); ?>
<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
</p>
<?php endif; ?>

<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
<label for="reg_email"><?php esc_html_e( '', 'woocommerce' ); ?></label>
<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Email Address" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
</p>
				 
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label for="reg_phone"><?php _e( 'Phone Number', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="tel" class="input-text regular-text billing_phone" name="billing_phone" id="billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>
	
<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
<label for="reg_password"><?php esc_html_e( '', 'woocommerce' ); ?></label>
<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Password" name="password" id="reg_password" autocomplete="new-password" />
</p>
<?php else : ?>
<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>
<?php endif; ?>
<?php do_action( 'woocommerce_register_form' ); ?>
<p class="woocommerce-FormRow form-row">
<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
</p>
<?php do_action( 'woocommerce_register_form_end' );


?>
</form>

<?php
 
return ob_get_clean();
}

///Custom Registration Field for phone number
///////////////////////////////
// 1. ADD FIELDS
add_action( 'woocommerce_register_form', 'wc_add_phone_woo_account_registration');
 
function wc_add_phone_woo_account_registration() {
    ?>
 
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label for="reg_phone"><?php _e( '', 'woocommerce' ); ?></label>
    <input type="tel" class="input-text regular-text billing_phone" name="billing_phone" placeholder="Phone Number" id="billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>
 
    <div class="clear"></div>
 
    <?php
}
 
 
///////////////////////////////
// 2. VALIDATE FIELDS
add_filter( 'woocommerce_registration_errors', 'wc_validate_name_fields', 10, 3 );
 
function wc_validate_name_fields( $errors, $username, $email ) {
    if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
        $errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Mobile Number is required!.', 'woocommerce' ) );
    }
    
    if ( isset( $_POST['billing_phone'] ) ) {
        $hasPhoneNumber= get_users('meta_value='.$_POST['billing_phone']);
            if ( !empty($hasPhoneNumber)) {
        $errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Mobile number is already used!.', 'woocommerce' ) );
    }
  }
    return $errors;
}
 
///////////////////////////////
// 3. SAVE FIELDS
 
add_action( 'woocommerce_created_customer', 'wc_save_name_fields' );
 
function wc_save_name_fields( $customer_id ) {
    if ( isset( $_POST['billing_phone'] ) ) {
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']) );
    }
 
}

// add_shortcode( 'wc_login_form_custom', 'separate_login_form' );
// function separate_login_form() {
// if ( is_admin() ) return;
// if ( is_user_logged_in() ) return;
// ob_start();
// woocommerce_login_form( array( 'redirect' => 'https://custom.url' ) );
// return ob_get_clean();
// }


function wc_login_form_function() {
   if ( is_admin() ) return;
   if ( is_user_logged_in() ) return; 
   ob_start();
?>


		<form class="woocommerce-form woocommerce-form-login login" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="username"><?php esc_html_e( '', 'woocommerce' ); ?>&nbsp;</label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Email ID" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</p>
 
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="password"><?php esc_html_e( '', 'woocommerce' ); ?>&nbsp;</label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" placeholder="Password" type="password" name="password" id="password" autocomplete="current-password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="form-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
				</label>
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forget password?', 'woocommerce' ); ?></a>

			</p>


				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>	
			<a href="/register">Sign Up</a>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php
   return ob_get_clean();
}
add_shortcode( 'wc_login_form_custom', 'wc_login_form_function' );

add_action('wp_logout','my_redirect_after_logout');
function my_redirect_after_logout(){
         wp_redirect( 'https://uniiqkombucha.com/' );
         exit();
}

// add_action('wp_login','my_redirect_after_login');
// function my_redirect_after_login(){
//          wp_redirect( 'https://uniiqkombucha.com/my-account' );
//          exit();
// }



//start of custom code for single product quote addon
  // Display custom field on single product page

// 	function d_extra_product_field(){
		
//       $value = isset( $_POST['extra_product_field'] ) ? sanitize_text_field( $_POST['extra_product_field'] ) : '';
// //         printf( '<label>%s</label><input name="extra_product_field" value="%s" />', __( 'Enter Your Quote For The Bottles' ), esc_attr( $value ) );
		 
// 		echo '<br><div>';
//     woocommerce_form_field('extra_product_field', array(
//         'type' => 'textarea',
//         'class' => array( 'my-field-class form-row-wide custom-addon-quote') ,
//         'label' => __('Enter Your Custom Quotes For The Bottles') ,
//         'placeholder' => __('Add your quotes here, please…') ,
//         'required' => True,
//     ) , esc_attr( $value ));
//     echo '</div>';

// 	}
    add_action( 'woocommerce_before_add_to_cart_button', 'd_extra_product_field', 9 );
	
	


//     // validate when add to cart
//     function d_extra_field_validation($passed, $product_id, $qty){

//         if( isset( $_POST['extra_product_field'] ) && sanitize_text_field( $_POST['extra_product_field'] ) == '' ){
//             $product = wc_get_product( $product_id );
//             wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some text.' ), $product->get_title() ), 'error' );
//             return false;
//         }

//         return $passed;

//     }
//     add_filter( 'woocommerce_add_to_cart_validation', 'd_extra_field_validation', 10, 3 );

//      // add custom field data in to cart
//     function d_add_cart_item_data( $cart_item, $product_id ){

//         if( isset( $_POST['extra_product_field'] ) ) {
//             $cart_item['extra_product_field'] = sanitize_text_field( $_POST['extra_product_field'] );
//         }

//         return $cart_item;

//     }
//     add_filter( 'woocommerce_add_cart_item_data', 'd_add_cart_item_data', 10, 2 );

//     // load data from session
//     function d_get_cart_data_f_session( $cart_item, $values ) {

//         if ( isset( $values['extra_product_field'] ) ){
//             $cart_item['extra_product_field'] = $values['extra_product_field'];
//         }

//         return $cart_item;

//     }
//     add_filter( 'woocommerce_get_cart_item_from_session', 'd_get_cart_data_f_session', 20, 2 );


//     //add meta to order
//     function d_add_order_meta( $item_id, $values ) {

//         if ( ! empty( $values['extra_product_field'] ) ) {
//             woocommerce_add_order_item_meta( $item_id, 'Your Quotes here', $values['extra_product_field'] );           
//         }
//     }
//     add_action( 'woocommerce_add_order_item_meta', 'd_add_order_meta', 10, 2 );

//     // display data in cart
//     function d_get_itemdata( $other_data, $cart_item ) {

//         if ( isset( $cart_item['extra_product_field'] ) ){

//             $other_data[] = array(
//                 'name' => __( 'Your Quotes Here' ),
//                 'value' => sanitize_text_field( $cart_item['extra_product_field'] )
//             );

//         }

//         return $other_data;

//     }
//     add_filter( 'woocommerce_get_item_data', 'd_get_itemdata', 10, 2 );


//     // display custom field data in order view
//     function d_dis_metadata_order( $cart_item, $order_item ){

//         if( isset( $order_item['extra_product_field'] ) ){
//             $cart_item_meta['extra_product_field'] = $order_item['extra_product_field'];
//         }

//         return $cart_item;

//     }
//     add_filter( 'woocommerce_order_item_product', 'd_dis_metadata_order', 10, 2 );


//     // add field data in email
//     function d_order_email_data( $fields ) { 
//         $fields['extra_product_field'] = __( 'Your Quotes Here' ); 
//         return $fields; 
//     } 
//     add_filter('woocommerce_email_order_meta_fields', 'd_order_email_data');

//     // again order
//     function d_order_again_meta_data( $cart_item, $order_item, $order ){

//         if( isset( $order_item['extra_product_field'] ) ){
//             $cart_item_meta['extra_product_field'] = $order_item['extra_product_field'];
//         }

//         return $cart_item;

//     }
//     add_filter( 'woocommerce_order_again_cart_item_data', 'd_order_again_meta_data', 10, 3 );

//       

add_action( 'template_redirect', 'redirect_if_user_not_logged_in' );

function redirect_if_user_not_logged_in() {

	if ( is_page(2825) && ! is_user_logged_in() ) { //example can be is_page(23) where 23 is page ID

		wp_redirect( 'https://uniiqkombucha.com/my-account/'); 
 
     exit();// never forget this exit since its very important for the wp_redirect() to have the exit / die 
   }  
}

// add_filter( 'generate_page_header_video_muted', 'enable_audio_header' );
// function enable_audio_header()
// { 
//     return false;
// }

function start_session() {
    if(!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session', 1);

// get  referer url and save it 
function redirect_url() {
    if (! is_user_logged_in()) {
        $_SESSION['referer_url'] = wp_get_referer();
    } else {
        session_destroy();
    }
}
add_action( 'template_redirect', 'redirect_url' );


//login redirect 
function login_redirect() {
    if (isset($_SESSION['referer_url'])) {
        wp_redirect($_SESSION['referer_url']);
    } else {
        wp_redirect(home_url());
    }
}
add_filter('woocommerce_login_redirect', 'login_redirect', 1100, 2);




