<?php

namespace App\DTO;
use App\DTO\Weighted;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use Illuminate\Support\Facades\Log;
use JMS\Serializer\Annotation as Serializer;

class Items
{

    #[Type('string')]
    #[SerializedName('name')]
    public string $name;

    #[Type('float')]
    #[SerializedName('price')]
    public float $price;

    #[Type('App\DTO\Weighted')]
    #[SerializedName('weighted')]
    public Weighted $weighted;

    #[Type('string')]
    #[SerializedName('avgCost')]
    public string $avgCost;

    #[Type('string')]
    #[SerializedName('unit')]
    public string $unit;



    public function validateProduct(string $productName, string $category): ?string{
        if (isset($this->weighted->unitPrice) and in_array($category,['Овощи', "Фрукты, ягоды, сухофрукты"])) {
            return '1 кг';
        }
        elseif (preg_match('/(\d+(\.\d+)?)\s*(г|кг|мл|л|шт)\b/u', $this->name, $matches)) {
            switch ($matches[3]){
                case 'г':
                    return "100 г";
                case 'мл':   
                    return "100 мл";
                case 'кг':
                    return "1 кг";
                case 'л':
                    return "1 л";
                case 'шт':
                    return "10 шт";
            }
        }
        return null;
    }
    public function getPricePerUnit(string $productName, string $category): ?float
    {
        if (isset($this->weighted->unitPrice) and in_array($category,['Овощи', "Фрукты, ягоды, сухофрукты"])) {
            $this->unit = "1 кг";
            return  $this->weighted->unitPrice / 100;
        }
        elseif (preg_match('/(\d+(\.\d+)?)\s*(г|кг|мл|л|шт)\b/u', $this->name, $matches)) {
                $price = $this->price / 100;
                $unit = (float) $matches[1];
                $this->unit = $matches[3];
                switch ($matches[3]){
                    case 'г':
                    case 'мл':   
                        $this->unit = "100 " . $matches[3];
                        return round(($price * 100) / $unit, 2);
                    case 'кг':
                    case 'л':
                        $this->unit = "1 " . $matches[3];
                        return round($price/$unit, 2);
                    case 'шт':
                        $this->unit = "10 " . $matches[3];
                        return round((10 * $price)/$unit, 2
                        ) ;
                }
        }
        return null;
    }
    public function setAvgCost(string $avgCost) {
        $this->avgCost = $avgCost;
    }
    public function setUnit(string $unit) {
        $this->unit = $unit;
    }
    public function getPricePerKg(string $productName, string $category): ?float
    {
        if (isset($this->weighted->unitPrice) and in_array($category,['Овощи', "Фрукты, ягоды, сухофрукты"])) {
            return  $this->weighted->unitPrice / 100;
        }
        elseif (preg_match('/(\d+(\.\d+)?)\s*(г|кг|мл|л|шт)\b/u', $this->name, $matches)) {
                $price = $this->price / 100;
                $unit = (float) $matches[1];
                switch ($matches[3]){
                    case 'г':
                    case 'мл':   
                        return ($price * 1000) / $unit;
                    case 'кг':
                    case 'л':
                        return $price / $unit;
                    case 'шт':
                        return ((1000 * $price)/$unit) / 100;
                }
        }
        return null;
    }

    public function getWeight(): ?float
    {
        return $this->weighted->shelfWeight ?? null;
    }

    public function getPricePer100Gram(): ?float
    {
        if (preg_match('/(\d+(\.\d+)?)\s*(г|кг|мл|л|шт)\b/u', $this->name, $matches)) {
            $quantity = (float) $matches[1];
        }
        if (isset($quantity)) {
            return (1000 * $this->price) / $quantity;
        }
        return null;
    }
}