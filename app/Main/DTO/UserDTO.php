<?php

namespace App\Main\DTO;

use App\Models\User;

class UserDTO
{
    protected ?User $product;
    public function __construct($product)
    {
        $this->user = $product;
    }

    public function formatData() {
        $item = $this->user;

        return $item;
    }



    public function formatDataDetailProduct() {
        $item = $this->user;
        return $item;
    }

}
