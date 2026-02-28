<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['stocks', 'prices']);

        if ($request->filled('linea_prod_id')) {
            $query->where('linea_prod_id', $request->linea_prod_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%");
            });
        }

        $productos = $query->paginate(15)->withQueryString();
        $lineas = Producto::whereNotNull('linea_prod_id')->distinct()->pluck('linea_prod_id');

        return Inertia::render('Inventory/Index', [
            'productos' => $productos,
            'lineas' => $lineas,
            'filters' => $request->only(['search', 'linea_prod_id'])
        ]);
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:productos,id',
            'lugar' => 'required|string|max:20',
            'cantidad' => 'required|numeric|min:1',
        ]);

        $stock = \App\Models\Stock::firstOrNew([
            'product_id' => $request->product_id,
            'lugar' => $request->lugar,
        ]);

        $stock->cantidad = ($stock->cantidad ?? 0) + $request->cantidad;
        $stock->yfr_disponible = ($stock->yfr_disponible ?? 0) + $request->cantidad;
        $stock->invpro_existencia = ($stock->invpro_existencia ?? 0) + $request->cantidad;
        
        $stock->yfr_disponiblepza = $stock->yfr_disponiblepza ?? 0;
        $stock->invpro_existenciapza = $stock->invpro_existenciapza ?? 0;

        $stock->save();

        return redirect()->back();
    }
}
