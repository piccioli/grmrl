<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'detailed_description' => 'Unisciti a noi per un trekking straordinario con base al Rifugio Branca. Questa spettacolare terrazza naturale diventerà il nostro laboratorio a cielo aperto per studiare la morfologia delle vette e analizzare l\'impatto dei cambiamenti climatici in corso sui ghiacciai. Lungo il cammino, esploreremo la straordinaria biodiversità del Parco Nazionale dello Stelvio, scoprendo i segreti della sua flora e avvistando la fauna locale. Scegli un\'avventura che unisce il piacere dell\'outdoor a una nuova consapevolezza ambientale.',
                'meeting_time' => '9:00',
                'meeting_place' => 'Parcheggio Diga di San Giacomo, ticket giornaliero a Santa Caterina Valfurva',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '343 m',
                'trail_length' => '3 km – 3 ore',
                'water_description' => 'Al parcheggio',
                'itinerary_description' => 'L\'escursione al Rifugio Branca è quanto di più remunerativo si possa desiderare per godere del panorama del gruppo Ortles-Cevedale a corona delle famose 13 Cime. Il rifugio Branca (CAI Milano) è un balcone panoramico davanti al ghiacciaio dei Forni, il più grande apparato nivo-glaciale delle Alpi Italiane contornato da cime che superano sempre i 3500mt. Una escursione adatta a tutti che regala grandi emozioni.',
                'image_url' => 'https://organizzazione.cai.it/gr-lombardia/wp-content/uploads/sites/63/2026/06/a3e473c5a7a4e0ddb029527d5d491733.webp',
            ],
            [
                'name' => 'Rifugio Alpe Corte',
                'description' => 'Forest Bathing con guida di Benessere Forestale',
                'detailed_description' => 'Insieme alla Guida di Benessere Forestale, Ilaria Saurgnani, saremo introdotti al Forest Bathing (Shinrin-yoku), una pratica di origine giapponese sostenuta da evidenze scientifiche che ne dimostrano i benefici sul benessere psicofisico e sulla riduzione dello stress. In Giappone è riconosciuta come terapia forestale anche in ambito medico. Un\'esperienza unica per ritrovare il contatto con la natura e con se stessi.',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio laghetto Valcanale (Valle Seriana)',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '350 m',
                'trail_length' => '5 km – 3 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Il rifugio a quota 1410 m è immerso in una splendida pineta al cospetto di severissime pareti dolomitiche. È la prima tappa del Sentiero delle Orobie Orientali, proprietà Sezione CAI Bergamo.',
                'image_url' => 'https://organizzazione.cai.it/gr-lombardia/wp-content/uploads/sites/63/2026/06/pics_kartub-forest-4908091_1920-1-1620x1080.webp',
            ],
            [
                'name' => 'Rifugio Menaggio',
                'description' => 'Riconoscimento fauna selvatica attraverso gli indici di presenza',
                'detailed_description' => 'Se vuoi imparare a decifrare i segreti della fauna selvatica senza disturbarla, non perdere questo speciale laboratorio a cielo aperto. Insieme a Vincenzo Perin, attraverso esercitazioni pratiche e di gruppo, impareremo l\'arte del monitoraggio non invasivo: riconoscere impronte, segni di presenza e tracce lasciate dagli animali nel loro habitat naturale.',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio Monti di Breglia',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '650 m',
                'trail_length' => '5 km – 4,5 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Dal parcheggio dei Monti di Breglia si sale a destra lungo tracciato panoramico tra betulle e ginestre fino a Sant\'Amate e Monte Bregagno. Lungo suggestivo passaggio in costa fino al Rifugio Menaggio con vista sul centro lago, Bellagio e Grigne.',
                'image_url' => 'https://organizzazione.cai.it/gr-lombardia/wp-content/uploads/sites/63/2026/06/rottonara-binoculars-5760779_1920-720x1080.webp',
            ],
            [
                'name' => 'Rifugio G. Pirlo allo Spino',
                'description' => 'Lettura del paesaggio con esperto',
                'detailed_description' => 'Guidati da un architetto ambientale, osserveremo da vicino la straordinaria trasformazione del territorio: scopriremo come un\'area un tempo fortemente antropizzata – tra antiche mulattiere militari e vecchie caserme – stia venendo riabbracciata da una natura rigogliosa e selvaggia. Un viaggio nel tempo tra storia, paesaggio e biodiversità.',
                'meeting_time' => '8:45',
                'meeting_place' => 'Colomber di San Michele – Gardone Riviera',
                'max_capacity' => 50,
                'difficulty' => 'E – Escursionistico',
                'elevation_gain' => '950 m',
                'trail_length' => '6 km – 3 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Il rifugio a 1165 m fu caserma della Guardia di Finanza fino all\'inizio della Prima Guerra mondiale. Percorso sui resti della mulattiera militare lastricata, fino all\'ex cascina Gemelle con fontana restaurata, poi nel bosco di faggio fino al Passo Spino.',
                'image_url' => 'https://organizzazione.cai.it/gr-lombardia/wp-content/uploads/sites/63/2026/06/7b9affd2e4f8f1638ccc0ff37f2ee28c.webp',
            ],
            [
                'name' => 'Rifugio Carlo Porta',
                'description' => 'Osservazione aspetti botanici',
                'detailed_description' => 'Durante la giornata, la Dr.ssa Clara Citterio vi accompagnerà alla scoperta degli affascinanti aspetti botanici del territorio. L\'esperienza sarà arricchita da un momento culturale unico: la lettura di alcune delle più belle poesie di Antonia Pozzi, poetessa milanese del Novecento profondamente legata a questi luoghi.',
                'meeting_time' => '8:45',
                'meeting_place' => 'Parcheggio via Piani dei Resinelli (a pagamento)',
                'max_capacity' => 50,
                'difficulty' => 'T – Turistico',
                'elevation_gain' => '200 m',
                'trail_length' => '1,5 km – 2 ore',
                'water_description' => 'Sì',
                'itinerary_description' => 'Percorso facile e agibile a tutti. Il Rifugio gode di ampio panorama ed è immerso nel verde del Bosco Giulia, evidenziando tutte le caratteristiche di rocce calcaree della Grigna meridionale.',
                'image_url' => 'https://organizzazione.cai.it/gr-lombardia/wp-content/uploads/sites/63/2026/06/farago_jozsef-mountains-7015772_1920-1620x1080.webp',
            ],
        ];

        foreach ($activities as $data) {
            Activity::create($data);
        }
    }
}
