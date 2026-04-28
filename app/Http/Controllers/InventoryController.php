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

    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:productos,id',
            'stocks' => 'array',
            'stocks.*.lugar' => 'required|string|max:20',
            'stocks.*.cantidad' => 'required|numeric|min:0',
        ]);

        $product = Producto::findOrFail($request->product_id);

        if ($request->has('stocks')) {
            foreach ($request->stocks as $stockData) {
                if (trim($stockData['lugar']) === '') continue;

                $stock = \App\Models\Stock::firstOrNew([
                    'product_id' => $product->id,
                    'lugar' => $stockData['lugar'],
                ]);

                $stock->cantidad = $stockData['cantidad'];
                $stock->yfr_disponible = $stockData['cantidad'];
                $stock->invpro_existencia = $stockData['cantidad'];
                
                $stock->yfr_disponiblepza = $stock->yfr_disponiblepza ?? 0;
                $stock->invpro_existenciapza = $stock->invpro_existenciapza ?? 0;

                $stock->save();
            }
        }

        return redirect()->back();
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:productos,id',
            'precio_1' => 'required|numeric|min:0',
            'extra_prices' => 'array',
            'extra_prices.*.price_list_id' => 'required|integer',
            'extra_prices.*.price' => 'required|numeric|min:0',
        ]);

        $product = Producto::with('prices')->findOrFail($request->product_id);
        
        // Update base price
        $product->precio_1 = $request->precio_1;
        $product->save();

        // Update extra prices
        if ($request->has('extra_prices')) {
            foreach ($request->extra_prices as $extraPriceData) {
                $priceRecord = $product->prices->where('price_list_id', $extraPriceData['price_list_id'])->first();
                if ($priceRecord) {
                    $priceRecord->price = $extraPriceData['price'];
                    $priceRecord->save();
                } else {
                    $product->prices()->create([
                        'price_list_id' => $extraPriceData['price_list_id'],
                        'price' => $extraPriceData['price']
                    ]);
                }
            }
        }

        return redirect()->back();
    }

    public function updateDetails(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'foto' => 'nullable|url',
        ]);

        $producto->nombre = $validated['nombre'];
        $producto->sku = $validated['sku'];
        $producto->foto = $validated['foto'] ?? $producto->foto;
        $producto->save();

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:productos,sku',
            'foto' => 'nullable|url',
            'precio_1' => 'required|numeric|min:0',
            'linea_prod_id' => 'nullable|string|max:50',
            'pround' => 'nullable|string|max:20',
            'nombre_corto' => 'nullable|string|max:100',
            'marca_prod_id' => 'nullable|string|max:50',
            'sublinea_prod_id' => 'nullable|string|max:50',
            'presentacion_prod_id' => 'nullable|string|max:50',
            'AAI' => 'nullable|string|max:10',
            'iva16' => 'nullable|numeric|min:0',
            'ieps' => 'nullable|numeric|min:0',
            'vigencia' => 'nullable|date',
            'proveedor_desc1' => 'nullable|string|max:255',
            'PRODUC_FACTCONVER3' => 'nullable|numeric',
            'PRODUC_PZAMINVTA' => 'nullable|integer',
            'VTAPRE_PRECIO' => 'nullable|numeric',
            'MULTIMARCA' => 'nullable|string|max:100',
        ]);

        $producto = new Producto();
        $producto->nombre = $validated['nombre'];
        $producto->sku = $validated['sku'];
        $producto->foto = $validated['foto'] ?? null;
        $producto->precio_1 = $validated['precio_1'];
        $producto->linea_prod_id = $validated['linea_prod_id'] ?? null;
        
        $producto->pround = $validated['pround'] ?? null;
        $producto->nombre_corto = $validated['nombre_corto'] ?? null;
        $producto->marca_prod_id = $validated['marca_prod_id'] ?? null;
        $producto->sublinea_prod_id = $validated['sublinea_prod_id'] ?? null;
        $producto->presentacion_prod_id = $validated['presentacion_prod_id'] ?? null;
        $producto->AAI = $validated['AAI'] ?? '0.00';
        $producto->iva16 = $validated['iva16'] ?? 0;
        $producto->ieps = $validated['ieps'] ?? 0;
        $producto->vigencia = $validated['vigencia'] ?? now();
        $producto->proveedor_desc1 = $validated['proveedor_desc1'] ?? null;
        $producto->PRODUC_FACTCONVER3 = $validated['PRODUC_FACTCONVER3'] ?? 0;
        $producto->PRODUC_PZAMINVTA = $validated['PRODUC_PZAMINVTA'] ?? 1;
        $producto->VTAPRE_PRECIO = $validated['VTAPRE_PRECIO'] ?? null;
        $producto->MULTIMARCA = $validated['MULTIMARCA'] ?? null;

        $producto->save();

        return redirect()->back();
    }

    public function destroy($id)
    {
        if (auth()->user()->email !== 'jorgepenagos50@gmail.com') {
            abort(403, 'Solo el usuario principal puede eliminar productos permanentemente.');
        }

        $producto = Producto::findOrFail($id);
        
        // Delete related records (assuming DB constraints don't cascade, or just to be safe)
        $producto->stocks()->delete();
        $producto->prices()->delete();
        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado exitosamente.');
    }
}
