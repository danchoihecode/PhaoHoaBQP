<?php

use App\Models\ContactDetail;


ContactDetail::updateOrCreate(
    ['id' => 1],
    [
        'phone_number' => '1234567890',
        'operation_hours' => '9h00 - 18h00',
        'fee' => 'Miễn phí',
        'type' => 'Bán hàng'
    ]
);
