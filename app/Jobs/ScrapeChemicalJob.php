<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ScrapeChemicalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Handle the job to scrape product data.
     */
    public function handle()
    {
        $client = new Client();
        $url = 'https://www.echemi.com/wholesale/pharmaceutical-raw-materials.html?page=25';

        try {
            // Fetch the HTML content
            $response = $client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                ],
            ]);

            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            Log::info('Scraper started successfully.');

            // Scrape product data
            $products = $crawler->filter('li')->each(function (Crawler $node) {
                $product = [];

                $product['name'] = $node->filter('h3.name')->count()
                    ? trim($node->filter('h3.name')->text())
                    : 'N/A';

                $product['image_url'] = $node->filter('img')->count()
                    ? $this->getFullImageUrl($node->filter('img')->attr('src'))
                    : 'N/A';

                // Scrape Price (Unit Price)
            $product['price'] = $node->filter('p.price')->count()
            ? trim($node->filter('p.price')->text())
            : 'N/A';

        // Clean the price to extract numeric value (e.g., remove non-numeric characters)
        if ($product['price'] !== 'N/A') {
            $product['price'] = preg_replace('/[^0-9.]/', '', $product['price']);
        }

        // Ensure the price is a valid number or set to '0' if it's invalid
        if (!is_numeric($product['price']) || empty($product['price'])) {
            $product['price'] = '0';
        }

           

                $product['price_valid'] = $node->filter('div.price_valid')->count()
                    ? trim($node->filter('div.price_valid')->text())
                    : 'N/A';
 // Scrape Package Information
 $product['package'] = $node->filter('p.package')->count()
 ? trim($node->filter('p.package')->text())
 : 'N/A';

// Scrape Product Link
$product['link'] = $node->filter('a')->count()
 ? $node->filter('a')->attr('href')
 : 'N/A';

// Scrape Supplier Name
$product['supplier_name'] = $node->filter('div.supplier_name a')->count()
 ? trim($node->filter('div.supplier_name a')->text())
 : 'N/A';

// Scrape Supplier Country
$product['supplier_country'] = $node->filter('div.supplier_info div.items[title="China"]')->count()
 ? trim($node->filter('div.supplier_info div.items[title="China"]')->text())
 : 'N/A';

// Scrape Supplier Type
$product['supplier_type'] = $node->filter('div.supplier_info div.items[title="Trader"]')->count()
 ? trim($node->filter('div.supplier_info div.items[title="Trader"]')->text())
 : 'N/A';


                return $product;
            });

            Log::info('Products fetched: ' . json_encode($products));

            if (empty($products)) {
                Log::warning('No products were fetched from the website.');
                return;
            }

            foreach ($products as $product) {
                if (!empty($product['name']) && $product['name'] !== 'N/A') {
                    Product::updateOrCreate(
                        ['name' => $product['name']],
                        [
                            'price' => $product['price'],
                            'package' => $product['package'],
                            'link' => $product['link'],
                            'supplier_name' => $product['supplier_name'],
                            'supplier_country' => $product['supplier_country'],
                            'supplier_type' => $product['supplier_type'],
                            'image_url' => $product['image_url'],
                        ]
                    );
                    Log::info('Inserted Product: ' . $product['name']);
                } else {
                    Log::warning('Skipped Product due to missing or invalid name: ' . json_encode($product));
                }
            }

            Log::info('Scraper job completed successfully.');
        } catch (\Exception $e) {
            Log::error('Scraping error: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to handle relative URLs and return full URLs.
     *
     * @param string $url
     * @return string
     */
    private function getFullImageUrl(string $url): string
    {
        $baseDomain = 'https://www.echemi.com';
        if (strpos($url, 'http') === 0) {
            return $url;
        }
        return rtrim($baseDomain, '/') . '/' . ltrim($url, '/');
    }
}
