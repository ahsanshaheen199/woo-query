<?php
/*
Plugin Name: WooCommerce Query
Description: This is sample woocommece query plugin
Author: Ahsan Habib Shaheen
Version: 1.0
*/


add_action('admin_menu',function(){
    add_menu_page( 'Woo Query', 'Woo Query', 'manage_options', 'woo-query', 'woo_query_page_html');
});

function woo_query_page_html() { ?>
    <div class="wrap">
        <h2>WooCommerce Query</h2>

        <?php
            $user_query = new WP_User_Query([
                'role' => 'customer'
            ]);

            if( !empty($user_query->get_results()) ) {
                foreach($user_query->get_results() as $singleUser) { ?>
                    <div style="padding: 15px;">
                        <h2> Customer Name: <?php echo $singleUser->display_name . ' ('. $singleUser->user_email.') '; ?></h2>

                        <?php 
                            $orders = wc_get_orders([
                                'customer'  => $singleUser->ID
                            ]);
                        ?>
                        <h4>Order Items</h4>
                        <table>
                            <tbody>
                                <tr>
                                    <th>Products</th>
                                    <th>Total Spent</th>
                                    <th>Payment Methods</th>
                                </tr>
                                <?php 
                                    if( !empty($orders) ) : 
                                        foreach( $orders as $singleOrder ):?>

                                        <tr>
                                            <td>
                                                <ul>
                                                    <?php 
                                                        foreach( $singleOrder->get_items() as $singleOrderItem ) { ?>
                                                            <li><?php echo  $singleOrderItem->get_name() ;?></li>
                                                        <?php }
                                                    ?>
                                                </ul>
                                            </td>
                                            <td><?php echo  $singleOrder->get_total();?></td>
                                            <td><?php echo  $singleOrder->get_payment_method_title();?></td>
                                        </tr>
                                    <?php
                                        endforeach;
                                    endif;
                                ?>
                                <tr></tr>
                            </tbody>
                        </table>
                    </div>
                <?php }
            }

        ?>

        <hr/>


        <?php 
            $products = wc_get_products([
                'posts_per_page'  => '-1'   
            ]);

            foreach( $products as $singleProduct ) { 
                global $wpdb;
                $result = $wpdb->get_row( "SELECT COUNT(product_id) AS total_sales,order_id, SUM(product_net_revenue) AS net_sales FROM {$wpdb->prefix}wc_order_product_lookup WHERE product_id = {$singleProduct->get_id()} GROUP BY product_id" );
                ?>
                <h2><?php echo $singleProduct->get_name(); ?></h2>

                <h4>Order Items</h4>

                <?php 
                    if( !empty( $result ) ) :
                        $order = wc_get_order($result->order_id);
                        $customer = get_user_by('ID',$order->get_customer_id());
                ?>
                <table>
                    <tbody>
                        <tr>
                            <th>Total Purchase</th>
                            <th>Total Revenue</th>
                            <th>Customer</th>
                        </tr>

                        <tr>
                            <td><?php echo $result->total_sales; ?></td>
                            <td><?php echo $result->net_sales; ?></td>
                            <td><?php echo $customer->user_nicename; ?></td>
                        </tr>
                    </tbody>
                </table>
                <?php 
                    endif;
            }
        ?>
    </div>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
<?php }