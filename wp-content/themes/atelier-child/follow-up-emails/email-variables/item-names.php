<?php
/**
 * Template file for the email variable "{item_names}".
 *
 * To edit this template, copy this file over to your wp-content/[current_theme]/follow-up-emails/email-variables
 * then edit the new file. A single variable named $lists is passed along to this template.
 *
 * $lists = array('items' => array(
 *      array(
 *          id:     Product ID
 *          sku:    Product's SKU
 *          link:   Absolute URL to the product
 *          name:   Product's name
 *          price:  Price of the product - unformatted
 *          qty:    Quantity bought
 *          categories: Array of product categories
 *      )
 * ))
 */
?>
<ul>
<?php foreach ( $lists['items'] as $item ): ?>
<!--    <li><a href="--><?php //echo $item['link']; ?><!--">--><?php //echo wp_kses_post( $item['name'] ); ?><!--</a></li>-->
        <li><a href="<?php echo $item['link']; ?>/#tab-reviews"><?php echo wp_kses_post( $item['name'] ); ?></a></li>
<?php endforeach; ?>
</ul>