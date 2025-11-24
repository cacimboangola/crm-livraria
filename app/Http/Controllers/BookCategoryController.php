<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookCategoryService;
use App\Models\BookCategory;

class BookCategoryController extends Controller
{
    protected $bookCategoryService;

    /**
     * Construtor do controlador.
     *
     * @param BookCategoryService $bookCategoryService
     */
    public function __construct(BookCategoryService $bookCategoryService)
    {
        $this->bookCategoryService = $bookCategoryService;
        $this->middleware('auth');
    }

    /**
     * Exibir uma lista de categorias de livros.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->bookCategoryService->getAllPaginated(10);
        return view('book-categories.index', compact('categories'));
    }

    /**
     * Mostrar o formulário para criar uma nova categoria de livro.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('book-categories.create');
    }

    /**
     * Armazenar uma nova categoria de livro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:book_categories,slug',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $category = $this->bookCategoryService->create($validated);

        return redirect()->route('book-categories.show', $category->id)
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Exibir uma categoria de livro específica.
     *
     * @param  \App\Models\BookCategory  $bookCategory
     * @return \Illuminate\Http\Response
     */
    public function show(BookCategory $bookCategory)
    {
        return view('book-categories.show', compact('bookCategory'));
    }

    /**
     * Mostrar o formulário para editar uma categoria de livro.
     *
     * @param  \App\Models\BookCategory  $bookCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(BookCategory $bookCategory)
    {
        return view('book-categories.edit', compact('bookCategory'));
    }

    /**
     * Atualizar uma categoria de livro específica.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BookCategory  $bookCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookCategory $bookCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:book_categories,slug,' . $bookCategory->id,
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $this->bookCategoryService->update($bookCategory->id, $validated);

        return redirect()->route('book-categories.show', $bookCategory->id)
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remover uma categoria de livro específica.
     *
     * @param  \App\Models\BookCategory  $bookCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookCategory $bookCategory)
    {
        // Verificar se existem livros associados a esta categoria
        if ($bookCategory->books()->count() > 0) {
            return redirect()->route('book-categories.index')
                ->with('error', 'Não é possível excluir uma categoria que possui livros associados.');
        }

        $this->bookCategoryService->delete($bookCategory->id);

        return redirect()->route('book-categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }

    /**
     * Buscar categorias de livros.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $term = $request->input('term');
        $categories = $this->bookCategoryService->search($term);

        return view('book-categories.index', compact('categories', 'term'));
    }
}
