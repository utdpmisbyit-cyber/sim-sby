<?php

namespace App\View\Components;

use Illuminate\View\Component;

class IoTextarea extends Component
{
    public $name, $caption, $placeholder, $value, $class, $required, $type, $rows, $prefix, $viewtype;
    public function __construct($name = '', $caption = '', $placeholder = '', $value = '', $class = '', $required = '', $type = '', $rows = 4, $prefix = '', $viewtype = 1)
    {
        $this->name = $name;
        $this->caption = $caption;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->class = $class;
        $this->required = $required;
        $this->type = $type;
        $this->rows = $rows;
        $this->prefix = $prefix;
        $this->viewtype = $viewtype;
    }

    public function render()
    {
        return view('components.io.textarea');
    }
}
