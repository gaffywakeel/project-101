<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\UserResource;
use App\Models\Module;
use App\Models\User;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:modules'));
    }

    /**
     * Paginate module records
     */
    public function paginate(): AnonymousResourceCollection
    {
        $records = paginate(Module::query());

        return ModuleResource::collection($records);
    }

    /**
     * Get operators
     */
    public function getOperators(): AnonymousResourceCollection
    {
        $records = User::operator()->latest()->get();

        return UserResource::collection($records);
    }

    /**
     * Enable module
     *
     * @return void
     */
    public function enable(Module $module)
    {
        $module->update(['status' => true]);
    }

    /**
     * Set operator
     *
     * @return void
     */
    public function setOperator(Request $request, Module $module)
    {
        $operator = User::operator()->findOrFail((int) $request->get('operator'));

        $module->operator()->associate($operator)->save();
    }

    /**
     * Disable module
     *
     * @return void
     */
    public function disable(Module $module)
    {
        $module->update(['status' => false]);
    }
}
