<?php

namespace App\Http\Controllers;

use App\Models\Roomtypes;
use Illuminate\Http\Request;

class RoomtypesController extends Controller
{
    public function index()
    {
        return Roomtypes::select("id", "type")->get();
    }

    public function create(Request $request)
    {
        $type = $request->input('type');

        if (!$type || $type=="") {
            return response()->json(['error' => 'Invalid type name.', 'code' => '409'], 409);
        }

        if (Roomtypes::where("type", $type)->first()) {
            return response()->json(['error' => 'Duplicated type.', 'code' => '409'], 409);
        }

        $roomType = new Roomtypes;
        $roomType->type = $type;
        $roomType->save();

        return $roomType;
    }

    public function update(Request $request, $id)
    {
        $roomType = Roomtypes::find($id);
        $type = $request->input('type');

        if (Roomtypes::where("type", $type)
                  ->where("id", "!=", $id)
                  ->first()) {
            return response()->json(['error' => 'Duplicated type.', 'code' => '409'], 409);
        }

        if (!$type || $type=="") {
            return response()->json(['error' => 'Invalid type name.', 'code' => '409'], 409);
        }

        if (!$roomType) {
            return response()->json(['error' => 'Not found.', 'code' => '404'], 404);
        }

        $roomType->type = $type;
        $roomType->save();

        return $roomType;
    }
  //
}
