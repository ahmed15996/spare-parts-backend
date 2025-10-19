<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\CommissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\StoreClientCommessionRequest;
use App\Http\Requests\API\V1\StoreProviderCommessionRequest;
use App\Http\Resources\API\V1\CommissionResource;
use App\Models\Commission;
use App\Models\CommissionProduct;
use App\Models\Product;
use App\Http\Resources\API\V1\Provider\ProductResource;
use App\Services\ProductSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function App\Helpers\setting;

class CommissionController extends Controller
{
    public function __construct(protected ProductSearchService $productSearchService)
    {
    }

    public function clientCommission(StoreClientCommessionRequest $request){
        $amount = $request->validated('amount');
        $user = Auth::user();
        $clientCommissionPercentage = setting('commission','client_commission') ?? 1;

        $commission = Commission::create([
            'amount' => $amount,
            'user_id' => $user->id,
            'type'=>CommissionType::Client,
            'value' => $amount * $clientCommissionPercentage / 100,
        ]);
        
        return $this->successResponse(CommissionResource::make($commission), __('The commission due has been calculated and is :value and please pay it',['value'=>$commission->value]));

    }
    public function providerCommission(StoreProviderCommessionRequest $request){
        $user = Auth::user();
        $items = $request->validated('products');

        return DB::transaction(function () use ($user, $items) {
            $amount = 0;
            $byProduct = [];
            foreach ($items as $item) {
                $pid = (int) ($item['id'] ?? 0);
                $pcs = (int) ($item['pieces'] ?? 0);
                if ($pid > 0 && $pcs > 0) {
                    $byProduct[$pid] = ($byProduct[$pid] ?? 0) + $pcs;
                }
            }

            if (empty($byProduct)) {
                return $this->errorResponse(__('No valid products provided'), 422);
            }

            // Get products with their current stock and prices
            $products = Product::whereIn('id', array_keys($byProduct))->get(['id', 'price', 'stock']);
            $prices = $products->pluck('price', 'id');
            $stocks = $products->pluck('stock', 'id');

            // Check if sufficient stock is available for all products
            foreach ($byProduct as $pid => $pcs) {
                $currentStock = (int) ($stocks[$pid] ?? 0);
                if ($currentStock < $pcs) {
                    $product = $products->firstWhere('id', $pid);
                    $productName = $product ? "Product ID: {$pid}" : "Product ID: {$pid}";
                    return $this->errorResponse(__('Insufficient stock for :product. Available: :available, Requested: :requested', [
                        'product' => $productName,
                        'available' => $currentStock,
                        'requested' => $pcs
                    ]), 422);
                }
            }
            $perProductValue = [];
            foreach ($byProduct as $pid => $pcs) {
                $price = (float) ($prices[$pid] ?? 0);
                $lineAmount = $pcs * $price;
                $amount += $lineAmount;
                $perProductValue[$pid] = $lineAmount; // temporarily store base amount per product
            }

            $pct = (float) (setting('commission','provider_commission') ?? 1);
            $value = round($amount * $pct / 100, 2);

            $commission = Commission::create([
                'type' => CommissionType::Provider,
                'value' => $value,
                'amount' => $amount,
                'payed' => false,
                'user_id' => $user->id,
            ]);

            foreach ($byProduct as $pid => $pcs) {
                $productCommission = round(($perProductValue[$pid] ?? 0) * $pct / 100, 2);
                CommissionProduct::create([
                    'commission_id' => $commission->id,
                    'product_id' => $pid,
                    'pieces' => $pcs,
                    'value' => $productCommission,
                ]);
            }

            // Decrease stock for each product
            foreach ($byProduct as $pid => $pcs) {
                Product::where('id', $pid)->decrement('stock', $pcs);
            }

            return $this->successResponse(CommissionResource::make($commission), __('The commission due has been calculated and is :value and please pay it',[ 'value' => $commission->value ]));
        });
    }
    protected function calcProviderCommission($products){
        $providerCommissionPercentage = setting('commission','provider_commission') ?? 1;
        $commission = 0;
        foreach($products as $product){
            $commission += $product['pieces'] * $product['price'] * $providerCommissionPercentage / 100;
        }
        return $commission;
    }

    public function products(Request $request){
        $data = $request->all();
        $data['provider_id'] = Auth::user()->provider->id;
        $products = $this->productSearchService->search($data);
        return $this->successResponse(
            ProductResource::collection($products),
        __('Products fetched successfully'));
    }

    public function markAsPaid(Request $request, $id){
        $commission = Commission::find($id);
        if(!$commission){
            return $this->errorResponse(__('Commission not found'),404);
        }
        if($commission->user_id !== Auth::user()->id){
            return $this->errorResponse(__('Unauthorized'),403);
        }
        if($commission->payed){
            return $this->errorResponse(__('Commission already paid'),400);
        }
        $commission->update(['payed' => true]);
        return $this->successResponse([],__('Commission marked as paid'),200);
    }
}
