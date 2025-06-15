<?php

namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class LogMessage
{
    #[Type('string')]
    #[SerializedName('message')]
    public string $message;
    
    #[Type('string')]
    #[SerializedName('label')]
    public string $label;

    #[Type('float')]
    #[SerializedName('time')]
    public float $time;
    
}