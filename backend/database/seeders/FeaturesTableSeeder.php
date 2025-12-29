<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $featuresBySportAndPosition = [
            'Futebol' => [
                'Goleiro' => [
                    'Defesa',
                    'Reflexos',
                    'Posicionamento',
                    'Saída de Gol',
                    'Passe',
                ],
                'Zagueiro' => [
                    'Marcação',
                    'Cabeceio',
                    'Posicionamento Defensivo',
                    'Desarme',
                    'Passe Longo',
                ],
                'Lateral Direito' => [
                    'Cruzamento',
                    'Velocidade',
                    'Defesa',
                    'Ataque',
                    'Resistência',
                ],
                'Lateral Esquerdo' => [
                    'Cruzamento',
                    'Velocidade',
                    'Defesa',
                    'Ataque',
                    'Resistência',
                ],
                'Volante' => [
                    'Marcação',
                    'Visão de Jogo',
                    'Passe',
                    'Chute de Longa Distância',
                    'Recuperação de Bola',
                ],
                'Meia' => [
                    'Visão de Jogo',
                    'Drible',
                    'Passe',
                    'Chute',
                    'Criatividade',
                ],
                'Atacante' => [
                    'Finalização',
                    'Drible',
                    'Velocidade',
                    'Cabeceio',
                    'Posicionamento Ofensivo',
                ],
                'Centroavante' => [
                    'Finalização',
                    'Cabeceio',
                    'Posicionamento Ofensivo',
                    'Força Física',
                    'Instinto de Gol',
                ],
                'Ponta Direita' => [
                    'Velocidade',
                    'Drible',
                    'Cruzamento',
                    'Finalização',
                    'Finta',
                ],
                'Ponta Esquerda' => [
                    'Velocidade',
                    'Drible',
                    'Cruzamento',
                    'Finalização',
                    'Finta',
                ],
            ],

            'Vôlei' => [
                'Levantador' => [
                    'Passe',
                    'Visão de Jogo',
                    'Levantamento',
                    'Defesa',
                    'Comunicação',
                ],
                'Oposto' => [
                    'Ataque',
                    'Bloqueio',
                    'Saque',
                    'Defesa',
                    'Poder de Fogo',
                ],
                'Central' => [
                    'Bloqueio',
                    'Defesa',
                    'Ataque',
                    'Salto',
                    'Posicionamento',
                ],
                'Ponteiro' => [
                    'Ataque',
                    'Saque',
                    'Defesa',
                    'Velocidade',
                    'Recepção',
                ],
                'Líbero' => [
                    'Recepção',
                    'Defesa',
                    'Passe',
                    'Velocidade',
                    'Posicionamento',
                ],
            ],
        ];

        foreach ($featuresBySportAndPosition as $sportName => $positions) {
            $sport = DB::table('sports')->where('name', $sportName)->first();
            if ($sport) {
                foreach ($positions as $positionName => $features) {
                    $position = DB::table('positions')->where('sport_id', $sport->id)->where('name', $positionName)->first();
                    if ($position) {
                        foreach ($features as $featureName) {
                            DB::table('features')->insert([
                                'name' => $featureName,
                                'position_id' => $position->id,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
