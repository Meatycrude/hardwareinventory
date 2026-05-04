<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

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

    public function generateSlug()
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
}
