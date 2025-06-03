<?php

namespace App\Http\Controllers;

use App\Repositories\Provider\ProviderRepositoryInterface as Provider;
use App\Http\Requests\ProviderRequest;

class ProviderController extends Controller
{
    private $provider;

    function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function index()
    {
        $data['providers'] = $this->provider->paginate(25);
        return view('providers.index', $data);
    }

    public function create()
    {
        return view('providers.create');
    }

    public function store(ProviderRequest $request)
    {
        $input = $request->all();
        $this->provider->create($input);
        return redirect()->route('providers.index')->withStatus('Successfully Registered Vendor.');
    }

    public function edit(int $id)
    {
        $data['provider'] = $this->provider->find($id);
        return view('providers.edit', $data);
    }

    public function show(int $id)
    {
        $data['provider'] = $provider = $this->provider->find($id);
        $data['transactions'] = $provider->transactions()->latest()->limit(25)->get();
        $data['receipts'] = $provider->receipts()->latest()->limit(25)->get();

        return view('providers.show', $data);
    }

    public function update(ProviderRequest $request, int $id)
    {
        $input = $request->all();
        $this->provider->update($id, $input);

        return redirect()->route('providers.index')->withStatus('Provider updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->provider->delete($id);
        return redirect()->route('providers.index')->withStatus('Provider removed successfully.');
    }
}
