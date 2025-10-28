<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provider;
use App\Http\Requests\StoreProvider;
use App\Http\Resources\storeProviderResource;


class ProviderController extends Controller
{
    //

    public function store(storeprovider $request)
    {
        $provider = new Provider();
        $provider->fill($request->all());
        $provider->save();
        return new storeProviderResource($provider);
        //
    }


    public function index()
    {
        $providers = Provider::all();
        return storeProviderResource::collection($providers);
        
    }

    public function show($id)
    {
        $provider = Provider::findOrFail($id);
        return new storeProviderResource($provider);
    }

    public function update(storeprovider $request, $id)
    {
        $provider = Provider::findOrFail($id);
        $provider->fill($request->all());
        $provider->save();
        return new storeProviderResource($provider);
    }

    public function destroy($id)
    {
        $provider = Provider::findOrFail($id);
        $provider->delete();
        return response()->json(['message' => 'Provider deleted successfully'], 200);
    }

    

}

