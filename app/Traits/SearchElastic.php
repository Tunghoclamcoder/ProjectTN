<?php
namespace App\Traits;

trait SearchElastic
{
    public function getSearchClient()
    {
        return app('elasticsearch');
    }

    public function indexToElasticsearch()
    {
        $params = [
            'index' => 'products',
            'id' => $this->product_id,
            'body' => [
                'product_id' => $this->product_id,
                'product_name' => $this->product_name,
                'status' => $this->status,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'brand_id' => $this->brand_id,
                'description' => $this->description
            ]
        ];

        return $this->getSearchClient()->index($params);
    }
}
