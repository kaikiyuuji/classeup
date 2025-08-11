<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlunoProfessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // Lista de alunos fictícios
    public function run(): void
    {
        // Lista de alunos
        $alunos = [
            [
                'nome' => 'Howard Wolowitz',
                'cpf' => '12345678910',
                'data_nascimento' => '2007-02-01',
                'email' => 'howard@tbt.com',
                'telefone' => '99987654321',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Sheldon Cooper',
                'cpf' => '23456789101',
                'data_nascimento' => '2006-05-26',
                'email' => 'sheldon@tbt.com',
                'telefone' => '99912345678',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Leonard Hofstadter',
                'cpf' => '34567891012',
                'data_nascimento' => '2006-09-15',
                'email' => 'leonard@tbt.com',
                'telefone' => '99876543210',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Rajesh Koothrappali',
                'cpf' => '45678910123',
                'data_nascimento' => '2007-08-15',
                'email' => 'rajesh@tbt.com',
                'telefone' => '99765432109',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Penny Teller',
                'cpf' => '56789101234',
                'data_nascimento' => '2007-11-02',
                'email' => 'penny@tbt.com',
                'telefone' => '99654321098',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Amy Farrah Fowler',
                'cpf' => '67891012345',
                'data_nascimento' => '2006-12-12',
                'email' => 'amy@tbt.com',
                'telefone' => '99543210987',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Stuart Bloom',
                'cpf' => '78910123456',
                'data_nascimento' => '2007-01-20',
                'email' => 'stuart@tbt.com',
                'telefone' => '99432109876',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Emily Sweeney',
                'cpf' => '89101234567',
                'data_nascimento' => '2007-04-09',
                'email' => 'emily@tbt.com',
                'telefone' => '99321098765',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Leslie Winkle',
                'cpf' => '91012345678',
                'data_nascimento' => '2007-06-18',
                'email' => 'leslie@tbt.com',
                'telefone' => '99210987654',
                'endereco' => 'Pasadena, California',
            ],
            [
                'nome' => 'Bert Kibbler',
                'cpf' => '10123456789',
                'data_nascimento' => '2007-09-27',
                'email' => 'bert@tbt.com',
                'telefone' => '99109876543',
                'endereco' => 'Pasadena, California',
            ],
        ];

        // Lista de professores
        $professores = [
            [
                'nome' => 'Bernadette Rostenkowski',
                'cpf' => '32109876543',
                'data_nascimento' => '1986-03-10',
                'ativo' => false,
                'email' => 'bernadette@tbt.com',
                'especialidade' => 'Microbiologia',
                'endereco' => 'Pasadena, California',
                'formacao' => 'Doutorado (Universidade da California)',
            ],
            [
                'nome' => 'Stephen Hawking',
                'cpf' => '43210987654',
                'data_nascimento' => '1942-01-08',
                'ativo' => true,
                'email' => 'hawking@tbt.com',
                'especialidade' => 'Física Teórica',
                'endereco' => 'Cambridge, Reino Unido',
                'formacao' => 'PhD em Física (Universidade de Cambridge)',
            ],
            [
                'nome' => 'Eric Gablehauser',
                'cpf' => '54321098765',
                'data_nascimento' => '1970-05-14',
                'ativo' => true,
                'email' => 'gablehauser@tbt.com',
                'especialidade' => 'Ciência de Materiais',
                'endereco' => 'Pasadena, California',
                'formacao' => 'Doutorado (MIT)',
            ],
            [
                'nome' => 'Beverly Hofstadter',
                'cpf' => '65432109876',
                'data_nascimento' => '1960-07-23',
                'ativo' => true,
                'email' => 'beverly@tbt.com',
                'especialidade' => 'Neurociência',
                'endereco' => 'Princeton, New Jersey',
                'formacao' => 'PhD em Neurociência (Princeton)',
            ],
            [
                'nome' => 'Leslie Lamport',
                'cpf' => '76543210987',
                'data_nascimento' => '1941-02-07',
                'ativo' => true,
                'email' => 'lamport@tbt.com',
                'especialidade' => 'Computação Distribuída',
                'endereco' => 'Pasadena, California',
                'formacao' => 'PhD em Matemática (Brandeis University)',
            ],
            [
                'nome' => 'Jane Goodall',
                'cpf' => '87654321098',
                'data_nascimento' => '1934-04-03',
                'ativo' => true,
                'email' => 'goodall@tbt.com',
                'especialidade' => 'Primatologia',
                'endereco' => 'Londres, Reino Unido',
                'formacao' => 'PhD em Etologia (Universidade de Cambridge)',
            ],
            [
                'nome' => 'Carl Sagan',
                'cpf' => '98765432109',
                'data_nascimento' => '1934-11-09',
                'ativo' => true,
                'email' => 'sagan@tbt.com',
                'especialidade' => 'Astronomia',
                'endereco' => 'Nova Iorque, EUA',
                'formacao' => 'PhD em Astronomia (Universidade de Chicago)',
            ],
            [
                'nome' => 'Richard Feynman',
                'cpf' => '09876543210',
                'data_nascimento' => '1918-05-11',
                'ativo' => false,
                'email' => 'feynman@tbt.com',
                'especialidade' => 'Física Quântica',
                'endereco' => 'Pasadena, California',
                'formacao' => 'PhD em Física (Princeton)',
            ],
            [
                'nome' => 'Marie Curie',
                'cpf' => '10987654321',
                'data_nascimento' => '1867-11-07',
                'ativo' => false,
                'email' => 'curie@tbt.com',
                'especialidade' => 'Química e Física',
                'endereco' => 'Paris, França',
                'formacao' => 'Doutorado em Física (Sorbonne)',
            ],
            [
                'nome' => 'Isaac Newton',
                'cpf' => '21098765432',
                'data_nascimento' => '1643-01-04',
                'ativo' => false,
                'email' => 'newton@tbt.com',
                'especialidade' => 'Matemática e Física',
                'endereco' => 'Woolsthorpe, Inglaterra',
                'formacao' => 'Universidade de Cambridge',
            ],
        ];

        // Criar alunos
        foreach ($alunos as $dados) {
            $aluno = Aluno::where('email', $dados['email'])->first();
            if (!$aluno) {
                Aluno::create(array_merge($dados, [
                    'numero_matricula' => Aluno::gerarNumeroMatricula(),
                    'data_matricula' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                    'status_matricula' => 'ativa',
                ]));
                $this->command->info("✓ Aluno {$dados['nome']} criado com sucesso!");
            } else {
                $this->command->info("→ Aluno {$dados['nome']} já existe no banco de dados.");
            }
        }

        // Criar professores
        foreach ($professores as $dados) {
            $professor = Professor::where('email', $dados['email'])->first();
            if (!$professor) {
                Professor::create($dados);
                $this->command->info("✓ Professor(a) {$dados['nome']} criado(a) com sucesso!");
            } else {
                $this->command->info("→ Professor(a) {$dados['nome']} já existe no banco de dados.");
            }
        }
    }
}
