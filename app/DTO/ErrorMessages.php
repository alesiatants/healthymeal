<?php

namespace App\DTO;

use App\DTO\LogMessage;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class ErrorMessages
{
    #[Type('int')]
    #[SerializedName('count')]
    public int $count;
    
    #[Type('array<App\DTO\LogMessage>')]
    #[SerializedName('messages')]
    public array $messages = [];
 
}