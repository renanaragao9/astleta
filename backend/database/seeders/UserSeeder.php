<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    private array $brazilianNames = [
        'João Silva',
        'Maria Santos',
        'José Oliveira',
        'Ana Costa',
        'Carlos Ferreira',
        'Francisca Rocha',
        'Pedro Almeida',
        'Márcia Souza',
        'Antonio Gomes',
        'Paula Martins',
        'Luís Ribeiro',
        'Juliana Barbosa',
        'Marcos Teixeira',
        'Fernanda Monteiro',
        'Rodrigo Pinto',
        'Camila Carvalho',
        'Rafael Dias',
        'Beatriz Nunes',
        'Diego Medeiros',
        'Isabela Duarte',
        'Felipe Moura',
        'Larissa Correia',
        'Gabriel Tavares',
        'Sofia Fonseca',
        'Lucas Azevedo',
        'Aline Brito',
        'Matheus Leal',
        'Thiago Vieira',
        'Carolina Gomes',
        'Gustavo Braga',
        'Natalia Rocha',
        'André Couto',
        'Giovanna Silva',
        'Bruno Mendes',
        'Leticia Castro',
        'Victor Bastos',
        'Débora Lemos',
        'Henrique Vaz',
        'Vanessa Faro',
        'Eduardo Sousa',
        'Verônica Peixoto',
        'Alexandre Neves',
        'Heloise Branco',
        'Leonardo Salazar',
        'Cecília Gama',
        'Elias Reis',
        'Marina Marques',
        'Otávio Prestes',
        'Amélia Vargas',
        'Cristiano Mendes',
    ];

    private array $dominantSides = ['esquerdo', 'direito', 'ambos'];

    private array $bios = [
        'Atleta dedicado com paixão pelo esporte.',
        'Treinador profissional com 10 anos de experiência.',
        'Amante do desporto e vida saudável.',
        'Jogador talentoso buscando aprimoramento contínuo.',
        'Atleta determinado e focado em objetivos.',
        'Jogador versátil com múltiplas habilidades.',
        'Profissional do esporte com grande dedicação.',
        'Atleta em desenvolvimento constante.',
    ];

    private array $sportIds = [];

    private array $positionIds = [];

    private array $featureIds = [];

    public function run(): void
    {
        $this->sportIds = DB::table('sports')->pluck('id')->toArray();
        $this->positionIds = DB::table('positions')->pluck('id')->toArray();
        $this->featureIds = DB::table('features')->pluck('id')->toArray();

        User::create([
            'uuid' => Str::uuid(),
            'name' => 'Admin',
            'username' => 'administrador@astleta',
            'date' => '1990-01-01',
            'cpf' => '123.456.789-01',
            'email' => 'astleta@astleta.com',
            'phone' => '11999999999',
            'password' => Hash::make('Goku@alvinegro#@!2024'),
            'email_verified_at' => now(),
            'qtd_login' => 0,
            'type' => 'normal',
            'lang' => 'pt',
            'is_active' => true,
            'profile_id' => 1,
        ]);

        for ($i = 1; $i <= 50; $i++) {
            $user = User::create([
                'uuid' => Str::uuid(),
                'name' => $this->brazilianNames[$i - 1],
                'username' => 'atletateste' . $i,
                'date' => $this->getRandomDate(),
                'cpf' => sprintf('%03d.%03d.%03d-%02d', $i, $i + 100, $i + 200, ($i + 12) % 100),
                'email' => 'atleta' . $i . '@astleta.com',
                'phone' => '117777777' . str_pad(77 + $i, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'qtd_login' => 0,
                'type' => 'test',
                'lang' => 'pt',
                'is_active' => true,
                'is_public' => true,
                'profile_id' => 2,
            ]);

            $sportId = !empty($this->sportIds) ? $this->sportIds[array_rand($this->sportIds)] : null;
            $positionId = !empty($this->positionIds) ? $this->positionIds[array_rand($this->positionIds)] : null;
            $subpositionId = !empty($this->positionIds) ? $this->positionIds[array_rand($this->positionIds)] : null;
            $featureId = !empty($this->featureIds) ? $this->featureIds[array_rand($this->featureIds)] : null;
            $subfeatureId = !empty($this->featureIds) ? $this->featureIds[array_rand($this->featureIds)] : null;

            DB::table('athlete_profiles')->insert([
                'dominant_side' => $this->dominantSides[array_rand($this->dominantSides)],
                'bio' => $this->bios[array_rand($this->bios)],
                'height' => rand(160, 210) / 100,
                'weight' => rand(60, 120),
                'user_id' => $user->id,
                'sport_id' => $sportId,
                'position_id' => $positionId,
                'subposition_id' => $subpositionId,
                'feature_id' => $featureId,
                'subfeature_id' => $subfeatureId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getRandomDate(): string
    {
        $year = rand(1980, 2005);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

        return "$year-$month-$day";
    }
}
