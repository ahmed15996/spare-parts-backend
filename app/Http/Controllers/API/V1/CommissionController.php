<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\CommissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\StoreClientCommessionRequest;
use App\Http\Requests\API\V1\StoreProviderCommessionRequest;
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
        
        return $this->successResponse([
            'commission_id' => $commission->id,
        ], __('The commission due has been calculated and is :value and please pay it',['value'=>$commission->value]));

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

            $prices = Product::whereIn('id', array_keys($byProduct))->pluck('price', 'id');
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

            return $this->successResponse([
                'commission'=>[
                    'id'=>$commission->id,
                    'value'=> $commission->value
                ]
            ], __('The commission due has been calculated and is :value and please pay it',[ 'value' => $commission->value ]));
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
        $products = $this->productSearchService->search($request->all());
        return $this->successResponse(
            ProductResource::collection($products),
        __('Products fetched successfully'));
    }
}
