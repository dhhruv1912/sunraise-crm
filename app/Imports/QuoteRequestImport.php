<?php
namespace App\Imports;

use App\Models\Customer;
use App\Services\QuoteRequestService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class QuoteRequestImport implements ToCollection
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows->skip(1) as $row) {

            $customer = Customer::firstOrCreate(
                ['mobile' => $row[1]],
                ['name' => $row[0]]
            );

            QuoteRequestService::create([
                'customer_id' => $customer->id,
                'type'   => $row[2],
                'kw'     => $row[3],
                'budget' => $row[4],
                'notes'  => $row[5],
            ], $this->userId, 'excel');
        }
    }
}
