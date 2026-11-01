<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductPricingController extends Controller
{
    public function edit()
    {
        clearstatcache();

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(
                config_path('products.php'),
                true
            );
        }

        $products = require config_path('products.php');

        return view(
            'admin.products.pricing',
            compact('products')
        );
    }

    public function update(Request $request)
    {
        $products = require config_path('products.php');

        foreach ($products as $key => &$product) {

            $product['price'] = (int) round(
                $request->input("price.$key") * 100
            );
        }

        $content =
            "<?php\n\nreturn "
            . var_export($products, true)
            . ";\n";

        file_put_contents(
            config_path('products.php'),
            $content
        );

        clearstatcache();

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(
                config_path('products.php'),
                true
            );
        }

        session([
            'price_save_success' => true,
        ]);

        return redirect()
            ->route('admin.products.edit');
    }
}
