<?php

/**
 * Provide a admin area view for the Lightbox icon Picker
 *
 * This file is used to markup the admin-facing colorpicker
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/inludes
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

$icons = apply_filters( 'ctcore_iconpicker_icons', array() );
?>
<div id="colorpickerwrap" style="display:none">
	<div class="colorpickerwrap close">
		<header>
			<div class="cat-wrap">
				<select id="iconpicker-category" name="iconpicker-cat" class="widefat">
					<option value=""><?php esc_attr_e( 'All Categories', 'ctcore' ); ?></option>
					<?php foreach ( $icons as $cat_key => $cat_icons ) : ?>
					<option value="<?php echo esc_attr( $cat_key ) ?>">
					  <?php echo isset( $cat_icons['name'] ) ? esc_attr( $cat_icons['name'] ) : '' ?>
					</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="search-wrap" style="display:none">
				<input class="widefat" type="text" name="iconpicker-search" value="" placeholder="<?php esc_html_e('Search Icon...','ctcore'); ?>" readonly>
			</div>
		</header>
		<section id="iconlist">
		<?php foreach ( $icons as $i_key => $i_group ) : 
      if ( empty( $i_group['icon'] ) || !is_array( $i_group['icon'] ) ) continue; ?>
		  <div class="icon-cat-wrap <?php echo esc_attr( $i_key ) ?>" data-cat="<?php echo esc_attr( $i_key ) ?>">
		    <?php foreach ( $i_group['icon'] as $k_icon => $v_icon ) : ?>
		    <?php $icon_prefix = !empty( $i_group['prefix'] ) ? esc_attr( $i_group['prefix'] ) : ''; ?>
		    <div class="column"><i class="<?php echo esc_attr( $icon_prefix . $k_icon ) ?> icon"></i><?php echo esc_html( $v_icon ) ?></div>
		    <?php endforeach; ?>
      </div>
		<?php endforeach; ?>
		</section>
		<footer>
			<button type="button" class="button button-default"><?php esc_html_e('Cancel','ctcore'); ?></button>
			<button id="pick-icon" type="button" class="pick-icon button button-primary"><?php esc_html_e('Select','ctcore'); ?></button>
		</footer>
	</div>
</div>
