<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VacancyController extends Controller
{
    public function index()
    {
        $vacancies = JobVacancy::latest()->paginate(15);
        return Inertia::render('Vacancies/Index', [
            'vacancies' => $vacancies
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        JobVacancy::create($validated);

        return redirect()->back()->with('success', 'Vacante creada exitosamente.');
    }

    public function update(Request $request, string $id)
    {
        $vacancy = JobVacancy::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'location' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $vacancy->update($validated);

        return redirect()->back()->with('success', 'Vacante actualizada exitosamente.');
    }

    public function destroy(string $id)
    {
        $vacancy = JobVacancy::findOrFail($id);
        $vacancy->delete();

        return redirect()->back()->with('success', 'Vacante eliminada exitosamente.');
    }
}
