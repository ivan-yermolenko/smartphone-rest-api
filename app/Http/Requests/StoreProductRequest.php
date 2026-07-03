<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'brand' => ['sometimes', 'string', 'max:255'],
            'sku' => ['sometimes', 'string', 'unique:products,sku', 'max:100'],
            'category' => ['sometimes', 'string', 'max:100'],
            'discount_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'rating' => ['sometimes', 'numeric', 'min:0', 'max:5'],
            'tags' => ['sometimes', 'array'],
            'weight' => ['sometimes', 'numeric', 'min:0'],
            'dimensions' => ['sometimes', 'array'],
            'dimensions.width' => ['required_with:dimensions', 'numeric'],
            'dimensions.height' => ['required_with:dimensions', 'numeric'],
            'dimensions.depth' => ['required_with:dimensions', 'numeric'],
            'warranty_information' => ['sometimes', 'string', 'max:255'],
            'shipping_information' => ['sometimes', 'string', 'max:255'],
            'availability_status' => ['sometimes', 'string', 'max:50'],
            'return_policy' => ['sometimes', 'string', 'max:255'],
            'minimum_order_quantity' => ['sometimes', 'integer', 'min:1'],
            'meta' => ['sometimes', 'array'],
            'reviews' => ['sometimes', 'array'],
            'thumbnail' => ['sometimes', 'string', 'max:500'],
            'images' => ['sometimes', 'array'],
        ];
    }
}
