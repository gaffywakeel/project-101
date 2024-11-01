<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransferRecordResource;
use App\Models\TransferRecord;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransferRecordController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:wallets'));
    }

    /**
     * Paginate transfer records
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $query = TransferRecord::query()->latest();

        $this->filterByUser($query, $request);

        return TransferRecordResource::collection(paginate($query));
    }

    /**
     * Remove transfer record
     *
     * @return void
     */
    public function remove(TransferRecord $record)
    {
        $record->acquireLock(function (TransferRecord $record) {
            if ($record->isRemovable()) {
                return $record->delete();
            }
        });
    }

    /**
     * Filter query by user
     */
    protected function filterByUser(Builder $query, Request $request)
    {
        if ($search = $request->get('searchUser')) {
            $query->whereHas('walletAccount.user', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        }
    }
}
