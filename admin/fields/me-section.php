<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Class ME_Section
 * 
 * ME Html Section container of field group
 *
 * @since 1.0
 * @package Admin/Options
 * @category Class
 *
 * @version 1.0
 */
class ME_Section extends ME_Container {
    /**
     * Contructor
     * @param array $args
     */
    public function __construct($args) {
        $this->_name     = $args['slug'];
        $this->_template = $args['template'];
        $this->_first = $args['first'];
    }

    public function start() {
        if($this->_first) {
            echo '<div class="me-section-content" id="'.$this->_name.'">';
        }else {
            echo '<div class="me-section-content" id="'.$this->_name.'" style="display:none;">';
        }
    }

    public function end() {
        echo '</div>';
    }
}