<?php

namespace App\View\Components;

use Illuminate\View\Component;

class IoSelect extends Component
{
    public $name, $caption, $placeholder, $value, $options, $viewtype, $required, $class, $prefix;
    public function __construct($name = '', $prefix = '', $caption = '', $placeholder = '', $value = '', $options = [], $required = '', $viewtype = 1, $class = '')
    {
        $this->name = $name;
        $this->caption = $caption;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->options = $options;
        $this->viewtype = $viewtype;
        $this->required = $required;
        $this->prefix = $prefix;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.io.select');
    }
}
