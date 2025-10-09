<?php

namespace Database\Seeders;

use App\Models\Parentesco;
use Illuminate\Database\Seeder;

class ParentescoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentescos = [
            'Padre',
            'Madre',
            'Hermano',
            'Hermana',
            'Hijo',
            'Hija',
            'Abuelo',
            'Abuela',
            'Tío',
            'Tía',
            'Primo',
            'Prima',
            'Sobrino',
            'Sobrina',
            'Nieto',
            'Nieta',
            'Esposo',
            'Esposa',
            'Cuñado',
            'Cuñada',
            'Suegro',
            'Suegra',
            'Amigo',
            'Amiga',
            'Otro',
        ];

        foreach ($parentescos as $parentesco) {
            Parentesco::create([
                'nombre_parentesco' => $parentesco,
            ]);
        }
    }
}

