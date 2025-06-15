<?php

namespace App\Http\Controllers;

use App\Services\ProductPriceService;
use Illuminate\Http\Request;

class ApiProductController extends Controller
{
    public function search(Request $request, ProductPriceService $service)
    {
        $searchData = [
            'query' => $request->input('query', ''),
            'target_weight' => $request->input('target_weight', 1.0),
            'products' => [],
            'average_price' => null,
            'search_performed' => false
        ];

        if ($request->filled('query')) {
            $request->validate([
                'query' => 'required|string|max:100',
                'target_weight' => 'nullable|numeric|min:0.1|max:10'
            ]);

            $response = $service->searchProducts($request->input('query'));
            $avgPrice = $service->calculateAveragePrice(
                $response->items,
                $request->input('target_weight') ?? 1.0
            );
            $avgPricePerKg = $service->calculateAveragePricePerKG($response->items);

            $searchData = array_merge($searchData, [
                'products' => $service->prepareForView($response, $avgPrice)['products'],
                'average_price' => $avgPrice,
                'search_performed' => true
            ]);
        }

        return view('apiproducts.search', $searchData);
    }
}