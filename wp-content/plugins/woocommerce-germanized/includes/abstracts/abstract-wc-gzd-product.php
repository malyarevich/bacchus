<?php

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/**
 * WooCommerce Germanized Abstract Product
 *
 * The WC_GZD_Product Class is used to offer additional functionality for every product type.
 *
 * @class 		WC_GZD_Product
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_GZD_Product {

	/**
	 * The actual Product object (e.g. simple, variable)
	 * @var object
	 */
	private $child;

	protected $gzd_variation_level_meta = array(
		'unit_price' 		 		=> '',
		'unit_price_regular' 		=> '',
		'unit_price_sale' 	 		=> '',
		'unit_price_auto'	 	   	=> '',
		'mini_desc' 		 		=> '',
		'service'					=> '',
		'gzd_product' 		 		=> NULL,
	);

	protected $gzd_variation_inherited_meta_data = array(
		'unit',
		'unit_base',
		'unit_product',
		'sale_price_label',
		'sale_price_regular_label',
		'free_shipping',
	);

	/**
	 * Construct new WC_GZD_Product
	 *  
	 * @param WC_Product $product 
	 */
	public function __construct( $product ) {
		
		if ( is_numeric( $product ) )
			$product = WC()->product_factory->get_product_standalone( get_post( $product ) );
		
		$this->child = $product;
	}
 
	/**
	 * Redirects __get calls to WC_Product Class.
	 *  
	 * @param  string $key
	 * @return mixed     
	 */
	public function __get( $key ) {

		if ( $this->child->is_type( 'variation' ) && in_array( $key, array_keys( $this->gzd_variation_level_meta ) ) ) {
			
			$value = get_post_meta( $this->child->variation_id, '_' . $key, true );

			if ( '' === $value )
				$value = $this->gzd_variation_level_meta[ $key ];
		
		} else if ( $this->child->is_type( 'variation' ) && in_array( $key, $this->gzd_variation_inherited_meta_data ) ) {
			
			$value = metadata_exists( 'post', $this->child->variation_id, '_' . $key ) ? get_post_meta( $this->child->variation_id, '_' . $key, true ) : get_post_meta( $this->child->parent->id, '_' . $key, true );

			// Handle meta data keys which can be empty at variation level to cause inheritance
			if ( '' === $value ) {
				$value = get_post_meta( $this->child->parent->id, '_' . $key, true );
			}
		
		} else if ( $key == 'delivery_time' ) {
			
			$value = $this->get_delivery_time();
		
		} else {
			
			$value = $this->child->$key;
		
		}

		return $value;
	}

	/**
	 * Redirect issets to WC_Product Class
	 *  
	 * @param  string  $key 
	 * @return boolean      
	 */
	public function __isset( $key ) {

		if ( $this->child->is_type( 'variation' ) && in_array( $key, array_keys( $this->gzd_variation_level_meta ) ) ) {
			return metadata_exists( 'post', $this->child->variation_id, '_' . $key );
		} else if ( $this->child->is_type( 'variation' ) && in_array( $key, array_keys( $this->gzd_variation_inherited_meta_data ) ) ) {
			return metadata_exists( 'post', $this->child->parent->id, '_' . $key );
		} else {
			return isset( $this->child->$key );
		}

	}

	public function __call( $method, $args ) {
		if ( method_exists( $this->child, $method ) )
			return call_user_func_array( array( $this->child, $method ), $args );
		return false;
	}

	/**
	 * Get a product's cart description
	 * 
	 * @return boolean|string
	 */
	public function get_mini_desc() {
		if ( $this->mini_desc && ! empty( $this->mini_desc ) )
			return wpautop( htmlspecialchars_decode( $this->mini_desc ) );
		return false;
	}

	public function is_service() {
		if ( ! empty( $this->service ) && 'yes' === $this->service )
			return true;
		return false;
	}

	/**
	 * Checks whether current product applies for a virtual VAT exception (downloadable or virtual)
	 *  
	 * @return boolean
	 */
	public function is_virtual_vat_exception() {
		return apply_filters( 'woocommerce_gzd_product_virtual_vat_exception', ( ( get_option( 'woocommerce_gzd_enable_virtual_vat' ) === 'yes' ) && ( $this->is_downloadable() || $this->is_virtual() ) ? true : false ), $this );
	}

	public function get_price_html_from_to( $from, $to, $show_labels = true ) {

		$sale_label = ( $show_labels ? $this->get_sale_price_label() : '' );
		$sale_regular_label = ( $show_labels ? $this->get_sale_price_regular_label() : '' );

		$price = ( ! empty( $sale_label ) ? '<span class="wc-gzd-sale-price-label">' . $sale_label . '</span>' : '' ) . ' <del>' . ( ( is_numeric( $from ) ) ? wc_price( $from ) : $from ) . '</del> ' . ( ! empty( $sale_regular_label ) ? '<span class="wc-gzd-sale-price-label wc-gzd-sale-price-regular-label">' . $sale_regular_label . '</span> ' : '' ) . '<ins>' . ( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) . '</ins>';

		return apply_filters( 'woocommerce_germanized_get_price_html_from_to', $price, $from, $to, $this );
	}

	/**
	 * Gets a product's tax description (if is taxable)
	 *  
	 * @return mixed string if is taxable else returns false
	 */
	public function get_tax_info() {
		
		$_tax  = new WC_Tax();
		$tax_notice = false;
		
		if ( $this->is_taxable() ) {
		
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
			$tax_rates  = $_tax->get_rates( $this->get_tax_class() );
		
			if ( ! empty( $tax_rates ) ) {
		
				$tax_rates = array_values( $tax_rates );
				// If is variable or is virtual vat exception dont show exact tax rate
				if ( $this->is_virtual_vat_exception() || $this->is_type( 'variable' ) )
					$tax_notice = ( $tax_display_mode == 'incl' && ! WC()->customer->is_vat_exempt() ? __( 'incl. VAT', 'woocommerce-germanized' ) : __( 'excl. VAT', 'woocommerce-germanized' ) );
				else
					$tax_notice = ( $tax_display_mode == 'incl' && ! WC()->customer->is_vat_exempt() ? sprintf( __( 'incl. %s%% VAT', 'woocommerce-germanized' ), ( wc_gzd_format_tax_rate_percentage( $tax_rates[0][ 'rate' ] ) ) ) : sprintf( __( 'excl. %s%% VAT', 'woocommerce-germanized' ), ( wc_gzd_format_tax_rate_percentage( $tax_rates[0][ 'rate' ] ) ) ) );
			}

		} 
		
		return apply_filters( 'woocommerce_gzd_product_tax_info', $tax_notice, $this );
	}

	/**
	 * Checks whether current Product has a unit price
	 *  
	 * @return boolean
	 */
	public function has_unit() {
		if ( $this->unit && $this->unit_price_regular && $this->unit_base )
			return true;
		return false;
	}

	/**
	 * Returns unit base html
	 *  
	 * @return string
	 */
	public function get_unit_base() {
		return ( $this->unit_base ) ? ( $this->unit_base != apply_filters( 'woocommerce_gzd_unit_base_hide_amount', 1 ) ? '<span class="unit-base">' . $this->unit_base . '</span>' . apply_filters( 'wc_gzd_unit_price_base_seperator', ' ' ) : '' ) . '<span class="unit">' . $this->get_unit() . '</span>' : '';
	}

	/**
	 * Returns unit
	 *  
	 * @return string
	 */
	public function get_unit() {
		$unit = $this->unit;
		return WC_germanized()->units->$unit;
	}

	/**
	 * Returns sale price label
	 *  
	 * @return string
	 */
	public function get_sale_price_label() {

		$default = get_option( 'woocommerce_gzd_default_sale_price_label', '' );
		$label = ( ! empty( $this->sale_price_label ) ? $this->sale_price_label : $default );

		return ( ! empty( $label ) ? WC_germanized()->price_labels->$label : '' );
	}

	/**
	 * Returns sale price regular label
	 *  
	 * @return string
	 */
	public function get_sale_price_regular_label() {

		$default = get_option( 'woocommerce_gzd_default_sale_price_regular_label', '' );
		$label = ( ! empty( $this->sale_price_regular_label ) ? $this->sale_price_regular_label : $default );

		return ( ! empty( $label ) ? WC_germanized()->price_labels->$label : '' );
	}

	/**
	 * Returns unit regular price
	 *  
	 * @return string the regular price
	 */
	public function get_unit_regular_price() {
		return apply_filters( 'woocommerce_gzd_get_unit_regular_price', $this->unit_price_regular, $this );
	}

	/**
	 * Returns unit sale price
	 *  
	 * @return string the sale price 
	 */
	public function get_unit_sale_price() {
		return apply_filters( 'woocommerce_gzd_get_unit_sale_price', $this->unit_price_sale, $this );
	}

	/**
	 * Returns the unit price (if is sale then return sale price)
	 *  
	 * @param  integer $qty   
	 * @param  string  $price 
	 * @return string  formatted unit price
	 */
	public function get_unit_price( $qty = 1, $price = '' ) {
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		return ( $tax_display_mode == 'incl' ) ? $this->get_unit_price_including_tax( $qty, $price ) : $this->get_unit_price_excluding_tax( $qty, $price );
	}

	/**
	 * Returns unit price including tax
	 *  
	 * @param  integer $qty   
	 * @param  string  $price 
	 * @return string  unit price including tax
	 */
	public function get_unit_price_including_tax( $qty = 1, $price = '' ) {
		$price = ( $price == '' ) ? $this->unit_price : $price;
		return ( $price == '' ) ? '' : $this->get_price_including_tax( $qty, $price );
	}

	/**
	 * Returns unit price excluding tax
	 *  
	 * @param  integer $qty   
	 * @param  string  $price 
	 * @return string  unit price excluding tax
	 */
	public function get_unit_price_excluding_tax( $qty = 1, $price = '' ) {
		$price = ( $price == '' ) ? $this->unit_price : $price;
		return ( $price == '' ) ? '' : $this->get_price_excluding_tax( $qty, $price );
	}

	/**
	 * Checks whether unit price is on sale
	 *  
	 * @return boolean 
	 */
	public function is_on_unit_sale() {
		return apply_filters( 'woocommerce_gzd_product_is_on_unit_sale', ( $this->get_unit_sale_price() !== $this->get_unit_regular_price() && $this->get_unit_sale_price() == $this->get_unit_price() ), $this );
	}

	/**
	 * Returns unit price html output
	 *  
	 * @return string 
	 */
	public function get_unit_html( $show_sale = true ) {

		if ( apply_filters( 'woocommerce_gzd_hide_unit_text', false, $this ) )
			return apply_filters( 'woocommerce_germanized_disabled_unit_text', '', $this );
		
		$display_price         = $this->get_unit_price();
		$display_regular_price = $this->get_unit_price( 1, $this->get_unit_regular_price() );
		$display_sale_price    = $this->get_unit_price( 1, $this->get_unit_sale_price() );

		$price_html 		   = ( ( $this->is_on_unit_sale() && $show_sale ) ? $this->get_price_html_from_to( $display_regular_price, $display_sale_price, false ) : wc_price( $display_price ) );
		$html 				   = '';
		$text 				   = get_option( 'woocommerce_gzd_unit_price_text' );

		if ( $this->has_unit() ) {

			if ( strpos( $text, '{price}' ) !== false ) {

				$html = str_replace( '{price}', $price_html . apply_filters( 'wc_gzd_unit_price_seperator', ' / ' ) . $this->get_unit_base(), $text );

			} else {

				$html = str_replace( array( '{base_price}', '{unit}', '{base}' ), array( $price_html, '<span class="unit">' . $this->get_unit() . '</span>', ( $this->unit_base != apply_filters( 'woocommerce_gzd_unit_base_hide_amount', 1 ) ? '<span class="unit-base">' . $this->unit_base . '</span>' : '' ) ), $text );

			}

		}

		return apply_filters( 'woocommerce_gzd_unit_price_html', $html, $this );
	}

	public function has_product_units() {
		return ( $this->unit_product && ! empty( $this->unit_product ) && $this->get_unit() );
	}

	/**
	 * Formats the amount of product units
	 *  
	 * @return string 
	 */
	public function get_product_units_html() {

		if ( apply_filters( 'woocommerce_gzd_hide_product_units_text', false, $this ) )
			return apply_filters( 'woocommerce_germanized_disabled_product_units_text', '', $this );

		$html = '';
		$text = get_option( 'woocommerce_gzd_product_units_text' );

		if ( $this->has_product_units() )
			$html = str_replace( array( '{product_units}', '{unit}', '{unit_price}' ), array( str_replace( '.', ',', $this->unit_product ), $this->get_unit(), $this->get_unit_html() ), $text );

		return apply_filters( 'woocommerce_gzd_product_units_html', $html, $this );

	}

	/**
	 * Returns the current products delivery time term without falling back to default term
	 *  
	 * @return bool|object false returns false if term does not exist otherwise returns term object
	 */
	public function get_delivery_time() {
		
		$terms = get_the_terms( $this->id, 'product_delivery_time' );
		
		if ( $this->child->is_type( 'variation' ) ) {
			
			$variation_terms = get_the_terms( $this->child->variation_id , 'product_delivery_time' );
			
			if ( ! empty( $variation_terms ) && ! is_wp_error( $variation_terms ) )
				$terms = $variation_terms;
		}

		if ( is_wp_error( $terms ) || empty( $terms ) )
			return false;
		
		return $terms[ 0 ];
	}

	/**
	 * Returns current product's delivery time term. If none has been set and a default delivery time has been set, returns that instead.
	 *  
	 * @return object
	 */
	public function get_delivery_time_term() {
		
		$delivery_time = $this->delivery_time;

		if ( empty( $delivery_time ) && get_option( 'woocommerce_gzd_default_delivery_time' ) && ! $this->is_downloadable() ) {
			
			$delivery_time = array( get_term_by( 'id', get_option( 'woocommerce_gzd_default_delivery_time' ), 'product_delivery_time' ) );
			if ( is_array( $delivery_time ) ) {
				array_values( $delivery_time );
				$delivery_time = $delivery_time[0];
			}
			
		}
		return ( ! is_wp_error( $delivery_time ) && ! empty( $delivery_time ) ) ? $delivery_time : false;
	}

	/**
	 * Returns the delivery time html output
	 *  
	 * @return string 
	 */
	public function get_delivery_time_html() {

		$html = '';
		
		if ( apply_filters( 'woocommerce_germanized_hide_delivery_time_text', false, $this ) )
			return apply_filters( 'woocommerce_germanized_disabled_delivery_time_text', '', $this );

		if ( $this->get_delivery_time_term() ) {
			$html = $this->get_delivery_time_term()->name;
		} else {
			$html = apply_filters( 'woocommerce_germanized_empty_delivery_time_text', '', $this );
		}
		
		return ( ! empty( $html ) ? apply_filters( 'woocommerce_germanized_delivery_time_html', str_replace( '{delivery_time}', $html, get_option( 'woocommerce_gzd_delivery_time_text' ) ), $html ) : '' );
	}

	public function has_free_shipping() {
		return ( apply_filters( 'woocommerce_germanized_product_has_free_shipping', ( $this->free_shipping === 'yes' ? true : false ), $this ) );
	}

	/**
	 * Returns the shipping costs notice html output
	 *  
	 * @return string 
	 */
	public function get_shipping_costs_html() {
		
		if ( apply_filters( 'woocommerce_germanized_hide_shipping_costs_text', false, $this ) )
			return apply_filters( 'woocommerce_germanized_disabled_shipping_text', '', $this );
		
		$find = array(
			'{link}',
			'{/link}'
		);
		
		$replace = array(
			'<a href="' . esc_url( get_permalink( wc_get_page_id( 'shipping_costs' ) ) ) . '" target="_blank">',
			'</a>'
		);
		
		return apply_filters( 'woocommerce_gzd_shipping_costs_text', str_replace( $find, $replace, ( $this->has_free_shipping() ? get_option( 'woocommerce_gzd_free_shipping_text' ) : get_option( 'woocommerce_gzd_shipping_costs_text' ) ) ), $this );
	}

}
?>