<?php
/**
 * Uninstall cleanup.
 *
 * @package PricingTableBlock
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'pricing-table-block_options' );
