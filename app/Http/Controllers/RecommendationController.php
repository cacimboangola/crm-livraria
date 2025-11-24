<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendationService;
use App\Models\Customer;
use App\Models\Book;

class RecommendationController extends Controller
{
    protected $recommendationService;

    /**
     * Construtor.
     *
     * @param  \App\Services\RecommendationService  $recommendationService
     * @return void
     */
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Exibe recomendações para um cliente específico.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function forCustomer(Customer $customer)
    {
        $recommendations = $this->recommendationService->getRecommendationsForCustomer($customer->id);
        
        if (request()->ajax()) {
            return response()->json([
                'recommendations' => $recommendations
            ]);
        }
        
        return view('recommendations.customer', compact('customer', 'recommendations'));
    }

    /**
     * Exibe livros similares a um livro específico.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function similarBooks(Book $book)
    {
        $similarBooks = $this->recommendationService->getSimilarBooks($book->id);
        
        if (request()->ajax()) {
            return response()->json([
                'similar_books' => $similarBooks
            ]);
        }
        
        return view('recommendations.similar', compact('book', 'similarBooks'));
    }

    /**
     * Exibe clientes potenciais para um livro específico.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function potentialCustomers(Book $book)
    {
        $potentialCustomers = $this->recommendationService->getPotentialCustomersForBook($book->id);
        
        if (request()->ajax()) {
            return response()->json([
                'potential_customers' => $potentialCustomers
            ]);
        }
        
        return view('recommendations.potential-customers', compact('book', 'potentialCustomers'));
    }

    /**
     * Exibe os livros mais populares.
     *
     * @return \Illuminate\Http\Response
     */
    public function popularBooks()
    {
        $popularBooks = $this->recommendationService->getPopularBooks(10);
        
        if (request()->ajax()) {
            return response()->json([
                'popular_books' => $popularBooks
            ]);
        }
        
        return view('recommendations.popular', compact('popularBooks'));
    }
}
