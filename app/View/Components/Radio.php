<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Radio extends Component
{
    public $prefix, $name, $caption, $class, $classInput, $value, $checked;
    public function __construct(
        $prefix = '',
        $name = '',
        $caption = '',
        $class = '',
        $classInput = '',
        $value = '1',
        $checked = false
    )
    {
        $this->prefix = $prefix;
        $this->name = $name;
        $this->caption = $caption;
        $this->class = $class;
        $this->classInput = $classInput;
        $this->value = $value;
        $this->checked = $checked;
    }

    public function render()
    {
        return view('components.radio');
    }
}
