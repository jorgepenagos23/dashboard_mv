<?php

namespace App\Http\Controllers;

use App\Models\ProductoPromocion;
use Illuminate\Http\Request;

class ProductoPromocionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_producto' => 'required|integer',
            'id_promocion' => 'required|integer',
            'mvcoins_extra' => 'required|integer',
        ]);

        ProductoPromocion::create($validated);

        return back()->with('success', 'Producto agregado a promoción exitosamente');
    }

    public function update(Request $request, ProductoPromocion $productos_promocione)
    {
        $validated = $request->validate([
            'id_producto' => 'required|integer',
            'id_promocion' => 'required|integer',
            'mvcoins_extra' => 'required|integer',
        ]);

        $productos_promocione->update($validated);

        return back()->with('success', 'Producto promoción actualizado exitosamente');
    }

    public function destroy(ProductoPromocion $productos_promocione)
    {
        $productos_promocione->delete();
        return back()->with('success', 'Producto quitado de promoción exitosamente');
    }
}
