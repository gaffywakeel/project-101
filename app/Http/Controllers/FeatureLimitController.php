<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeatureLimitResource;
use App\Models\FeatureLimit;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureLimitController extends Controller
{
    /**
     * Get all feature limit
     */
    public function all(): AnonymousResourceCollection
    {
        return FeatureLimitResource::collection(FeatureLimit::all());
    }
}
