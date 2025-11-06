<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\company;
use App\Http\Requests\companyrequest;
use App\Http\Resources\companyresource;


class companycontroller extends Controller
{
    //


    public function store(companyrequest $request)
    {
        $company = new company();
        $company->fill($request->all());
        $company->save();
        




        return new companyresource($company);

    }


    public function destroy($id)
    {
        $company = company::findOrFail($id);
        $company->delete();

        return new companyresource($company);
    }
  
}
