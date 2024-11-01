<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Helpers\FileVault;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatParticipantResource;
use App\Http\Resources\PeerTradeResource;
use App\Models\PeerTrade;
use App\Models\Rating;
use App\Models\Support\Rateable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Musonza\Chat\Chat;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PeerTradeController extends Controller
{
    /**
     * Get trade
     *
     *
     * @throws AuthorizationException
     */
    public function get(PeerTrade $trade): PeerTradeResource
    {
        $this->authorize('view', $trade);

        return PeerTradeResource::make($trade);
    }

    /**
     * Get participants
     *
     *
     * @throws AuthorizationException
     */
    public function getParticipants(PeerTrade $trade): AnonymousResourceCollection
    {
        $this->authorize('view', $trade);

        return ChatParticipantResource::collection($trade->conversation->getParticipants());
    }

    /**
     * Mark conversation as read
     *
     * @return void
     *
     * @throws AuthorizationException
     */
    public function markRead(PeerTrade $trade)
    {
        $this->authorize('view', $trade);

        app(Chat::class)
            ->conversation($trade->conversation)
            ->setParticipant(Auth::user())
            ->readAll();
    }

    /**
     * Send message to trade conversation
     *
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Exception
     */
    public function sendMessage(PeerTrade $trade, Request $request)
    {
        $this->authorize('sendMessage', $trade);

        $this->validate($request, [
            'message' => 'required|string|min:1|max:2000',
        ]);

        $message = app(Chat::class)
            ->message($request->input('message'))
            ->from(Auth::user())
            ->to($trade->conversation)
            ->send();

        broadcast(new ChatMessageSent($message))->toOthers();

        return ChatMessageResource::make($message);
    }

    /**
     * Upload file
     *
     * @throws AuthorizationException
     * @throws ValidationException|FileNotFoundException
     */
    public function uploadFile(PeerTrade $trade, Request $request)
    {
        $this->authorize('sendMessage', $trade);

        $this->validate($request, [
            'file' => [
                'required', 'file', 'max:5120',
                'mimes:png,jpeg,doc,docx,pdf',
            ],
        ]);

        $file = $request->file('file');

        $data = collect([
            'extension' => $file->clientExtension(),
            'name' => substr($file->getClientOriginalName(), 0, 100),
            'path' => FileVault::encrypt($file->get()),
            'mimeType' => $file->getMimeType(),
        ]);

        $message = app(Chat::class)
            ->message($file->getMimeType())
            ->type('attachment')
            ->data($data->toArray())
            ->from(Auth::user())
            ->to($trade->conversation)
            ->send();

        broadcast(new ChatMessageSent($message))->toOthers();

        return ChatMessageResource::make($message);
    }

    /**
     * Download File
     *
     *
     * @throws AuthorizationException
     */
    public function downloadFile(PeerTrade $trade, $id): StreamedResponse
    {
        $this->authorize('view', $trade);

        $message = $trade->conversation
            ->messages()->where('type', 'attachment')
            ->findOrFail($id);

        $data = collect($message->data);
        $name = pathinfo($path = $data->get('path'), PATHINFO_FILENAME);

        return response()->streamDownload(function () use ($path) {
            echo FileVault::decrypt($path);
        }, "$name.{$data->get('extension')}", [
            'Content-Type' => $data->get('mimeType'),
        ]);
    }

    /**
     * Cancel PeerTrade
     *
     * @return void
     */
    public function cancel(PeerTrade $trade)
    {
        $trade->acquireLockOrAbort(function (PeerTrade $trade) {
            $this->authorize('cancel', $trade);

            $trade->update(['status' => 'canceled']);
        });
    }

    /**
     * Confirm PeerTrade
     *
     * @return void
     */
    public function confirm(PeerTrade $trade)
    {
        $trade->acquireLockOrAbort(function (PeerTrade $trade) {
            $this->authorize('confirm', $trade);

            $trade->update(['confirmed_at' => now()]);
        });
    }

    /**
     * Dispute trade
     *
     * @return void
     */
    public function dispute(PeerTrade $trade)
    {
        $trade->acquireLockOrAbort(function (PeerTrade $trade) {
            $this->authorize('dispute', $trade);

            $trade->update([
                'status' => 'disputed',
                'disputed_by' => $trade->getRole(Auth::user()),
            ]);
        });
    }

    /**
     * Complete trade
     *
     * @return void
     */
    public function complete(PeerTrade $trade)
    {
        $trade->acquireLockOrAbort(function (PeerTrade $trade) {
            $this->authorize('complete', $trade);

            $trade->complete();
        });
    }

    /**
     * Rate seller
     *
     * @return void
     */
    public function rateSeller(Request $request, PeerTrade $trade)
    {
        $trade->acquireLockOrAbort(function (PeerTrade $trade) use ($request) {
            $this->authorize('rateSeller', $trade);
            $this->rate($trade->seller, $trade->sellerRating(), $request);
        });
    }

    /**
     * Rate buyer
     *
     * @return void
     */
    public function rateBuyer(Request $request, PeerTrade $trade)
    {
        $trade->acquireLockOrAbort(function (PeerTrade $trade) use ($request) {
            $this->authorize('rateBuyer', $trade);
            $this->rate($trade->buyer, $trade->buyerRating(), $request);
        });
    }

    /**
     * Rate participant of trade
     *
     *
     * @throws ValidationException
     */
    protected function rate(Rateable $subject, BelongsTo $relation, Request $request): Rating
    {
        $validated = $this->validate($request, [
            'value' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if (!$rating = $relation->first()) {
            $rating = Auth::user()->rate($subject, $validated['value'], $validated['comment']);

            return tap($rating, function ($rating) use ($relation) {
                $relation->associate($rating)->save();
            });
        } else {
            return tap($rating)->update($validated);
        }
    }

    /**
     * Paginate Messages
     *
     *
     * @throws AuthorizationException
     */
    public function messagePaginate(PeerTrade $trade, Request $request): AnonymousResourceCollection
    {
        $this->authorize('view', $trade);

        $messages = app(Chat::class)
            ->conversation($trade->conversation)
            ->setParticipant(Auth::user())
            ->setPaginationParams([
                'page' => $request->input('page'),
                'perPage' => $request->input('itemPerPage'),
                'sorting' => 'desc',
            ])
            ->getMessages();

        return ChatMessageResource::collection($messages);
    }

    /**
     * Get buy statistics
     *
     * @return array
     */
    public function getBuyStatistics()
    {
        $query = Auth::user()->buyPeerTrades();

        return $this->getStatistics($query);
    }

    /**
     * Get sell statistics
     *
     * @return array
     */
    public function getSellStatistics()
    {
        $query = Auth::user()->sellPeerTrades();

        return $this->getStatistics($query);
    }

    /**
     * Get statistics
     */
    protected function getStatistics(Builder $query): array
    {
        return [
            'active' => $query->clone()->whereStatus('active')->count(),
            'completed' => $query->clone()->whereStatus('completed')->count(),
            'canceled' => $query->clone()->whereStatus('canceled')->count(),
            'disputed' => $query->clone()->whereStatus('disputed')->count(),
            'all' => $query->clone()->count(),
        ];
    }

    /**
     * Paginate "buy" trades
     */
    public function buyPaginate(Request $request): AnonymousResourceCollection
    {
        $query = Auth::user()->buyPeerTrades()->latest();

        $this->applyFilters($query, $request);

        return PeerTradeResource::collection(paginate($query));
    }

    /**
     * Paginate "sell" trades
     */
    public function sellPaginate(Request $request): AnonymousResourceCollection
    {
        $query = Auth::user()->sellPeerTrades()->latest();

        $this->applyFilters($query, $request);

        return PeerTradeResource::collection(paginate($query));
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
