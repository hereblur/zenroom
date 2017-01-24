<?php

namespace App\Http\Controllers;

use App\Models\Roomtypes;
use Illuminate\Http\Request;
use \Exception;

class RoomtypesController extends Controller
{
    public function index()
    {
        return Roomtypes::select("id", "type", "baseprice", "inventory")->get();
    }

    public function create(Request $request)
    {
        try{
            $this->validate($request, [
                  'type' => 'required',
                  'baseprice' => 'numeric|min:0',
            ]);

            if (Roomtypes::where("type", $request->input('type'))->first()) {
                throw new Exception("Duplicated type");
            }

            $roomType = new Roomtypes;
            $roomType->type = $request->input('type');
            $roomType->baseprice = $request->input('baseprice');
            $roomType->save();

            return $roomType;
        }catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $this->validate($request, [
                  'type' => 'sometimes|required',
                  'baseprice' => 'sometimes|numeric|min:0',
            ]);

            $roomType = Roomtypes::find($id);
            if (!$roomType) {
                throw new Exception("Not found");
            }

            if($request->has('type')){
                if (Roomtypes::where("type", $request->input('type'))
                          ->where("id", "!=", $id)
                          ->first()) {
                    throw new Exception("Duplicated type");
                }

                $roomType->type = $request->input('type');;
            }

            if($request->has('baseprice')){
                $roomType->baseprice = $request->input('baseprice');
            }

            $roomType->save();

            return $roomType;
        }catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
  //
}
