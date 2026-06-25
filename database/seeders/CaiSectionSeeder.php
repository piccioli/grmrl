<?php

namespace Database\Seeders;

use App\Models\CaiSection;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CaiSectionSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('docs/materiale iniziale/2026_MS_Sezioni_SottoSezioni_GR_Gruppi Regionali ETS.xlsx');

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('Sezioni');

        $highestRow = $sheet->getHighestDataRow();

        $batch = [];

        for ($i = 2; $i <= $highestRow; $i++) {
            $code = $sheet->getCell('A' . $i)->getValue();

            if (empty($code)) {
                continue;
            }

            $batch[] = [
                'code'     => (string) $code,
                'name'     => (string) $sheet->getCell('B' . $i)->getValue(),
                'region'   => $sheet->getCell('C' . $i)->getValue() ?: null,
                'province' => $sheet->getCell('D' . $i)->getValue() ?: null,
            ];

            if (count($batch) >= 100) {
                CaiSection::insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            CaiSection::insert($batch);
        }
    }
}
