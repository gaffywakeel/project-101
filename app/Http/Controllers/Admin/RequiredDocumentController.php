<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequiredDocumentResource;
use App\Models\RequiredDocument;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class RequiredDocumentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:settings'));
    }

    /**
     * Paginate required documents
     */
    public function paginate(): AnonymousResourceCollection
    {
        $query = RequiredDocument::latest()->withCount([
            'documents as pending_count' => function (Builder $query) {
                $query->where('status', 'pending');
            },
            'documents as approved_count' => function (Builder $query) {
                $query->where('status', 'approved');
            },
            'documents as rejected_count' => function (Builder $query) {
                $query->where('status', 'rejected');
            },
        ]);

        return RequiredDocumentResource::collection(paginate($query));
    }

    /**
     * Create required document
     *
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $validated = $this->validate($request, [
            'name' => ['required', 'string', 'max:250'],
            'description' => ['required', 'string', 'max:1000'],
        ]);
        RequiredDocument::create($validated);
    }

    /**
     * Update required document
     *
     *
     * @throws ValidationException
     */
    public function update(Request $request, RequiredDocument $document)
    {
        $validated = $this->validate($request, [
            'name' => ['required', 'string', 'max:250'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $document->update($validated);
    }

    /**
     * Delete required document
     */
    public function delete(RequiredDocument $document)
    {
        $document->delete();
    }
}
