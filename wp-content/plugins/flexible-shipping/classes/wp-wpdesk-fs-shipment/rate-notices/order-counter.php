<?php

namespace WPDesk\FS\Rate;

/**
 * Counts orders with FS shipping method.
 */
class Flexible_Shipping_Order_Counter implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable {

    const ORDER_STATUS_COMPLETED = 'completed';
    const FS_ORDER_COUNTER       = 'flexible_shipping_rate_notice_counter';
    const FS_METHOD				 = 'flexible_shipping';

    /**
     * Hooks.
     */
    public function hooks() {
        add_action( 'woocommerce_order_status_changed', array( $this, 'count_order_for_fs_methods' ), 10, 4 );
    }

    /**
     * Count order.
     *
     * @param WC_Order $order .
     */
    private function count_order( $order ) {
        update_option( self::FS_ORDER_COUNTER, intval( get_option( self::FS_ORDER_COUNTER, '0' ) ) + 1 );
        $order->update_meta_data( self::FS_ORDER_COUNTER, 1 );
        $order->save();
    }

    /**
     * Count orders for FS methods.
     *
     * @param int      $order_id Order ID.
     * @param string   $status_from Status from.
     * @param string   $status_to Status to.
     * @param WC_Order $order Order.
     */
    public function count_order_for_fs_methods( $order_id, $status_from, $status_to, $order ) {
        if ( self::ORDER_STATUS_COMPLETED === $status_to ) {
            $shipping_methods = $order->get_shipping_methods();
            foreach ( $shipping_methods as $shipping_method ) {
                if ( self::FS_METHOD === $shipping_method->get_method_id() ) {
                    if ( '' === $order->get_meta( self::FS_ORDER_COUNTER ) ) {
                        $this->count_order( $order );
                    }
                }
            }
        }
    }

}
