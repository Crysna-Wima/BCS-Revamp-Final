<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectProfileRequest;
use App\Models\ProjectProfile;
use App\Models\Company;
use DataTables;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Routes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProjectProfileController extends Controller
{
    public function index()
    {
        // $query = new Company();
        $data['menus'] = $this->getDashboardMenu();
        $data['menu']  = Menu::select('id', 'name')->get();
        $data['company'] = Company::select('id', 'company', 'description')->get();
        return view('ProjectProfile', $data);
    }

    public function datatables(Request $request)
    {
        $query    = ProjectProfile::get();
        $data     = DataTables::of($query)->make(true);
        $response = $data->getData(true);
        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $projectprofile = ProjectProfile::create([
            'project_profile' => $request->project_profile,
            'description' => $request->description,
            'posting_date' => $request->posting_date,

            // 'created_by' => Auth::user()->username,
            // 'updated_by' => Auth::user()->username,
            'status' => $request->status,
            'company' => $request->company,
            'type_inv' => $request->type_inv,
        ]);

        $response = responseSuccess(trans('message.read-success'),$projectprofile);
        return response()->json($response,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($projectprofile)
    {

        $query   = ProjectProfile::find($projectprofile);
        $response = responseSuccess(trans('message.read-success'),$query);
        return response()->json($response,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $query   = ProjectProfile::find($id);
        $response = responseSuccess(trans("messages.read-success"), $query);
        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, ProjectProfileRequest $request)
    {
          $data = $this->findDataWhere(ProjectProfile::class, ['id' => $id]);

        //   dd($data);exit();
          DB::beginTransaction();
          try {
              $data->update([
                'project_profile' => $request->project_profile,
                'description' => $request->description,
                'posting_date' => $request->posting_date,
    
                // 'created_by' => Auth::user()->username,
                // 'updated_by' => Auth::user()->username,
                'status' => $request->status,
                'company' => $request->company,
                'type_inv' => $request->type_inv,
                    
              ]);
              DB::commit();
              $response = responseSuccess(trans("messages.update-success"), $data);
              return response()->json($response, 200, [], JSON_PRETTY_PRINT);
          } catch (Exception $e) {
              DB::rollback();
              $response = responseFail(trans("messages.update-fail"), $e->getMessage());
              return response()->json($response, 500, [], JSON_PRETTY_PRINT);
            }

    }


    public function destroy($id)
    {

        ProjectProfile::destroy($id);
        $response = responseSuccess(trans('message.delete-success'));
        return response()->json($response,200);
    }
}
