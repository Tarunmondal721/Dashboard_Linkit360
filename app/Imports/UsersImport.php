<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
         // print_r($row);die('+++++');
        return new User([
            'staff_id'     => $row[0],
            'ippis_no'     => $row[1],
            'password' => \Hash::make($row[2]),
            'email'    => $row[3],
            'fname'     => $row[4],
            'lname'     => $row[5],
            'gender'     => $row[6],
            'type' => $row[7], 
            'org_code' => $row[8], 
            'created_by' => 1, 
        ]);
    }
}
