<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\WebhookClient\Models\WebhookCall;

class WebhookController extends Controller
{
    public function index()
    {
        $webhooks = WebhookCall::latest()->paginate(50);

        return view('admin.webhooks.index', compact('webhooks'));
    }
}
