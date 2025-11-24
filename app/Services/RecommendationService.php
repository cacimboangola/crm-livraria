<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Obtém recomendações de livros para um cliente específico.
     *
     * @param  int  $customerId
     * @param  int  $limit
     * @return Collection
     */
    public function getRecommendationsForCustomer(int $customerId, int $limit = 5): Collection
    {
        // 1. Encontrar categorias de livros que o cliente já comprou
        $customerCategories = $this->getCustomerPreferredCategories($customerId);
        
        if ($customerCategories->isEmpty()) {
            // Se o cliente não tem histórico, retornar livros populares
            return $this->getPopularBooks($limit);
        }
        
        // 2. Encontrar livros dessas categorias que o cliente ainda não comprou
        $purchasedBookIds = $this->getCustomerPurchasedBookIds($customerId);
        
        $recommendations = Book::whereIn('category_id', $customerCategories->pluck('category_id'))
            ->whereNotIn('id', $purchasedBookIds)
            ->where('active', true)
            ->where('stock', '>', 0)
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
            
        // 3. Se não houver recomendações suficientes, complementar com livros populares
        if ($recommendations->count() < $limit) {
            $popularBooks = $this->getPopularBooks($limit - $recommendations->count(), $purchasedBookIds);
            $recommendations = $recommendations->merge($popularBooks);
        }
        
        return $recommendations;
    }
    
    /**
     * Obtém as categorias preferidas de um cliente com base no histórico de compras.
     *
     * @param  int  $customerId
     * @return Collection
     */
    protected function getCustomerPreferredCategories(int $customerId): Collection
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('books', 'invoice_items.book_id', '=', 'books.id')
            ->where('invoices.customer_id', $customerId)
            ->where('invoices.status', 'paid')
            ->select('books.category_id', DB::raw('COUNT(*) as count'))
            ->groupBy('books.category_id')
            ->orderBy('count', 'desc')
            ->get();
    }
    
    /**
     * Obtém os IDs dos livros que um cliente já comprou.
     *
     * @param  int  $customerId
     * @return array
     */
    protected function getCustomerPurchasedBookIds(int $customerId): array
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoices.customer_id', $customerId)
            ->where('invoices.status', 'paid')
            ->pluck('invoice_items.book_id')
            ->unique()
            ->toArray();
    }
    
    /**
     * Obtém os livros mais populares (mais vendidos).
     *
     * @param  int  $limit
     * @param  array  $excludeBookIds
     * @return Collection
     */
    public function getPopularBooks(int $limit = 5, array $excludeBookIds = []): Collection
    {
        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('books', 'invoice_items.book_id', '=', 'books.id')
            ->where('invoices.status', 'paid')
            ->whereNotIn('books.id', $excludeBookIds)
            ->where('books.active', true)
            ->where('books.stock', '>', 0)
            ->select('books.*', DB::raw('SUM(invoice_items.quantity) as total_sold'))
            ->groupBy('books.id')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return Book::find($item->id);
            });
    }
    
    /**
     * Obtém livros similares a um livro específico.
     *
     * @param  int  $bookId
     * @param  int  $limit
     * @return Collection
     */
    public function getSimilarBooks(int $bookId, int $limit = 5): Collection
    {
        $book = Book::findOrFail($bookId);
        
        // Livros da mesma categoria e mesmo autor
        $similarBooks = Book::where('id', '!=', $bookId)
            ->where('category_id', $book->category_id)
            ->where('author', $book->author)
            ->where('active', true)
            ->where('stock', '>', 0)
            ->limit($limit)
            ->get();
            
        // Se não houver livros suficientes, adicionar livros da mesma categoria
        if ($similarBooks->count() < $limit) {
            $categoryBooks = Book::where('id', '!=', $bookId)
                ->where('category_id', $book->category_id)
                ->where('author', '!=', $book->author)
                ->where('active', true)
                ->where('stock', '>', 0)
                ->limit($limit - $similarBooks->count())
                ->get();
                
            $similarBooks = $similarBooks->merge($categoryBooks);
        }
        
        return $similarBooks;
    }
    
    /**
     * Obtém clientes que podem estar interessados em um livro específico.
     *
     * @param  int  $bookId
     * @param  int  $limit
     * @return Collection
     */
    public function getPotentialCustomersForBook(int $bookId, int $limit = 10): Collection
    {
        $book = Book::findOrFail($bookId);
        
        // Clientes que compraram livros da mesma categoria
        $customerIds = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('books', 'invoice_items.book_id', '=', 'books.id')
            ->where('books.category_id', $book->category_id)
            ->where('books.id', '!=', $bookId)
            ->where('invoices.status', 'paid')
            ->select('invoices.customer_id', DB::raw('COUNT(*) as count'))
            ->groupBy('invoices.customer_id')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->pluck('customer_id')
            ->toArray();
            
        // Excluir clientes que já compraram este livro
        $customersWithBook = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->where('invoice_items.book_id', $bookId)
            ->where('invoices.status', 'paid')
            ->pluck('invoices.customer_id')
            ->toArray();
            
        $potentialCustomerIds = array_diff($customerIds, $customersWithBook);
        
        return Customer::whereIn('id', $potentialCustomerIds)
            ->where('active', true)
            ->limit($limit)
            ->get();
    }
}
