<?php
abstract class ME_Input {
    protected $_name;
    protected $_type;
    protected $_label;
    protected $_description;
    protected $_container;
    protected $_value;

    protected $_options;
    abstract public function render();

    protected function label() {
        if (!empty($this->_label)) {
            echo '<label class="me-title">' . $this->_label . '</label>';
        }
    }

    protected function description() {
        if (!empty($this->_description)) {
            echo '<span class="me-subtitle">' . $this->_description . '</span>';
        }
    }

    protected function open_form() {
        if(!isset($this->_isform) || $this->_isform)
            echo '<form novalidate="novalidate">';
    }

    protected function close_form() {
        if(!isset($this->_isform) || $this->_isform)
            echo '</form>';
    }

    protected function get_id() {
        return $this->_slug ? 'id="'.$this->_slug.'"' : '';
    }

    protected function get_value() {
        if (!$this->_container || !$this->_options) {
            return '';
        }
        $parent      = $this->_options;
        $options     = $this->_options;
        $option_name = $this->_name;

        $option_value = ME_Options::get_instance()->get_option( $option_name);

        return !empty($option_value) ? $option_value : '';
        //return $options->$parent->$option_name;
    }
}