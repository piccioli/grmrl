<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Rifugio Branca',
                'description' => 'Osservazione cambiamenti climatici, fauna/flora Parco Nazionale dello Stelvio, morfologia delle montagne',
                'meeting_time' => '9:00',
                'meeting_place' => 'Parcheggio Diga S.Giacomo presso Rif. Forni',
                'max_capacity' => 50,
            ],
            [
                'name' => 'Rifugio Alpe Corte',
                'description' => 'Forest Bathing con guida di Benessere Forestale',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio laghetto Valcanale (Valle Seriana)',
                'max_capacity' => 50,
            ],
            [
                'name' => 'Rifugio Menaggio',
                'description' => 'Riconoscimento fauna selvatica attraverso gli indici di presenza',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio Monti di Breglia',
                'max_capacity' => 50,
            ],
            [
                'name' => 'Rifugio G. Pirlo allo Spino',
                'description' => 'Lettura del paesaggio con esperto',
                'meeting_time' => '8:45',
                'meeting_place' => 'Colomber di San Michele – Gardone Riviera',
                'max_capacity' => 50,
            ],
            [
                'name' => 'Rifugio Carlo Porta',
                'description' => 'Osservazione aspetti botanici',
                'meeting_time' => '9:00',
                'meeting_place' => 'Ritrovo angolo via Pian dei Resinelli e via Carlo Mauri',
                'max_capacity' => 50,
            ],
        ];

        foreach ($activities as $data) {
            Activity::create($data);
        }
    }
}
