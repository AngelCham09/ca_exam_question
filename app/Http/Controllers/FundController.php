<?php

namespace App\Http\Controllers;

use App\Models\Fund;

class FundController extends Controller
{
    public function index()
    {
        return view('fund.index', [
            'funds' => Fund::orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get()
        ]);
    }
}
