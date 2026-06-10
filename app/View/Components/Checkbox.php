<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Checkbox extends Component
{
    public $prefix, $name, $caption, $class, $classInput, $value, $checked, $classLabel;
    public function __construct(
        $prefix = '',
        $name = '',
        $caption = '',
        $class = '',
        $classInput = '',
        $value = '1',
        $checked = false,
        $classLabel = '',
    )
    {
        $this->prefix = $prefix;
        $this->name = $name;
        $this->caption = $caption;
        $this->class = $class;
        $this->classInput = $classInput;
        $this->value = $value;
        $this->checked = $checked;
        $this->classLabel = $classLabel;
    }

    public function render()
    {
        return view('components.checkbox');
    }
}
