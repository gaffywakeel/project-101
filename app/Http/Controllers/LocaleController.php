<?php

namespace App\Http\Controllers;

use App\Helpers\LocaleManager;
use ArrayObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LocaleController extends Controller
{
    protected LocaleManager $localeManager;

    /**
     * SessionController constructor.
     */
    public function __construct(LocaleManager $localeManager)
    {
        $this->localeManager = $localeManager;
    }

    /**
     * @throws ValidationException
     */
    public function set(Request $request): JsonResponse
    {
        $locales = $this->localeManager->getLocales();

        $validated = $this->validate($request, [
            'locale' => ['required', Rule::in($locales->keys())],
        ]);

        session()->put('locale', $validated['locale']);

        $messages = $this->localeManager->getJsonLines($validated['locale']);

        return response()->json([
            'locale' => $validated['locale'],
            'messages' => new ArrayObject($messages),
        ]);
    }

    /**
     * Get locale messages
     */
    public function get(): JsonResponse
    {
        $locale = session('locale', app()->getLocale());

        $messages = $this->localeManager->getJsonLines($locale);

        return response()->json([
            'messages' => new ArrayObject($messages),
            'locale' => $locale,
        ]);
    }
}
