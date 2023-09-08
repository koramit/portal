<?php

namespace App\Http\Controllers\API;

use App\APIs\ItemizeAPI;
use App\Http\Controllers\Controller;
use App\Traits\ServiceAccessLoggable;
use Illuminate\Http\Request;

class ItemizeController extends Controller
{
    use ServiceAccessLoggable;

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'in:drug,supply,department,doctor,title'],
            'search' => ['nullable', 'string'],
            'item_status' => ['nullable', 'in:ALL,Enable,Disable'],
        ]);

        $api = new ItemizeAPI();

        $data = $api->getItem(
            $validated['category'],
            $validated['search'] ?? '',
            $validated['item_status'] ?? 'ALL',
        );
        $this->log(
            $request->bearerToken(),
            $validated,
            $request->route()->getName(),
            $data['found'] ?? false,
        );

        return $data;
    }
}
