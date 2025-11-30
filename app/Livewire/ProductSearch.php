<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    public function layout()
    {
        return 'components.layouts.app';
    }

    public $query = '';
    public $categoryId = null;
    public $brandId = null;
    public $minPrice = null;
    public $maxPrice = null;

    protected $queryString = [
        'query' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'brandId' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
    ];

    public function updatingQuery()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingBrandId()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->query = '';
        $this->categoryId = null;
        $this->brandId = null;
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->resetPage();
    }

    public function render()
    {
        // Check if we have any search criteria
        $hasSearchCriteria = !empty(trim($this->query)) || $this->categoryId || $this->brandId || $this->minPrice !== null || $this->maxPrice !== null;
        
        // If no query and no filters, show all products using database
        if (!$hasSearchCriteria) {
            // Show all products when no search/filters
            $products = Product::query()
                ->with(['category', 'brand'])
                ->orderBy('id', 'desc')
                ->paginate(24);
        } else {
            // Use Meilisearch with native filter syntax
            try {
                $search = Product::search($this->query ?: '');

                // Build Meilisearch filter array
                $filters = [];
                
                if ($this->categoryId) {
                    $filters[] = 'category_id = ' . (int)$this->categoryId;
                }

                if ($this->brandId) {
                    $filters[] = 'brand_id = ' . (int)$this->brandId;
                }

                if ($this->minPrice !== null) {
                    $filters[] = 'price >= ' . (float)$this->minPrice;
                }

                if ($this->maxPrice !== null) {
                    $filters[] = 'price <= ' . (float)$this->maxPrice;
                }

                // Apply filters using Meilisearch's native filter syntax via options()
                if (!empty($filters)) {
                    $search->options([
                        'filter' => implode(' AND ', $filters),
                    ]);
                }

                $products = $search->paginate(24);
                $products->load(['category', 'brand']);
            } catch (\Exception $e) {
                // If Meilisearch fails, fall back to database query
                \Log::error('Meilisearch error: ' . $e->getMessage());
                
                $products = Product::query()
                    ->when($this->query, function($q) {
                        $q->where('name', 'like', '%' . $this->query . '%')
                          ->orWhere('description', 'like', '%' . $this->query . '%');
                    })
                    ->when($this->categoryId, fn($q) => $q->where('category_id', $this->categoryId))
                    ->when($this->brandId, fn($q) => $q->where('brand_id', $this->brandId))
                    ->when($this->minPrice !== null, fn($q) => $q->where('price', '>=', $this->minPrice))
                    ->when($this->maxPrice !== null, fn($q) => $q->where('price', '<=', $this->maxPrice))
                    ->with(['category', 'brand'])
                    ->orderBy('id', 'desc')
                    ->paginate(24);
            }
        }

        $categories = Category::where('active', true)->orderBy('name')->get();
        $brands = Brand::where('active', true)->orderBy('name')->get();

        return view('livewire.product-search', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
