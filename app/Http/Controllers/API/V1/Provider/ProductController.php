<?php

namespace App\Http\Controllers\API\V1\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Provider\Product\StoreProductRequest;
use App\Http\Requests\API\V1\Provider\Product\UpdateProductRequest;
use App\Http\Resources\API\V1\Provider\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request)
    {
        $provider = Auth::user()->provider;
        $products = Product::query()->where('provider_id', $provider->id)->latest()->get();

        if($products->isEmpty()){
            return $this->errorResponse(__('No products found'), 404);
        }
        return $this->successResponse(ProductResource::collection($products), __('Products fetched successfully'));
    }

    public function show($id)
    {
        $provider = Auth::user()->provider;
        $product = $this->productService->findWithRelations($id, ['provider']);

        if (!$product || $product->provider_id !== $provider->id) {
            return $this->errorResponse(__('Product not found'), 404);
        }

        return $this->successResponse(
            ProductResource::make($product),
        __('Product fetched successfully'));
    }

    public function store(StoreProductRequest $request)
    {
        $provider = Auth::user()->provider;
        $data = $request->validated();
        $data['provider_id'] = $provider->id;

        $product = $this->productService->createWithBusinessLogic($data);

        return $this->successResponse([
            'id' => $product->id,
        ], __('Product created successfully'));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $provider = Auth::user()->provider;
        $product = $this->productService->findWithRelations($id);

        if (!$product || $product->provider_id !== $provider->id) {
            return $this->errorResponse(__('Product not found'), 404);
        }

        $updated = $this->productService->updateWithBusinessLogic($product, $request->validated());

        if (!$updated) {
            return $this->errorResponse(__('Failed to update product'), 500);
        }

        $product->refresh();

        return $this->successResponse(ProductResource::make($product), __('Product updated successfully'));
    }

    public function destroy($id)
    {
        $provider = Auth::user()->provider;
        $product = $this->productService->findWithRelations($id);

        if (!$product || $product->provider_id !== $provider->id) {
            return $this->errorResponse(__('Product not found'), 404);
        }

        $deleted = $this->productService->delete($product);

        if (!$deleted) {
            return $this->errorResponse(__('Failed to delete product'), 500);
        }

        return $this->successResponse([], __('Product deleted successfully'));
    }
}


