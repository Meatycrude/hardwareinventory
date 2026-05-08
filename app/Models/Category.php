<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->generateSlug();
        });

        static::updating(function ($category) {

            if ($category->isDirty('name')) {
                $category->generateSlug();
            }

        });
    }

    public function generateSlug(): void
    {
        $slug = Str::slug($this->name);

        $originalSlug = $slug;

        $counter = 1;

        $query = self::where('slug', $slug);

        if ($this->id) {
            $query->where('id', '!=', $this->id);
        }

        while ($query->exists()) {

            $slug = $originalSlug.'-'.$counter;

            $counter++;

            $query = self::where('slug', $slug);

            if ($this->id) {
                $query->where('id', '!=', $this->id);
            }
        }

        $this->slug = $slug;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
