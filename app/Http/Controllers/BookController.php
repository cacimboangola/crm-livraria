<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\BookCategoryService;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    protected $bookService;
    protected $bookCategoryService;

    /**
     * Construtor do controlador.
     *
     * @param BookService $bookService
     * @param BookCategoryService $bookCategoryService
     */
    public function __construct(BookService $bookService, BookCategoryService $bookCategoryService)
    {
        $this->bookService = $bookService;
        $this->bookCategoryService = $bookCategoryService;
        $this->middleware('auth');
    }

    /**
     * Exibir uma lista de livros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = $this->bookService->getAllPaginated(10);
        $categories = $this->bookCategoryService->getAll(true);
        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Mostrar o formulário para criar um novo livro.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->bookCategoryService->getAll(true);
        return view('books.create', compact('categories'));
    }

    /**
     * Armazenar um novo livro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:book_categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'language' => 'nullable|string|max:50',
            'cover_image' => 'nullable|image|max:2048',  // max 2MB
            'active' => 'boolean',
        ]);

        // Processar imagem da capa, se fornecida
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $book = $this->bookService->create($validated);

        return redirect()
            ->route('books.show', $book->id)
            ->with('success', 'Livro criado com sucesso!');
    }

    /**
     * Exibir um livro específico.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    /**
     * Mostrar o formulário para editar um livro.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        $categories = $this->bookCategoryService->getAll(true);
        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Atualizar um livro específico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:book_categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'language' => 'nullable|string|max:50',
            'cover_image' => 'nullable|image|max:2048',  // max 2MB
            'active' => 'boolean',
        ]);

        // Processar imagem da capa, se fornecida
        if ($request->hasFile('cover_image')) {
            // Remover imagem antiga, se existir
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        $this->bookService->update($book->id, $validated);

        return redirect()
            ->route('books.show', $book->id)
            ->with('success', 'Livro atualizado com sucesso!');
    }

    /**
     * Remover um livro específico.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        // Verificar se o livro está associado a algum item de fatura
        if ($book->invoiceItems()->count() > 0) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Não é possível excluir um livro que está associado a faturas.');
        }

        // Remover imagem da capa, se existir
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $this->bookService->delete($book->id);

        return redirect()
            ->route('books.index')
            ->with('success', 'Livro removido com sucesso!');
    }

    /**
     * Buscar livros.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $term = $request->input('term');
        $books = $this->bookService->search($term);

        return view('books.index', compact('books', 'term'));
    }

    /**
     * Exibir livros por categoria.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\Response
     */
    public function byCategory($categoryId)
    {
        $category = $this->bookCategoryService->getById($categoryId);
        $books = $this->bookService->getByCategory($categoryId, 10);

        return view('books.by-category', compact('books', 'category'));
    }

    /**
     * Atualizar estoque do livro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function updateStock(Request $request, Book $book)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
        ]);

        $this->bookService->updateStock($book->id, $validated['quantity']);

        return redirect()
            ->route('books.show', $book->id)
            ->with('success', 'Estoque atualizado com sucesso!');
    }
}
