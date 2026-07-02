<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Info extends Component
{
    public $infotype, $name, $caption, $prefix, $value, $options, $alert = '0', $class, $viewtype, $required = '', $placeholder = '', $type = '', $rows, $showlabel;
    public function __construct($infotype, $name, $caption, $prefix = null, $value = null, $options = null, $class = 'form-control-solid', $viewtype = 1, $showlabel = true, $rows = 3)
    {
        $this->infotype = $infotype;
        $this->name = $name;
        $this->caption = $caption;
        $this->prefix = $prefix;
        $this->value = $value;
        $this->options = $options;
        $this->viewtype = $viewtype;
        if ($infotype === 'date') $class .= ' datepicker';
        $this->class = $class;
        if ($infotype === 'file') $this->type = 'file';
        $this->showlabel = $showlabel;
        $this->rows = $rows;
    }

    public function render(): View|Closure|string
    {
        if ($this->showlabel === true) {
            return match ($this->infotype) {
                'date', 'text', 'file' => view('components.metronic-input'),
                'longtext' => view('components.metronic-textarea'),
                'select' => view('components.metronic-select'),
                default => view('components.empty'),
            };
        } else {
            return match ($this->infotype) {
                'date', 'text', 'file' => view('components.input'),
                'longtext' => view('components.textarea'),
                'select' => view('components.select'),
                default => view('components.empty'),
            };
        }
    }
}
