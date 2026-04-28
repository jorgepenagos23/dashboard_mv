<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with('direcciones');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cliente', 'like', "%{$search}%")
                  ->orWhere('razon_social', 'like', "%{$search}%")
                  ->orWhere('denominacion_comercial', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ruta')) {
            $ruta = $request->ruta;
            $query->where(function($q) use ($ruta) {
                $q->where('ruta_preventa', 'like', "%{$ruta}%")
                  ->orWhere('ruta_reparto', 'like', "%{$ruta}%");
            });
        }

        if ($request->filled('ctepfr_codigo')) {
            $ctepfr_codigo = $request->ctepfr_codigo;
            $query->whereHas('direcciones', function($q) use ($ctepfr_codigo) {
                $q->where('CTEPFR_CODIGO_K', 'like', "%{$ctepfr_codigo}%");
            });
        }

        $clientes = $query->paginate(15)->withQueryString();

        return Inertia::render('Customers/Index', [
            'clientes' => $clientes,
            'filters' => $request->only(['search', 'ruta', 'ctepfr_codigo'])
        ]);
    }
}
