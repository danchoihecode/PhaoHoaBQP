<?php

namespace App\Main\DTO;
use App\Models\Setting;

class SettingDTO
{
    protected ?Setting $dto;
    public function __construct($dto)
    {
        $this->dto = $dto;
    }

    public function formatData() {
        $item = $this->dto;

        return $item;
    }



}
