<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClientePromocion;
use App\Models\Promocion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PromocionClienteController extends Controller
{
    public function index(Request $request)
    {
        $promociones = Promocion::where('tipo', '!=', 'GRUPO_PRECIO')
            ->withCount('clientesAsignados as clientes_count')
            ->orderBy('id', 'desc')
            ->get(['id', 'nombre', 'tipo', 'descripcion', 'promo_price', 'fecha_inicio', 'fecha_fin', 'acceso_restringido']);

        $asignaciones = ClientePromocion::with([
                'cliente:id,cliente,razon_social',
                'promocion:id,nombre,tipo,promo_price,acceso_restringido',
            ])
            ->whereHas('promocion', fn($q) => $q->where('tipo', '!=', 'GRUPO_PRECIO'))
            ->orderBy('id', 'desc')
            ->paginate(20, ['*'], 'asig_page')
            ->withQueryString();

        $search = $request->get('buscar_cliente', '');
        $todosClientes = Cliente::when($search, fn($q) =>
                $q->where('cliente', 'like', "%{$search}%")
                  ->orWhere('razon_social', 'like', "%{$search}%")
            )
            ->select('id', 'cliente', 'razon_social')
            ->limit(30)
            ->get();

        return Inertia::render('Promociones/Clientes', [
            'promociones'   => $promociones,
            'asignaciones'  => $asignaciones,
            'todosClientes' => $todosClientes,
            'filters'       => ['buscar_cliente' => $search],
        ]);
    }

    // POST /promociones-clientes/asignar
    public function asignar(Request $request)
    {
        $data = $request->validate([
            'id_cliente'   => 'required|integer|exists:cliente,id',
            'id_promocion' => 'required|integer|exists:promociones,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        ClientePromocion::updateOrCreate(
            ['id_cliente' => $data['id_cliente'], 'id_promocion' => $data['id_promocion']],
            [
                'fecha_inicio' => $data['fecha_inicio'] ?? now()->toDateString(),
                'fecha_fin'    => $data['fecha_fin']    ?? '2030-12-31',
            ]
        );

        return back()->with('success', 'Cliente asignado a la promoción correctamente.');
    }

    // DELETE /promociones-clientes/remover/{id}
    public function remover(int $id)
    {
        ClientePromocion::findOrFail($id)->delete();

        return back()->with('success', 'Asignación eliminada correctamente.');
    }

    // PATCH /promociones-clientes/restringir/{promocion}
    public function toggleRestringida(Request $request, Promocion $promocion)
    {
        $data = $request->validate([
            'acceso_restringido' => 'required|boolean',
        ]);

        $promocion->update(['acceso_restringido' => $data['acceso_restringido']]);

        $estado = $data['acceso_restringido'] ? 'restringida (solo clientes asignados)' : 'pública (todos los clientes)';

        return back()->with('success', "Promoción «{$promocion->nombre}» ahora es {$estado}.");
    }
}
