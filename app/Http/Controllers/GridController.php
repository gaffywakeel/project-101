<?php

namespace App\Http\Controllers;

use App\Models\Grid;
use Illuminate\Http\Request;

class GridController extends Controller
{
    /**
     * Get all grids for page
     *
     * @return Grid[]
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function all(Request $request)
    {
        $data = $this->validate($request, [
            'page' => 'required|exists:grids,page',
        ]);

        return Grid::enabled()->withOrder()
            ->where('page', $data['page'])->get();
    }

    /**
     * Reset dimensions
     *
     * @return void
     */
    public function resetDimensions(Request $request)
    {
        $validated = $this->validate($request, [
            'page' => 'required|exists:grids,page',
        ]);

        $query = Grid::where('page', $validated['page']);

        $query->update(['dimensions' => null]);
    }

    /**
     * Set grid dimensions
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setDimensions(Request $request)
    {
        $validated = $this->validate($request, [
            'page' => 'required|exists:grids,page',
            'dimensions' => 'required|array',
        ]);

        foreach ($validated['dimensions'] as $name => $dimensions) {
            $query = Grid::where('page', $validated['page']);

            if ($grid = $query->where('name', $name)->first()) {
                $grid->update(['dimensions' => $dimensions]);
            }
        }
    }
}
