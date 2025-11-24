<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BookService
{
    /**
     * Obter todos os livros com paginação
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Book::with('category')
            ->orderBy('title')
            ->paginate($perPage);
    }

    /**
     * Obter todos os livros
     *
     * @param bool $activeOnly
     * @return Collection
     */
    public function getAll(bool $activeOnly = false): Collection
    {
        $query = Book::with('category')->orderBy('title');

        if ($activeOnly) {
            $query->where('active', true);
        }

        return $query->get();
    }

    /**
     * Obter livro por ID
     *
     * @param int $id
     * @return Book|null
     */
    public function getById(int $id): ?Book
    {
        return Book::with('category')->find($id);
    }

    /**
     * Criar novo livro
     *
     * @param array $data
     * @return Book
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * Atualizar livro existente
     *
     * @param int $id
     * @param array $data
     * @return Book|null
     */
    public function update(int $id, array $data): ?Book
    {
        $book = $this->getById($id);

        if ($book) {
            $book->update($data);
            return $book->fresh();
        }

        return null;
    }

    /**
     * Excluir livro
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $book = $this->getById($id);

        if ($book) {
            return $book->delete();
        }

        return false;
    }

    /**
     * Buscar livros
     *
     * @param string $term
     * @return Collection
     */
    public function search(string $term): Collection
    {
        return Book::where('title', 'like', "%{$term}%")
            ->orWhere('author', 'like', "%{$term}%")
            ->orWhere('isbn', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
            ->with('category')
            ->orderBy('title')
            ->get();
    }

    /**
     * Obter livros por categoria
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return Book::where('category_id', $categoryId)
            ->with('category')
            ->orderBy('title')
            ->paginate($perPage);
    }

    /**
     * Atualizar estoque do livro
     *
     * @param int $id
     * @param int $quantity
     * @return Book|null
     */
    public function updateStock(int $id, int $quantity): ?Book
    {
        $book = $this->getById($id);

        if ($book) {
            $book->stock += $quantity;
            $book->save();
            return $book->fresh();
        }

        return null;
    }
}
