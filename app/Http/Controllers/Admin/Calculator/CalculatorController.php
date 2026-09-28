<?php

namespace App\Http\Controllers\Admin\Calculator;

use App\Http\Controllers\Controller;

class CalculatorController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('Ax Calculator')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.calculator.index');
    }
}
