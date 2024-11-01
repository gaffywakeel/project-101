<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileVault;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserAddressResource;
use App\Http\Resources\UserDocumentResource;
use App\Models\UserAddress;
use App\Models\UserDocument;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserVerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:users'));
    }

    /**
     * Get paginated address
     */
    public function addressPaginate(Request $request): AnonymousResourceCollection
    {
        $query = UserAddress::with('user')->latest();

        $this->filterByUser($query, $request);

        return UserAddressResource::collection(paginate($query));
    }

    /**
     * Approve address
     */
    public function approveAddress(UserAddress $address)
    {
        if ($address->status === 'pending') {
            $address->update(['status' => 'approved']);
        }
    }

    /**
     * Reject address
     */
    public function rejectAddress(UserAddress $address)
    {
        $address->update(['status' => 'rejected']);
    }

    /**
     * Download document
     */
    public function downloadDocument(UserDocument $document): StreamedResponse
    {
        $data = collect($document->data);
        $name = pathinfo($path = $data->get('path'), PATHINFO_FILENAME);

        return response()->streamDownload(function () use ($path) {
            echo FileVault::decrypt($path);
        }, "$name.{$data->get('extension')}", [
            'Content-Type' => $data->get('mimeType'),
        ]);
    }

    /**
     * Approve document
     */
    public function approveDocument(UserDocument $document)
    {
        if ($document->status === 'pending') {
            $document->update(['status' => 'approved']);
        }
    }

    /**
     * Reject document
     */
    public function rejectDocument(UserDocument $document)
    {
        $document->update(['status' => 'rejected']);
    }

    /**
     * Get paginated document
     */
    public function documentPaginate(Request $request): AnonymousResourceCollection
    {
        $query = UserDocument::with(['user', 'requirement'])
            ->latest()->whereHas('requirement', function (Builder $query) {
                $query->where('status', true);
            });

        $this->filterByUser($query, $request);

        return UserDocumentResource::collection(paginate($query));
    }

    /**
     * Filter query by user
     */
    protected function filterByUser(Builder $query, Request $request)
    {
        if ($search = $request->get('searchUser')) {
            $query->whereHas('user', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
    }
}
