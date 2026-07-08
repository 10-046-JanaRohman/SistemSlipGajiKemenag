<?php

namespace App\Imports;

use App\Models\SlipGaji;
use Maatwebsite\Excel\Concerns\ToModel;

class GajiImportV2 implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SlipGaji([
            //
        ]);
    }
}
