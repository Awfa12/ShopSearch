<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">ShopSearch</h1>
        <p class="text-gray-600">Search through 50,000+ products</p>
    </div>

    {{-- Search Bar --}}
    <div class="mb-6">
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="query"
                placeholder="Search products... (try 'shoes', 'laptop', or 'iphoen' for typo tolerance)"
                class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <svg class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <p class="mt-2 text-sm text-gray-500">
            Search happens automatically as you type (300ms delay)
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Filters Sidebar --}}
        <div class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow p-4 sticky top-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Filters</h2>
                    @if($query || $categoryId || $brandId || $minPrice || $maxPrice)
                        <button 
                            wire:click="clearFilters"
                            class="text-sm text-blue-600 hover:text-blue-800"
                        >
                            Clear All
                        </button>
                    @endif
                </div>

                {{-- Category Filter --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select 
                        wire:model.live="categoryId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Brand Filter --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <select 
                        wire:model.live="brandId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Price Range Filter --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                    <div class="flex gap-2">
                        <input 
                            type="number" 
                            wire:model.live.debounce.500ms="minPrice"
                            placeholder="Min"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        >
                        <input 
                            type="number" 
                            wire:model.live.debounce.500ms="maxPrice"
                            placeholder="Max"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="flex-1">
            {{-- Results Count --}}
            @if($products->total() > 0)
                <div class="mb-4 text-gray-600">
                    Showing {{ $products->firstItem() }} - {{ $products->lastItem() }} of {{ $products->total() }} results
                    @if(!empty(trim($query)))
                        <span class="text-sm">for "{{ $query }}"</span>
                    @endif
                </div>
            @endif

            {{-- Loading State --}}
            <div wire:loading class="mb-4">
                <div class="flex items-center text-blue-600">
                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Searching...
                </div>
            </div>

            {{-- Products Grid --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden">
                            {{-- Product Image --}}
                            <div class="aspect-square bg-gray-200 flex items-center justify-center">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                                
                                @if($product->category)
                                    <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                                @endif

                                @if($product->brand)
                                    <p class="text-xs text-gray-400 mb-2">{{ $product->brand->name }}</p>
                                @endif

                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-lg font-bold text-blue-600">€{{ number_format($product->price, 2) }}</span>
                                    @if($product->stock > 0)
                                        <span class="text-xs text-green-600">In Stock</span>
                                    @else
                                        <span class="text-xs text-red-600">Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                {{-- No Results --}}
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                    @if($query || $categoryId || $brandId || $minPrice || $maxPrice)
                        <button 
                            wire:click="clearFilters"
                            class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Clear Filters
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
