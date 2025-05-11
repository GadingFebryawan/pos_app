<?php

namespace Modules\User\Http\Controllers;

use App\Models\Companies;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Upload\Entities\Upload;
use Modules\User\DataTables\CompaniesDataTable;

class CompaniesController extends Controller
{
    public function index(CompaniesDataTable $dataTable)
    {
        abort_if(Gate::denies('access_user_management'), 403);
        return $dataTable->render('user::companies.index');
    }


    public function create()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user::companies.create');
    }


    public function store(Request $request)
    {
        abort_if(Gate::denies('access_user_management'), 403);
        $company = Companies::create([
            'name'     => $request->name,
            'address'  => $request->address,
            'status'   => $request->is_active ? 1 : 0
        ]);

        if ($company) {
            DB::table('user_companies')->insert([
                'user_id' => 1,
                'company_id' => (int) $company->id,
            ]);
        }

        toast("Success Create Company", 'success');

        return redirect()->route('companies.index');
    }


    public function edit(Companies $company)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user::companies.edit', compact('company'));
    }


    public function update(Request $request, Companies $company)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $company->update([
            'name'     => $request->name,
            'address'    => $request->address,
            'status' => $request->status ? 1 : 0
        ]);

        toast("Success Update Company", 'info');

        return redirect()->route('companies.index');
    }


    public function destroy(Companies $company)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $company->delete();

        toast('Company Deleted!', 'warning');

        return redirect()->route('companies.index');
    }
}
