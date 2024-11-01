<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransferRecordResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransferRecordController extends Controller
{
    /**
     * Get collection of TransferRecord resources
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $request->user()->transferRecords()->latest();

        if ($account = $request->query('account')) {
            $query->where('wallet_accounts.id', $account);
        }

        return TransferRecordResource::collection($query->autoPaginate());
    }
}
