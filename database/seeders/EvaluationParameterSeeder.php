<?php

namespace Database\Seeders;

use App\Models\EvaluationParameter;
use Illuminate\Database\Seeder;

class EvaluationParameterSeeder extends Seeder
{
    public function run(): void
    {
        $parameters = config('scoring.parameters', []);

        foreach ($parameters as $param) {
            EvaluationParameter::updateOrCreate(
                ['code' => $param['code']],
                [
                    'name'       => $param['name'],
                    'weight'     => $param['weight'],
                    'is_active'  => true,
                    'sort_order' => $param['sort_order'],
                ]
            );
        }

        $this->command->info('✅ Parameter evaluasi berhasil di-seed dari config/scoring.php');
    }
}
