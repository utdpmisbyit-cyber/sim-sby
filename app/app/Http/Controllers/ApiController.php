<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function hasil_lab(Request $request)
    {
        Log::info("Hasil Lab", $request->input('records')[0]);

        return response()->json(['success' => true]);
    }
}
