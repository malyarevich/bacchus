<?php
/**
 * Admin View: Generator Editor
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

?>

<div id="message" class="error woocommerce-gzd-message wc-connect">
	
	<h3><?php _e( 'Dependencies missing, outdated or not yet tested', 'woocommerce-germanized-pro' );?></h3>

	<?php foreach ( $dependencies->plugins_result as $type => $plugins ) : ?>

		<?php if ( $type === 'unactivated' && ! empty( $plugins ) ) : ?>

			<p><?php _e( 'To use WooCommerce Germanized Pro you may at first install the following plugins:', 'woocommerce-germanized-pro' ); ?></p>

			<ul>
			
			<?php foreach ( $plugins as $plugin ) : ?>
				<li><a class="" href="<?php echo admin_url( "plugin-install.php?tab=search&s=" . urlencode( $plugin[ 'name' ] ) ); ?>"><?php echo $plugin[ 'name' ]; ?></a></li>
			<?php endforeach; ?>
			
			</ul>

		<?php elseif ( $type === 'outdated' && ! empty( $plugins ) ) : ?>

			<p><?php _e( 'To use WooCommerce Germanized Pro you may at first update the following plugins to a newer version:', 'woocommerce-germanized-pro' ); ?></p>

			<ul>
			
			<?php foreach ( $plugins as $plugin ) : ?>
				<li><?php printf( __( '%s required in at least version %s', 'woocommerce-germanized-pro' ), $plugin[ 'name' ], '<strong>' . $plugin[ 'requires' ] . '</strong>' ); ?></li>
			<?php endforeach; ?>
			
			</ul>

		<?php elseif ( $type === 'untested' && ! empty( $plugins ) ) : ?>

			<p><?php _e( 'Seems like you are using a not yet supported version of a Plugin which Germanized Pro requires. You may downgrade the Plugin or update to the latest version of Germanized Pro.', 'woocommerce-germanized-pro' ); ?></p>

			<ul>

			<?php foreach ( $plugins as $plugin => $plugin_data ) : ?>
				<li><?php printf( __( '%s %s is not yet supported - you may install an %s', 'woocommerce-germanized-pro' ), $plugin_data[ 'name' ], '<strong>' . $plugin_data[ 'version' ] . '</strong>', '<a href="https://wordpress.org/plugins/' . $plugin . '/developers/" target="_blank">' . __( 'older version', 'woocommerce-germanized-pro' ) . '</a>' ); ?></li>
			<?php endforeach; ?>
			
			</ul>

		<?php endif; ?>

	<?php endforeach; ?>

	<?php if ( ! empty( $dependencies->plugins_result[ 'outdated' ] ) || ! empty( $dependencies->plugins_result[ 'untested' ] ) ) : ?>

		<p><a class="button button-secondary" href="<?php echo admin_url( "update-core.php" ); ?>"><?php _e( 'Check for Updates', 'woocommerce-germanized-pro' ); ?></a></p>

	<?php endif; ?>

</div>