<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ME_Order {
    public $id;
    public $order;
    public $total;
    public $shipping_info = array();
    
    /**
     * 
     */
    public function __construct() {

    }

    public function add_item($item){
    	$price = $item->get_price();
    	$quantity = 1;
    }

    public function add_note(){
    	
    }

    public function caculate_total() {

    }

    public function get_order_details() {

    }

    public function get_buyer() {

    }

    public function get_payment_info() {

    }
}