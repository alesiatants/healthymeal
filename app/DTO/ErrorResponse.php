<?php

namespace App\DTO;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use Illuminate\Support\Facades\Log;
use JMS\Serializer\Annotation as Serializer;
use App\Models\RecipesComment;

class ErrorResponse
{
    #[Type('bool')]
    #[SerializedName('status')]
    public bool $status;

    #[Type('array')]
    #[SerializedName('validation')]
    public array $validation;

    #[Type('string')]
    #[SerializedName('message')]
    public string $message;
    
    public function __construct(bool $status = true, array $validation = [], string $message = "")
    {
        $this->status = $status;
        $this->validation = $validation;
        $this->message = $message;
    }
}