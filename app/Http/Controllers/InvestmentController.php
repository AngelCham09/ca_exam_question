<?php

namespace App\Http\Controllers;

use App\Models\Investment;

class InvestmentController extends Controller
{
    public function index()
    {
        return view('investment.index', [
            'investments' => Investment::with(['investor', 'fund'])->orderBy('start_date', 'desc')->get()
        ]);
    }
}
