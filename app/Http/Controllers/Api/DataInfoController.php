<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataInfo;
use Illuminate\Http\Request;

class DataInfoController extends Controller
{
    public function index()
    {
        return response()->json(DataInfo::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'data' => 'required|string',
            'info' => 'required|string',
            'icono' => 'required|string',
        ]);

        $dataInfo = DataInfo::create($request->all());

        return response()->json($dataInfo, 201);
    }

    public function show($id)
    {
        $dataInfo = DataInfo::findOrFail($id);
        return response()->json($dataInfo);
    }

    public function update(Request $request, $id)
    {
        $dataInfo = DataInfo::findOrFail($id);
        $dataInfo->update($request->all());

        return response()->json($dataInfo);
    }

    public function destroy($id)
    {
        DataInfo::destroy($id);
        return response()->json(null, 204);
    }
}
