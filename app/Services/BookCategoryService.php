<?php

namespace App\Services;

use App\Models\BookCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BookCategoryService
{
    /**
     * Obter todas as categorias de livros com paginação
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return BookCategory::orderBy('name')->paginate($perPage);
    }

    /**
     * Obter todas as categorias de livros
     *
     * @param bool $activeOnly
     * @return Collection
     */
    public function getAll(bool $activeOnly = false): Collection
    {
        $query = BookCategory::orderBy('name');
        
        if ($activeOnly) {
            $query->where('active', true);
        }
        
        return $query->get();
    }

    /**
     * Obter categoria de livro por ID
     *
     * @param int $id
     * @return BookCategory|null
     */
    public function getById(int $id): ?BookCategory
    {
        return BookCategory::find($id);
    }

    /**
     * Criar nova categoria de livro
     *
     * @param array $data
     * @return BookCategory
     */
    public function create(array $data): BookCategory
    {
        // Gerar slug se não fornecido
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return BookCategory::create($data);
    }

    /**
     * Atualizar categoria de livro existente
     *
     * @param int $id
     * @param array $data
     * @return BookCategory|null
     */
    public function update(int $id, array $data): ?BookCategory
    {
        $category = $this->getById($id);
        
        if ($category) {
            // Atualizar slug se o nome foi alterado e o slug não foi fornecido
            if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            $category->update($data);
            return $category->fresh();
        }
        
        return null;
    }

    /**
     * Excluir categoria de livro
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $category = $this->getById($id);
        
        if ($category) {
            return $category->delete();
        }
        
        return false;
    }

    /**
     * Buscar categorias de livros
     *
     * @param string $term
     * @return Collection
     */
    public function search(string $term): Collection
    {
        return BookCategory::where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
            ->orderBy('name')
            ->get();
    }
}
