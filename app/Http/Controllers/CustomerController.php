<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('direcciones')->paginate(15);

        return Inertia::render('Customers/Index', [
            'clientes' => $clientes
        ]);
    }
}
