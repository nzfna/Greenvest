<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SimulationRequest;
use App\Models\SimulationType;
use App\Services\SimulationService;

class SimulationController extends Controller
{
    public function __construct(private SimulationService $simulationService) {}

    public function index()
    {
        $simulationTypes = SimulationType::with('category')->get();
        return view('user.simulation', compact('simulationTypes'));
    }

    public function calculate(SimulationRequest $request)
    {
        $result = $this->simulationService->calculate(
            $request->simulation_type_id,
            (float) $request->initial_amount,
            (int) $request->duration
        );

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }
}