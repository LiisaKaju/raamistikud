<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            ['first_name' => 'Mati', 'last_name' => 'Kask', 'date_of_birth' => '1989-04-14'],
            ['first_name' => 'Katrin', 'last_name' => 'Saar', 'date_of_birth' => '1992-11-03'],
            ['first_name' => 'Joonas', 'last_name' => 'Tamm', 'date_of_birth' => '1985-07-22'],
            ['first_name' => 'Liis', 'last_name' => 'Mets', 'date_of_birth' => '1996-01-17'],
            ['first_name' => 'Karl', 'last_name' => 'Puu', 'date_of_birth' => '1990-09-29'],
        ];

        foreach ($authors as $author) {
            Author::updateOrCreate(
                ['first_name' => $author['first_name'], 'last_name' => $author['last_name']],
                $author
            );
        }
    }
}
