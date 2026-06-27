<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Activity::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $activities = [
            [
                'name' => 'Rifugio Branca',
                'description' => 'Osservazione cambiamenti climatici, fauna/flora Parco Nazionale dello Stelvio, morfologia delle montagne',
                'meeting_time' => '9:00',
                'meeting_place' => 'Parcheggio Diga di San Giacomo, ticket giornaliero a Santa Caterina Valfurva',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '343 m',
                'trail_length' => '3 km – 3 ore',
                'water_description' => 'Al parcheggio',
                'itinerary_description' => 'Trekking per studiare l\'impatto dei cambiamenti climatici sui ghiacciai e la biodiversità del Parco Nazionale dello Stelvio. Il Rifugio Branca è il balcone panoramico davanti al ghiacciaio dei Forni, circondato da cime oltre 3500 m.',
                'image_url' => null,
            ],
            [
                'name' => 'Rifugio Alpe Corte',
                'description' => 'Forest Bathing con guida di Benessere Forestale',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio laghetto Valcanale (Valle Seriana)',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '350 m',
                'trail_length' => '5 km – 3 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Pratica di origine giapponese con benefici su benessere psicofisico e riduzione dello stress. Rifugio a quota 1410 m immerso in pineta con pareti dolomitiche, prima tappa del Sentiero delle Orobie Orientali.',
                'image_url' => null,
            ],
            [
                'name' => 'Rifugio Menaggio',
                'description' => 'Riconoscimento fauna selvatica attraverso gli indici di presenza',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio Monti di Breglia',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '650 m',
                'trail_length' => '5 km – 4,5 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Laboratorio di monitoraggio non invasivo della fauna attraverso impronte e segni di presenza. Percorso panoramico tra betulle e ginestre con vista sul centro lago, Bellagio e Grigne.',
                'image_url' => null,
            ],
            [
                'name' => 'Rifugio G. Pirlo allo Spino',
                'description' => 'Lettura del paesaggio con esperto',
                'meeting_time' => '8:45',
                'meeting_place' => 'Colomber di San Michele – Gardone Riviera',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '950 m',
                'trail_length' => '6 km – 3 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Viaggio con architetto ambientale sulle trasformazioni del territorio da area antropizzata a natura selvaggia. Rifugio a 1165 m, ex caserma con osservatorio ornitologico, percorso su mulattiera militare lastricata.',
                'image_url' => null,
            ],
            [
                'name' => 'Rifugio Carlo Porta',
                'description' => 'Osservazione aspetti botanici',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio via Piani dei Resinelli (a pagamento)',
                'max_capacity' => 50,
                'difficulty' => 'T – Turistico',
                'elevation_gain' => '200 m',
                'trail_length' => '1,5 km – 2 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Percorso con botanica su aspetti floristici con lettura di poesie di Antonia Pozzi. Percorso facile e agibile a tutti, immerso nel Bosco Giulia con rocce calcaree della Grigna meridionale.',
                'image_url' => null,
            ],
        ];

        foreach ($activities as $index => $data) {
            if ($index > 0) {
                sleep(1);
            }

            $coords = $this->geocode($data['meeting_place']);

            Activity::create(array_merge($data, $coords));
        }
    }

    private function geocode(string $place): array
    {
        $query = $place.' Italia';

        try {
            $response = Http::withHeaders(['User-Agent' => 'GRMRL/1.0'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                ]);

            $results = $response->json();

            if (empty($results)) {
                $this->command->warn("Nominatim: nessun risultato per \"{$place}\"");

                return ['latitude' => null, 'longitude' => null];
            }

            $lat = (float) $results[0]['lat'];
            $lng = (float) $results[0]['lon'];

            $this->command->info("{$place} → {$lat}, {$lng}");

            return ['latitude' => $lat, 'longitude' => $lng];
        } catch (\Exception $e) {
            $this->command->warn("Nominatim: errore per \"{$place}\": {$e->getMessage()}");

            return ['latitude' => null, 'longitude' => null];
        }
    }
}
