<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestPaddleConnection extends Command
{
    protected $signature = 'paddle:test';
    protected $description = 'Test Paddle API connection and credentials';

    public function handle()
    {
        $this->info('Testing Paddle API connection...');
        $this->newLine();

        // Check if credentials are set
        $apiKey = config('services.paddle.api_key');
        $productId = config('services.paddle.product_id');
        $clientToken = config('services.paddle.client_token');
        $environment = config('services.paddle.environment', 'sandbox');

        $isSandbox = $environment === 'sandbox';
        $apiUrl = $isSandbox ? 'https://sandbox-api.paddle.com' : 'https://api.paddle.com';

        $this->info('Configuration Check:');
        $this->line('Environment: ' . ($isSandbox ? 'Sandbox' : 'Production') . ' (' . $apiUrl . ')');
        $this->line('API Key: ' . ($apiKey ? '✓ Set (' . strlen($apiKey) . ' chars)' : '✗ Not set'));
        $this->line('Product ID: ' . ($productId ? '✓ Set (' . $productId . ')' : '✗ Not set'));
        $this->line('Client Token: ' . ($clientToken ? '✓ Set (' . strlen($clientToken) . ' chars)' : '✗ Not set'));
        $this->newLine();

        if (!$apiKey) {
            $this->error('PADDLE_API_KEY is not set in .env file');
            return 1;
        }

        // Test API connection
        $this->info('Testing API connection...');

        try {
            $response = Http::withToken(trim($apiKey))
                ->withHeaders(['Paddle-Version' => '1'])
                ->get($apiUrl . '/products');

            $this->newLine();
            $this->info('Response Status: ' . $response->status());

            if ($response->successful()) {
                $this->info('✓ API connection successful!');
                $data = $response->json();
                $this->line('Products found: ' . count($data['data'] ?? []));

                if (!empty($data['data'])) {
                    $this->newLine();
                    $this->info('Your Paddle Products:');
                    foreach ($data['data'] as $product) {
                        $this->line('  - ' . $product['name'] . ' (ID: ' . $product['id'] . ')');
                    }
                }
            } else {
                $this->error('✗ API connection failed!');
                $this->line('Response body: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('✗ Exception: ' . $e->getMessage());
            return 1;
        }

        // Test creating a price if product ID is set
        if ($productId) {
            $this->newLine();
            $this->info('Testing price creation with product: ' . $productId);

            try {
                $response = Http::withToken(trim($apiKey))
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Paddle-Version' => '1',
                    ])
                    ->post($apiUrl . '/prices', [
                        'description' => 'Test Price - ' . now()->format('Y-m-d H:i:s'),
                        'product_id' => $productId,
                        'unit_price' => [
                            'amount' => '10000', // $100.00
                            'currency_code' => 'USD',
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->info('✓ Test price created successfully!');
                    $this->line('Price ID: ' . ($data['data']['id'] ?? 'N/A'));
                } else {
                    $this->error('✗ Failed to create test price');
                    $this->line('Status: ' . $response->status());
                    $this->line('Response: ' . $response->body());
                }

            } catch (\Exception $e) {
                $this->error('✗ Exception creating price: ' . $e->getMessage());
            }
        }

        return 0;
    }
}
