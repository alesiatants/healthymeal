<?php

namespace App\DTO;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class Time
{
    #[Type('float')]
    #[SerializedName('duration')]
    public ?float $duration;
    
    #[Type('string')]
    #[SerializedName('duration_str')]
    public ?string $duration_str;
}