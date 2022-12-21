<?php

namespace App\Imports;

use App\Models\Charge;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ChargesImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Charge([
            'name' =>$row['name'],
            'governmentId' =>$row['governmentId'],
            'email' =>$row['email'],
            'debtAmount' =>$row['debtAmount'],
            'debtDueDate' =>$row['debtDueDate'],
            'debtId' =>$row['debtId']
        ]);
    }
}
