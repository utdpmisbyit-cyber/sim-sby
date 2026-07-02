<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $name, $prefix, $message;
    public function __construct($name, $prefix = '', $message = '')
    {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->message = $message;
    }

    public function render()
    {
        return view('components.alert');
    }
}
