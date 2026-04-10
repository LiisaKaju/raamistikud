<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authorIds = Author::query()->pluck('id')->all();

        if ($authorIds === []) {
            return;
        }

        $posts = [
            [
                'title' => 'Kevadine rabamatk: mida kaasa võtta?',
                'content' => 'Kevadisel rajal on oluline kihiline riietus, kuivad sokid ja kerge vihmakeep. '
                    .'Võta kaasa ka termos ning väike esmaabikomplekt.',
                'published' => true,
            ],
            [
                'title' => '5 nippi, kuidas hoida jalad kuivad',
                'content' => 'Vali õiged matkasokid, kasuta veekindlaid jalanõusid ja vaheta märjad sokid kohe välja. '
                    .'Pikal matkal on varupaarid hädavajalikud.',
                'published' => true,
            ],
            [
                'title' => 'Esimene mitmepäevane matk algajale',
                'content' => 'Planeeri lühem marsruut, testi varustus enne minekut ja arvesta ilmaga. '
                    .'Mõistlik tempovalik aitab vältida ülekoormust.',
                'published' => true,
            ],
            [
                'title' => 'Metsaraja etikett ja looduse hoidmine',
                'content' => 'Liigu märgitud radadel, vii prügi alati endaga kaasa ja austa vaikust. '
                    .'Nii jääb loodus puhtaks ka järgmistele matkajatele.',
                'published' => true,
            ],
            [
                'title' => 'Sügisretke kontrollnimekiri',
                'content' => 'Sügisel arvesta lühema päevaga, pimeduse tulekuga ning libedate lõikudega. '
                    .'Pealamp ja nähtavuselemendid on väga olulised.',
                'published' => false,
            ],
        ];

        foreach ($posts as $index => $post) {
            Post::updateOrCreate(
                ['title' => $post['title']],
                [
                    ...$post,
                    'author_id' => $authorIds[$index % count($authorIds)],
                ]
            );
        }
    }
}
