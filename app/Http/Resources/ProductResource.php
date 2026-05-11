<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SupplierResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'sku' => $this->sku,

            'brand' => $this->brand,

            'unit' => $this->unit,

            'buying_price' => $this->buying_price,

            'selling_price' => $this->selling_price,

            'stock_quantity' => $this->stock_quantity,

            'minimum_stock' => $this->minimum_stock,

            'description' => $this->description,

            'category' => new CategoryResource(
                $this->whenLoaded('category')
            ),

            'supplier' => new SupplierResource(
                $this->whenLoaded('supplier')
            ),

            'created_at' => $this->created_at,
        ];
    }
}
