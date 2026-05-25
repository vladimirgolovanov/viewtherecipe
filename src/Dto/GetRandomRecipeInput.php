<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GetRandomRecipeInput
{
    /**
     * @var int[] IDs of recipes already suggested in this session — they will be excluded from the result.
     */
    #[Assert\All([new Assert\Type('integer')])]
    public array $exclude_ids = [];
}
