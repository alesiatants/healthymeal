<?php

namespace App\DTO;
use App\DTO\Items;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class SearchResponse
{
    
    #[Type('array<App\DTO\Items>')]
    #[SerializedName('items')]
    public array $items;

    #[Type('string')]
    #[SerializedName('term')]
    public string $term;

    #[Type('int')]
    #[SerializedName('code')]
    public int $code;

    #[Type('string')]
    #[SerializedName('message')]
    public string $message;
 
}