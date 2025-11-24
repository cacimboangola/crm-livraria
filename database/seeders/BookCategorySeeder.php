<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookCategory;
use Illuminate\Support\Str;

class BookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ficção Científica',
                'description' => 'Livros de ficção científica, futurismo e tecnologia.',
                'active' => true,
            ],
            [
                'name' => 'Romance',
                'description' => 'Livros de romance, drama e relacionamentos.',
                'active' => true,
            ],
            [
                'name' => 'Fantasia',
                'description' => 'Livros de fantasia, mundos mágicos e aventuras épicas.',
                'active' => true,
            ],
            [
                'name' => 'Negócios',
                'description' => 'Livros sobre empreendedorismo, gestão e negócios.',
                'active' => true,
            ],
            [
                'name' => 'Autoajuda',
                'description' => 'Livros de desenvolvimento pessoal e autoconhecimento.',
                'active' => true,
            ],
            [
                'name' => 'Biografia',
                'description' => 'Biografias e autobiografias de personalidades.',
                'active' => true,
            ],
            [
                'name' => 'História',
                'description' => 'Livros sobre eventos históricos e civilizações.',
                'active' => true,
            ],
            [
                'name' => 'Infantil',
                'description' => 'Livros para crianças e jovens leitores.',
                'active' => true,
            ],
        ];

        foreach ($categories as $category) {
            // Gerar slug a partir do nome da categoria
            $category['slug'] = Str::slug($category['name']);
            
            // Verificar se já existe uma categoria com esse slug
            $count = 0;
            $originalSlug = $category['slug'];
            
            while (BookCategory::where('slug', $category['slug'])->exists()) {
                $count++;
                $category['slug'] = $originalSlug . '-' . $count;
            }
            
            BookCategory::create($category);
        }
        
        $this->command->info('Categorias de livros criadas com sucesso!');
    }
}
