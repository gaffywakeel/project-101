<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\StakeResource;
use App\Models\Module;
use App\Models\Stake;
use App\Models\WalletAccount;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class StakeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:stakes'));
    }

    /**
     * Get paginated Stake
     */
    public function paginate(Request $request): AnonymousResourceCollection
    {
        $query = Stake::query()->latest();

        $this->applyFilters($query, $request);

        return StakeResource::collection($query->autoPaginate());
    }

    /**
     * Get statistics
     *
     * @return array
     */
    public function getStatistics()
    {
        $query = Stake::query();

        return [
            'holding' => $query->clone()->whereStatus('holding')->count(),
            'redeemed' => $query->clone()->whereStatus('redeemed')->count(),
            'pending' => $query->clone()->whereStatus('pending')->count(),
            'all' => $query->clone()->count(),
        ];
    }

    /**
     * Redeem Stake
     */
    public function redeem(Stake $stake): StakeResource
    {
        return $stake->acquireLockOrAbort(function (Stake $stake) {
            $this->authorize('redeem', $stake);

            $operator = Module::stake()->operatorFor($stake->walletAccount->user);
            $operatorAccount = $stake->walletAccount->parseTarget($operator);

            return $operatorAccount->acquireLockOrAbort(function (WalletAccount $operatorWalletAccount) use ($stake) {
                $yield = $stake->getYieldObject();

                if ($operatorWalletAccount->getAvailableObject()->lessThan($yield)) {
                    return abort(422, trans('wallet.insufficient_available'));
                }

                return DB::transaction(function () use ($operatorWalletAccount, $stake, $yield) {
                    $stake->update(['status' => 'redeemed']);

                    $redeemerDescription = $stake->getRedemptionDescription($stake->walletAccount->user);
                    $operatorDescription = $stake->getRedemptionDescription($operatorWalletAccount->user);

                    $stake->walletAccount->credit($yield, $redeemerDescription);
                    $operatorWalletAccount->debit($yield, $operatorDescription);

                    return StakeResource::make($stake);
                });
            });
        });
    }

    /**
     * Apply query filters
     */
    protected function applyFilters($query, Request $request): void
    {
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
    }
}
