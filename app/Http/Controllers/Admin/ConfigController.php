<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    //
    public function updateOrCreate(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|json'
        ]);

        $config = Config::updateOrCreate(
            ['key' => $request->key],
            ['value' => json_decode($request->value, true)]
        );

        return response()->json([
            'success' => true,
            'message' => 'Configuration updated successfully!',
            'config' => $config
        ]);
    }
}
