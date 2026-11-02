<?php

namespace App\Jobs;

use App\Models\ImportError;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProcessProductImport implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60];

    public int $timeout = 60;

    public function __construct(private string $csvPath) {}

    public function handle(): void
    {
        ImportError::query()->delete();

        $lines = file(Storage::path($this->csvPath), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $rows = array_map(fn (string $line) => str_getcsv($line, ',', '"', '\\'), $lines);
        $header = array_shift($rows);

        if (! is_array($header) || $header === []) {
            $this->recordError(1, 'header CSV tidak ada');
            Storage::delete($this->csvPath);

            return;
        }

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            if (count($row) !== count($header)) {
                $this->recordError($line, 'jumlah kolom tidak sesuai');

                continue;
            }

            $validator = Validator::make(array_combine($header, $row), [
                'name' => ['required', 'string'],
                'category_id' => ['required', 'exists:categories,id'],
                'price' => ['required', 'integer', 'min:0'],
            ], [
                'name.required' => 'name kosong',
                'category_id.required' => 'kategori_id kosong',
                'category_id.exists' => 'kategori_id tidak ditemukan',
                'price.required' => 'price kosong',
                'price.integer' => 'price harus berupa angka',
                'price.min' => 'price tidak boleh negatif',
            ]);

            if ($validator->fails()) {
                $this->recordError($line, $validator->errors()->first());

                continue;
            }

            $data = $validator->validated();

            Product::updateOrCreate(
                ['name' => $data['name'], 'category_id' => $data['category_id']],
                ['price' => $data['price']],
            );
        }

        Storage::delete($this->csvPath);
    }

    private function recordError(int $line, string $reason): void
    {
        ImportError::create([
            'row_number' => $line,
            'message' => "Baris {$line}: {$reason}",
        ]);
    }
}
