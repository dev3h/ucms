<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class UsersExport implements FromCollection
{
    public function collection()
    {
        return new Collection([
            ['Name', 'Email', 'Password'],
            ['Sample Name', 'sample@example.com', 'password123'],
        ]);
    }
}
