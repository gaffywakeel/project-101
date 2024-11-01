<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GridResource;
use App\Models\Grid;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GridController extends Controller
{
    /**
     * Paginate grid resource
     */
    public function paginate(): AnonymousResourceCollection
    {
        $records = paginate(Grid::query());

        return GridResource::collection($records);
    }

    /**
     * Enable grid
     *
     * @return void
     */
    public function enable(Grid $grid)
    {
        $grid->update(['status' => true]);
    }

    /**
     * Disable grid
     *
     * @return void
     */
    public function disable(Grid $grid)
    {
        $grid->update(['status' => false]);
    }
}
