<?php

namespace App\Services;

use App\Models\Variable;

class SystemStatusVariables
{
    public function vmwareCores(): int
    {
        $value = Variable::query()->where('name', 'vmware_cores')->value('value');

        return is_numeric($value) ? (int) $value : 0;
    }
}
