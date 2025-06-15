<?php

namespace App\DTO;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class Weighted
{
    #[Type('float')]
    #[SerializedName('shelfWeight')]
    public ?float $shelfWeight;
    #[Type('string')]
    #[SerializedName('shelfLabel')]
    public ?string $shelfLabel;

    #[Type('float')]
    #[SerializedName('unitPrice')]
    public ?float $unitPrice;
}