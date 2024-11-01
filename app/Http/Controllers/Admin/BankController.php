<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BankController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(Authorize::using('manage:banks'));
    }

    /**
     * Paginate bank
     */
    public function paginate(): AnonymousResourceCollection
    {
        $records = paginate(Bank::latest()->with('operatingCountries'));

        return BankResource::collection($records);
    }

    /**
     * Create bank
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $validated = $this->validate($request, [
            'name' => ['required', 'string', 'max:250', 'unique:banks'],
            'operating_countries' => ['required', 'array'],
            'operating_countries.*' => ['required', 'exists:operating_countries,code'],
        ]);

        $bank = Bank::create(['name' => $validated['name']]);

        $bank->operatingCountries()->sync($validated['operating_countries']);
    }

    /**
     * Update bank
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Bank $bank)
    {
        $validated = $this->validate($request, [
            'operating_countries' => ['required', 'array'],
            'operating_countries.*' => ['required', 'exists:operating_countries,code'],
            'name' => ['required', 'string', 'max:250', $bank->getUniqueRule()],
        ]);

        $bank->update(['name' => $validated['name']]);

        $bank->operatingCountries()->sync($validated['operating_countries']);
    }

    /**
     * Delete bank
     */
    public function delete(Bank $bank)
    {
        $bank->delete();
    }

    /**
     * Upload logo
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function setLogo(Request $request, Bank $bank)
    {
        $this->validate($request, [
            'file' => 'required|mimetypes:image/png,image/jpeg|dimensions:ratio=1|file|max:100',
        ]);

        $file = $request->file('file');
        $logo = savePublicFile($file, $bank->path());
        $bank->update(['logo' => $logo]);
    }

    /**
     * Get operating banks
     */
    public function getOperatingBanks(): AnonymousResourceCollection
    {
        $banks = Bank::latest()->has('operatingCountries')
            ->with('operatingCountries')->get();

        return BankResource::collection($banks);
    }
}
