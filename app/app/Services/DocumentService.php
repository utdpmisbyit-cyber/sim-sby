<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService {

    public static function save_file(Request $request, $column_name = 'file', $folder = '', $name = '', $ext = '')
    {
        if ($request->hasFile($column_name)) {
            $file = $request->file($column_name);
            if ($folder === '') $folder = $column_name;
            if ($name === '') $name = Str::uuid();
            if ($ext === '') $filename = $name . '.'. $file->extension();
            else $filename = $name . '.'. $ext;
            return Storage::putFileAs($folder, $file, $filename);
        }
        return '';
    }

    public static function save_multiple_file(Request $request, $column_name = 'files', $folder = '')
    {
        $result = [];
        if ($request->hasFile($column_name)) {
            foreach ($request->file($column_name) as $file) {
                if ($folder === '') $folder = $column_name;
                $name = Str::uuid();
                $filename = $name . '.'. $file->extension();
                $result[] = Storage::putFileAs($folder, $file, $filename);
            }
        }
        return $result;
    }

    public static function delete_file($filename)
    {
        try {
            Storage::delete($filename);
        } catch (\Exception $e) {}
    }

    public static function file_size($filepath)
    {
        try {
            $bytes = Storage::size($filepath);
            $kilobytes = $bytes / 1024;
            return round($kilobytes, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

}
