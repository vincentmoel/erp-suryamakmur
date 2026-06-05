<?php

namespace App\Http\Controllers\Traits;

use App\Helpers\Encryption;
use App\Helpers\Response;

trait IsActive
{
    public function toggleActive($encryptedId)
    {
        $data = $this->model::findOrFail(Encryption::decrypt($encryptedId));
        $data->update(['is_active' => !$data->is_active]);

        return Response::build(200, 'Success', [
            'is_active' => $data->is_active,
        ]);
    }
}
