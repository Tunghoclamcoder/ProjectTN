<?php
return [
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'localhost:9200')
    ],
    'indices' => [
        'products' => [
            'mappings' => [
                'properties' => [
                    'product_id' => ['type' => 'integer'],
                    'product_name' => [
                        'type' => 'text',
                        'analyzer' => 'standard',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword'
                            ]
                        ]
                    ],
                    'status' => ['type' => 'keyword'],
                    'quantity' => ['type' => 'integer'],
                    'price' => ['type' => 'float'],
                    'created_at' => ['type' => 'date']
                ]
            ]
        ]
    ]
];
