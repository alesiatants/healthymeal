<?php

namespace App\DTO;
use App\DTO\Time;
use App\DTO\ErrorMessages;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class DebugRequest
{
    #[Type('string')]
    #[SerializedName('id')]
    public string $id;
    
    #[Type('App\DTO\Time')]
    #[SerializedName('time')]
    public Time $time;
    
    #[Type('string')]
    #[SerializedName('datetime')]
    public string $datetime;

    #[Type('string')]
    #[SerializedName('method')]
    public string $method;

    #[Type('string')]
    #[SerializedName('uri')]
    public string $uri;

    #[Type('string')]
    #[SerializedName('ip')]
    public string $ip;

    #[Type('App\DTO\ErrorMessages')]
    #[SerializedName('messages')]
    public ErrorMessages $messages;
 
}