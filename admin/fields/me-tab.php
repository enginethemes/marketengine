<?php
class ME_Tab extends ME_Container {
    protected $_controls;
    public function __construct($args) {
        $this->_name     = $args['slug'];
        $this->_template = $args['template'];
        $this->_id = 'id="em-setting-tab"';
        if( isset( $args['id'] ) && !empty($args['id']) )
            $this->_id = 'id="'. $args['id'] .'"';
    }

    public function menus() {
        if (count($this->_template) == 1) {
            return;
        }

        echo '<ul class="me-nav me-section-nav">';
        $class = 'class="active"';
        foreach ($this->_template as $key => $tab) {
            if ($tab['type'] == 'section') {
                echo '<li ' . $class . '><span>' . $tab['title'] . '</span></li>';
            }
            $class = '';
        }
        echo '</ul>';
    }

    public function start() {
        echo '<div class="me-tab" '. $this->_id .'>';
    }

    public function end() {
        echo '</div>';
    }

    public function wrapper_start() {
        echo '<div class="me-section-container">';
    }

    public function wrapper_end() {
        echo '</div>';
    }
}