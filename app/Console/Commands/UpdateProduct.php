<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPriceChangeNotification;
use Illuminate\Support\Facades\Log;

class UpdateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update {id} {--name=} {--description=} {--price=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a product with the specified details';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $product = Product::find($this->argument('id'));

        if (!$product) {
            $this->error("Product not found.");
            return Command::FAILURE;
        }

        $updatedData = $this->getValidatedInput();

        if (empty($updatedData)) {
            $this->info("No changes provided. Product remains unchanged.");
            return Command::SUCCESS;
        }

        $oldPrice = $product->price;
        $product->fill($updatedData);
        $product->save();

        $this->info("Product updated successfully.");
        if (array_key_exists('price', $updatedData) && $oldPrice != $updatedData['price']) {
            $this->info("Price changed from {$oldPrice} to {$updatedData['price']}.");

            $notificationEmail = env('PRICE_NOTIFICATION_EMAIL', 'hardik@yopmail.com');

            try {
                SendPriceChangeNotification::dispatch(
                    $product,
                    $oldPrice,
                    $updatedData['price'],
                    $notificationEmail
                );
                $this->info("Notification dispatched to {$notificationEmail}.");
            } catch (\Throwable $e) {
                Log::error("Failed to dispatch price notification: " . $e->getMessage());
                $this->error("Failed to send notification.");
            }
        }

        return Command::SUCCESS;
    }

    private function getValidatedInput(): array
    {
        $data = [];

        if ($name = $this->option('name')) {
            if (strlen(trim($name)) < 3) {
                $this->error("Name must be at least 3 characters long.");
                return [];
            }
            $data['name'] = $name;
        }

        if ($description = $this->option('description')) {
            $data['description'] = $description;
        }

        if ($price = $this->option('price')) {
            if (!is_numeric($price) || $price < 0) {
                $this->error("Invalid price provided.");
                return [];
            }
            $data['price'] = $price;
        }

        return $data;
    }
}
