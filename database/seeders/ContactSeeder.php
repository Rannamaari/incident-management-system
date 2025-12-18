<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use Illuminate\Support\Facades\File;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing contacts
        Contact::truncate();

        // Read CSV file
        $csvFile = public_path('contact_book_entries.csv');

        if (!File::exists($csvFile)) {
            $this->command->error('CSV file not found at: ' . $csvFile);
            return;
        }

        $file = fopen($csvFile, 'r');

        // Skip header row
        $header = fgetcsv($file);

        $imported = 0;

        while (($row = fgetcsv($file)) !== false) {
            // Map CSV columns to database fields
            Contact::create([
                'name' => $row[0] ?? null,
                'phone' => $row[1] ?? null,
                'email' => $row[2] ?? null,
                'company' => $row[3] ?? null,
                'role' => $row[4] ?? null,
                'category' => $row[5] ?? null,
                'region' => $row[6] ?? null,
                'atoll' => $row[7] ?? null,
                'island' => $row[8] ?? null,
                'site' => $row[9] ?? null,
                'notes' => $row[10] ?? null,
                'source_sheet' => $row[11] ?? null,
                'raw' => $row[12] ?? null,
            ]);

            $imported++;
        }

        fclose($file);

        $this->command->info("Imported {$imported} contacts successfully!");
    }
}
