<?php

namespace App\Http\Controllers;
use App\Models\ProductType;
use App\Models\Products;
use App\Models\ProductTypesCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
/**
 * Контроллер для работы с продуктами и их категориями
 */
class ProductsController extends Controller
{
    public function showProductType(ProductType $productType)
    {
        Log::info("Выгрузка каталога типов продуктов");
        return view('products.index',[
            'productType' => $productType,
            'categories'  => $productType->categories,
            'products'    => $productType->getFilteredProductsByCategory(request('category')),
            'productTypes' => ProductType::getAllTypes(),
        ]);
    }
     /**
     * Отображение детальной страницы продукта
     *
     * @param ProductType $productType Модель типа продукта (неявное связывание)
     * @param Products $product Модель продукта (неявное связывание)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(ProductType $productType, Products $product)
    {
        Log::info("Выгрузка данных по продукту");
        return view('products.show', compact('product', 'productType'));
    }

    public function searchByName(Request $request)
    {
        $query = $request->input('query');
        $products = Products::where('name', 'like', '%' . $query. '%')
        ->limit(10)->get(['id', 'name']);
        Log::info($products);
        return $products;
    }

}
