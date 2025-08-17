<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Repositories\Client\ClientRepositoryInterface as Client;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface as PaymentMethod;

class ClientController extends Controller
{
    private $client;
    private $payment_method;

    function __construct(Client $client, PaymentMethod $payment_method)
    {
        $this->client = $client;
        $this->payment_method = $payment_method;
    }

    public function index()
    {
        $data['clients'] = $this->client->paginate();
        return view('clients.index', $data);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        $payload = $request->all();
        $this->client->create($payload);
        return redirect()->route('clients.index')->withStatus('Successfully registered customer.');
    }

    public function show(int $id)
    {
        $data['client'] = $this->client->find($id);
        return view('clients.show', $data);
    }

    public function edit(int $id)
    {
        $data['client'] = $this->client->find($id);
        return view('clients.edit', $data);
    }

    public function update(ClientRequest $request, int $id)
    {
        $payload = $request->all();
        $this->client->update($id, $payload);
        return redirect()->route('clients.index')->withStatus('Successfully modified customer.');
    }

    public function destroy(int $id)
    {
        $this->client->delete($id);
        return redirect()->route('clients.index')->withStatus('Customer successfully removed.');
    }

    public function addTransaction(int $id)
    {
        $data['client'] = $this->client->find($id);
        $data['payment_methods'] = $this->payment_method->all();
        return view('clients.transactions.add', $data);
    }
}
