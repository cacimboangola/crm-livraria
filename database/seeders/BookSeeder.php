<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookCategory;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garantir que temos categorias
        $categories = BookCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('Nenhuma categoria encontrada. Execute o BookCategorySeeder primeiro.');
            return;
        }
        
        $books = [
            // Ficção Científica
            [
                'title' => 'Duna',
                'author' => 'Frank Herbert',
                'isbn' => '9788576573135',
                'publisher' => 'Aleph',
                'edition' => '1',
                'year' => '2017',
                'pages' => 680,
                'description' => 'Uma épica aventura de ficção científica sobre o planeta desértico Arrakis.',
                'price' => 89.90,
                'stock' => 15,
                'category_id' => $categories->where('name', 'Ficção Científica')->first()->id,
                'active' => true,
            ],
            [
                'title' => 'Neuromancer',
                'author' => 'William Gibson',
                'isbn' => '9788576572008',
                'publisher' => 'Aleph',
                'edition' => '5',
                'year' => '2016',
                'pages' => 320,
                'description' => 'O romance que definiu o gênero cyberpunk.',
                'price' => 49.90,
                'stock' => 8,
                'category_id' => $categories->where('name', 'Ficção Científica')->first()->id,
                'active' => true,
            ],
            [
                'title' => 'Fundação',
                'author' => 'Isaac Asimov',
                'isbn' => '9788576573401',
                'publisher' => 'Aleph',
                'edition' => '2',
                'year' => '2019',
                'pages' => 320,
                'description' => 'O primeiro livro da aclamada série Fundação.',
                'price' => 54.90,
                'stock' => 12,
                'category_id' => $categories->where('name', 'Ficção Científica')->first()->id,
                'active' => true,
            ],
            
            // Romance
            [
                'title' => 'Orgulho e Preconceito',
                'author' => 'Jane Austen',
                'isbn' => '9788544001820',
                'publisher' => 'Martin Claret',
                'edition' => '1',
                'year' => '2018',
                'pages' => 384,
                'description' => 'Um dos romances mais populares da literatura inglesa.',
                'price' => 39.90,
                'stock' => 20,
                'category_id' => $categories->where('name', 'Romance')->first()->id,
                'active' => true,
            ],
            [
                'title' => 'Cem Anos de Solidão',
                'author' => 'Gabriel García Márquez',
                'isbn' => '9788501112644',
                'publisher' => 'Record',
                'edition' => '102',
                'year' => '2020',
                'pages' => 448,
                'description' => 'A obra-prima do realismo mágico.',
                'price' => 64.90,
                'stock' => 10,
                'category_id' => $categories->where('name', 'Romance')->first()->id,
                'active' => true,
            ],
            
            // Fantasia
            [
                'title' => 'O Senhor dos Anéis: A Sociedade do Anel',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9788595084759',
                'publisher' => 'HarperCollins',
                'edition' => '1',
                'year' => '2019',
                'pages' => 576,
                'description' => 'O primeiro volume da trilogia O Senhor dos Anéis.',
                'price' => 69.90,
                'stock' => 25,
                'category_id' => $categories->where('name', 'Fantasia')->first()->id,
                'active' => true,
            ],
            [
                'title' => 'Harry Potter e a Pedra Filosofal',
                'author' => 'J.K. Rowling',
                'isbn' => '9788532530783',
                'publisher' => 'Rocco',
                'edition' => '1',
                'year' => '2017',
                'pages' => 264,
                'description' => 'O primeiro livro da série Harry Potter.',
                'price' => 44.90,
                'stock' => 30,
                'category_id' => $categories->where('name', 'Fantasia')->first()->id,
                'active' => true,
            ],
            
            // Negócios
            [
                'title' => 'Pai Rico, Pai Pobre',
                'author' => 'Robert T. Kiyosaki',
                'isbn' => '9788550801483',
                'publisher' => 'Alta Books',
                'edition' => '20',
                'year' => '2017',
                'pages' => 336,
                'description' => 'O que os ricos ensinam a seus filhos sobre dinheiro.',
                'price' => 49.90,
                'stock' => 18,
                'category_id' => $categories->where('name', 'Negócios')->first()->id,
                'active' => true,
            ],
            [
                'title' => 'A Startup Enxuta',
                'author' => 'Eric Ries',
                'isbn' => '9788543108834',
                'publisher' => 'Leya',
                'edition' => '1',
                'year' => '2019',
                'pages' => 288,
                'description' => 'Como os empreendedores utilizam a inovação contínua.',
                'price' => 59.90,
                'stock' => 15,
                'category_id' => $categories->where('name', 'Negócios')->first()->id,
                'active' => true,
            ],
            
            // Autoajuda
            [
                'title' => 'O Poder do Hábito',
                'author' => 'Charles Duhigg',
                'isbn' => '9788539004119',
                'publisher' => 'Objetiva',
                'edition' => '1',
                'year' => '2016',
                'pages' => 408,
                'description' => 'Por que fazemos o que fazemos na vida e nos negócios.',
                'price' => 54.90,
                'stock' => 22,
                'category_id' => $categories->where('name', 'Autoajuda')->first()->id,
                'active' => true,
            ],
            [
                'title' => 'Mindset: A Nova Psicologia do Sucesso',
                'author' => 'Carol S. Dweck',
                'isbn' => '9788547000240',
                'publisher' => 'Objetiva',
                'edition' => '1',
                'year' => '2017',
                'pages' => 312,
                'description' => 'Como podemos aprender a atingir nosso potencial.',
                'price' => 49.90,
                'stock' => 14,
                'category_id' => $categories->where('name', 'Autoajuda')->first()->id,
                'active' => true,
            ],
            
            // Biografia
            [
                'title' => 'Steve Jobs: A Biografia',
                'author' => 'Walter Isaacson',
                'isbn' => '9788535921182',
                'publisher' => 'Companhia das Letras',
                'edition' => '1',
                'year' => '2015',
                'pages' => 624,
                'description' => 'A biografia autorizada do fundador da Apple.',
                'price' => 79.90,
                'stock' => 10,
                'category_id' => $categories->where('name', 'Biografia')->first()->id,
                'active' => true,
            ],
            
            // História
            [
                'title' => 'Sapiens: Uma Breve História da Humanidade',
                'author' => 'Yuval Noah Harari',
                'isbn' => '9788525432186',
                'publisher' => 'L&PM',
                'edition' => '1',
                'year' => '2018',
                'pages' => 464,
                'description' => 'De animais insignificantes a senhores do mundo.',
                'price' => 69.90,
                'stock' => 25,
                'category_id' => $categories->where('name', 'História')->first()->id,
                'active' => true,
            ],
            
            // Infantil
            [
                'title' => 'O Pequeno Príncipe',
                'author' => 'Antoine de Saint-Exupéry',
                'isbn' => '9788574063928',
                'publisher' => 'Agir',
                'edition' => '52',
                'year' => '2019',
                'pages' => 96,
                'description' => 'Um clássico da literatura infantil.',
                'price' => 29.90,
                'stock' => 40,
                'category_id' => $categories->where('name', 'Infantil')->first()->id,
                'active' => true,
            ],
        ];
        
        foreach ($books as $book) {
            Book::create($book);
        }
        
        $this->command->info('Livros criados com sucesso!');
    }
}
