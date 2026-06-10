<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FilePreview extends Component
{
    public $file, $name, $class, $imageClass;
    public function __construct($file,
                                $name = '',
                                $class = '',
                                $imageClass = '')
    {
        $this->file = $file;
        $this->name = $name;
        $this->class = $class;
        $this->imageClass = $imageClass;
    }

    public function render()
    {
        $temp = explode('.', $this->file);
        $file_type = end($temp);
        return view('components.file-preview', compact('file_type'));
    }
}
