<?php

namespace App\Services;

use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use App\DTO\SearchResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\DTO\ErrorResponse;
use App\DTO\Items;
use App\DTO\Response;
use Illuminate\Support\Facades\Log;

class ProductPriceService
{
    private Client $client;
    private SerializerInterface $serializer;
    public function __construct(SerializerInterface $serializer)
    {
            $this->serializer = $serializer;
            $this->client = new Client([
                'base_uri' => env('MAGNIT_API_URL'),
                'headers' => [
                    'Authorization' => 'Bearer ' . env('MAGNIT_API_BAREER'),
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                    'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                ],
                'timeout' => 80,
                'http_errors' => false]);  }
    public function searchProducts(string $term, int $limit = 6): SearchResponse
    {
            $response = $this->client->post('', [
                'json' => [
                    'term' => $term,
                    'pagination' => ['limit' => $limit],
                    'sort' => [
                        'order' => 'desc',
                        'type' => 'popularity',],
                    'includeAdultGoods' => true,
                    'storeCode' => '992301',
                    'storeType' => '6',
                    'catalogType' => '1', ]]);
            $respData = json_decode($response->getBody()->getContents(), true);
            $code = $response->getStatusCode();
            if ($code !== 200) {
                $errorRespData = ['items' => [], 'term' =>$term, 'code' => $code, 'message' => $respData['message']];
                $result = $this->serializer->deserialize(
                    json_encode($errorRespData),SearchResponse::class,
                    'json');
            } else {
                $respData['code'] = $response->getStatusCode();
                $respData['message'] = '';
                $result = $this->serializer->deserialize(
                    json_encode($respData), SearchResponse::class,
                    'json' );}
            return $result;     
    }


    public function calculateGramm(float $quantity, string $unit):float {
        switch ($unit){
            case 'г': return $quantity;
            case 'кг': return $quantity * 1000;
            case 'мл': return $quantity; // предполагаем плотность ~1 г/мл
            case 'л': return $quantity * 1000;
            case 'ч.л.': return $quantity * 5; // чайная ложка ~5г
            case 'ст.л.': return $quantity * 15; // столовая ложка ~15г
            case 'ст': return $quantity * 240; // стакан ~240г
            case 'шт': return $quantity * 100; // условно 1 шт = 100г
            default: return $quantity; }
    }
    public function filterProposition(array $products, string  $productName, string $category)
    { $products =  array_filter($products, fn(Items $p) => 
             $p->getPricePerKg($productName, $category));
        foreach($products as $product) {
            $product->setAvgCost($product->getPricePerUnit( $productName, $category));}
        return $products;}
    public function calculateAveragePrice(array $products, string  $productName, string $category, float $targetWeight = 1.0,): ?float
    { $validProducts = $this->filterProposition($products, $productName, $category);
        if (empty($validProducts)) { return null; }
        $total = array_reduce($validProducts, function($carry, Items $product) use ($targetWeight, $productName, $category) {
            return null!==$product->getPricePerKg($productName, $category)?$carry + ($product->getPricePerKg($productName, $category) * $targetWeight):$carry;}, 0);
        return $total / count($validProducts);}
    public function calculateAveragePricePerUnit(array $products, string  $productName, string $category): ?string
    {$validProducts = array_filter($products, fn(Items $p) => 
             $p->validateProduct($productName, $category));
        if (empty($validProducts)) {return null;}
        $unit = array_reduce($validProducts, function($carry, Items $product) use ($productName, $category) { 
            return null!==$product->validateProduct($productName, $category)? $product->validateProduct($productName, $category):$carry; }, 0);
        $total = array_reduce($validProducts, function($carry, Items $product) use ($productName, $category) { 
                return null!==$product->getPricePerUnit($productName, $category)? $carry + $product->getPricePerUnit($productName, $category):$carry; }, 0);
        return strval(round($total / count($validProducts), 2)) . " руб. / " . $unit . ".";}
    public function calculateAveragePricePerKG(array $products, string  $productName, string $category): ?float
    {
        $validProducts = array_filter($products, fn(Items $p) => 
             $p->getPricePerKg($productName, $category));

        if (empty($validProducts)) {
            return null; }
        $total = array_reduce($validProducts, function($carry, Items $product) use ($productName, $category) { 
                return null!==$product->getPricePerKg($productName, $category)? $carry + $product->getPricePerKg($productName, $category):$carry;
            
        }, 0);
        return $total / count($validProducts);
    }
   

    /**
     * Подготавливает данные для отображения
     *
     * @param SearchResponse $response
     * @param float|null $avgPrice
     * @return array
     */
    public function prepareForView(SearchResponse $response, ?float $avgPrice): array
    {
        return [
            'products' => array_map(function(Items $product) {
                return [
                    'name' => $product->name,
                    'price' => $product->price/ 100 ?? null,
                    'weight' => $product->weighted->shelfLabel ?? null,
                    'price_per_kg' => $product->getPricePerKg(),
                    'rating' => $product->ratings->rating ?? null
                ];
            }, $response->items),
            'search_term' => $response->term,
            'average_price' => $avgPrice,
            'total_count' => count($response->items)
        ];
    }
    public function getClient() {
        return $this->client;
    }
}