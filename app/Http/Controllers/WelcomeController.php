<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function welcome()
    {
        $products = Product::where('active', 1)
            ->get()
            ->keyBy('slug');

        $title = 'FlowState Desks | Premium Studio Desks & Music Production Workstations';

        $description = 'Discover premium studio desks built for music producers, recording engineers, composers, and creators. The Flow Pro Studio Desk features 18U rack space, professional cable management, and a heavy-duty keyboard tray designed for recording, mixing, mastering, and music production.';

        $keywords = 'studio desk, music production desk, recording studio desk, producer workstation, mixing desk, mastering desk, MIDI controller desk, synth workstation, studio furniture, professional studio desk, rack mount desk, home recording studio furniture, FlowState Desks';

        return view('welcome', compact(
            'products',
            'title',
            'description',
            'keywords'
        ));
    }
}
