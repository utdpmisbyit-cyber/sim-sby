<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $class, $prefix, $name, $caption, $value, $options, $alert;
    public function __construct(
        $class = null,
        $prefix = null,
        $name = null,
        $caption = '',
        $value = '',
        $options = [],
        $alert = '1',
    )
    {
        $this->class = $class;
        $this->prefix = $prefix;
        $this->name = $name;
        $this->caption = $caption;
        $this->value = $value;
        $this->options = $options;
        $this->alert = $alert;
    }

    public function render()
    {
        return view('components.select');
    }
}
