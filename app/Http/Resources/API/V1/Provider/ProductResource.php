<?php

namespace App\Http\Resources\API\V1\Provider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $routeName = optional($request->route())->getName();

        $isProviderProductRoute = $routeName && Str::startsWith($routeName, 'provider.products.') && $request->route()->getName() != 'provider.products.index';

        $isCommissionRoute = $routeName && Str::startsWith($routeName, 'provider.commissions');
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'main_image' => $this->getFirstMediaUrl('products'),
        ];

        // For provider endpoints, include full editable payload
        if ($isProviderProductRoute) {
            unset($data['main_image']);
            $data['description'] = $this->description;
            $data['price'] = $this->price;
            $data['discount_percentage'] = $this->discount_percentage;
            $data['stock'] = $this->stock;
            $data['published'] = (bool) $this->published;
            $data['gallery'] = $this->getMedia('products')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                ];
            })->toArray();
        }
        if($isCommissionRoute){
            unset($data['main_image']);
            $data['description'] = $this->description;
            $data['price'] = $this->price;
            $data['stock'] = $this->stock;
            $data['total_commission'] = $this->totalCommission();
        }


        return $data;
    }
}
