<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;
    protected $fillable = ['name', 'slug', 'description', 'price', 'category_id', 'brand_id', 'attributes', 'stock', 'image_url'];

    protected $casts = [
        'price' => 'decimal:2',
        'attributes' => 'array',
        'stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the indexable data array for the model.
     * This defines which fields Meilisearch will index.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'category_name' => $this->category->name ?? null,
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand->name ?? null,
            'attributes' => $this->attributes,
            'stock' => $this->stock,
            'slug' => $this->slug,
        ];
    }

    /**
     * Get the filterable attributes for Meilisearch.
     * These can be used for filtering search results.
     */
    public function getScoutFilterableAttributes(): array
    {
        return [
            'category_id',
            'brand_id',
            'price',
            'stock',
        ];
    }
}
