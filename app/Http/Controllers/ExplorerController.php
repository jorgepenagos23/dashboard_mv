<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ExplorerController extends Controller
{
    private $allowedTables = [
        'Clienteenca', 'cliente', 'clientes_promociones', 'direcciones', 'empleados', 'fcm_tokens',
        'historial_promociones', 'pedido_producto', 'pedidos', 'price_lists', 'privacy_policies',
        'product_prices', 'producto_combos', 'producto_combos_items', 'producto_promociones_regalo',
        'productos', 'productos_promociones', 'promocion_regalo_costo0', 'promociones', 'stock'
    ];

    public function index(Request $request, $table = 'cliente')
    {
        if (!in_array($table, $this->allowedTables)) {
            abort(404, 'Tabla no permitida.');
        }

        $columns = Schema::connection('mysql')->getColumnListing($table);
        
        // Paginate data, sort by the first column descending to show newest first generally
        $primaryKey = $this->getPrimaryKey($table) ?? $columns[0];
        
        $query = DB::connection('mysql')->table($table);

        $search = $request->input('search');
        if ($search) {
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        $data = $query->orderBy($primaryKey, 'desc')
                  ->paginate(15)
                  ->withQueryString();

        return Inertia::render('Explorer/Index', [
            'currentTable' => $table,
            'tables' => $this->allowedTables,
            'columns' => $columns,
            'tableData' => $data,
            'primaryKey' => $primaryKey,
            'filters' => $request->only(['search'])
        ]);
    }

    public function update(Request $request, $table)
    {
        if (!in_array($table, $this->allowedTables)) {
            abort(403, 'Tabla no permitida.');
        }

        $original = $request->input('original');
        $changes = $request->input('changes');

        if (!$original || empty($changes)) {
            return back()->with('error', 'No hay datos para actualizar.');
        }

        $query = DB::connection('mysql')->table($table);
        $primaryKey = $this->getPrimaryKey($table);

        // Build WHERE clause to find the exact row
        if ($primaryKey && isset($original[$primaryKey])) {
            $query->where($primaryKey, $original[$primaryKey]);
        } else {
            // If composite or no PK, match by all original values
            foreach ($original as $col => $val) {
                if ($val === null) {
                    $query->whereNull($col);
                } else {
                    $query->where($col, $val);
                }
            }
        }

        try {
            $query->update($changes);
            return back()->with('success', 'Registro actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    private function getPrimaryKey($table)
    {
        $keys = DB::connection('mysql')->select("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
        if (count($keys) === 1) {
            return $keys[0]->Column_name;
        }
        return null; // For tables with composite primary keys like pedido_producto, fallback to match-all logic
    }
}
