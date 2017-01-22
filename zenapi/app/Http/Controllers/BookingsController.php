<?php

namespace App\Http\Controllers;

use App\Models\Roomtypes;
use App\Models\Bookings;
use Illuminate\Http\Request;

class BookingsController extends Controller
{
    public function index($month)
    {
        list($year, $month) = explode('-', $month);

        $begin = "{$year}-{$month}-01";
        $end = date("Y-m-t", strtotime("{$year}-{$month}-1"));



        return array_map(function($result){
                                return array(
                                              "date"      => $result['date'],
                                              "inventory"  => $result['inventory'],
                                              "price"  => $result['price'],
                                              "roomtypeId"  => $result['roomtype'],
                                              "roomtypeName" => $result['type']['type']
                                            );
                            },
                         Bookings::select("date", "inventory", "roomtype", "price")
                                ->with('type')
                                ->whereBetween("date", [$begin, $end])
                                ->get()
                                ->toArray()
                        );
    }

    public function store(Request $request, $date, $roomtype)
    {
        $roomtype = Roomtypes::find($roomtype);
        if (!$roomtype) {
            return response()->json(['error' => 'Invalid room type.', 'code' => '500'], 500);
        }


        $inventory = false;
        $price = false;
        if($request->has("inventory")){
          $inventory = $request->input("inventory");
          if($inventory < 0 || !is_numeric($inventory) || intval($inventory)!=$inventory)
          {
            return response()->json(['error' => "Invalid number {$inventory}", 'code' => '500'], 500);
          }
        }

        if($request->has("price")){
          $price = $request->input("price");
          if($price < 0 || !is_numeric($price))
          {
            return response()->json(['error' => "Invalid number {$price}", 'code' => '500'], 500);
          }
        }

        if(!$price && !$inventory)
        {
          return response()->json(['error' => "Nothing to update", 'code' => '500'], 500);
        }

        $booking = Bookings::where("date", $date)
                        ->where("roomtype", $roomtype->id)
                        ->first();

        if (!$booking) {
            $booking = new Bookings;
            $booking->date = $date;
            $booking->price = $roomtype->baseprice;
            $booking->inventory = $roomtype->inventory;
            $booking->roomtype = $roomtype->id;
        }

        if($inventory !== false)
          $booking->inventory = $inventory;

        if($price  !== false)
          $booking->price = $price;
        $booking->save();

        return $booking;
    }

    public function bulk(Request $request)
    {
        $roomtype = Roomtypes::find($request->input("roomtype"));
        if (!$roomtype) {
            return response()->json(['error' => 'Invalid room type.', 'code' => '500'], 500);
        }

        $startDate = strtotime($request->input("start"));
        $endDate = strtotime($request->input("end"));
        $weekdays = $request->input("weekdays");

        $inventory = false;
        $price = false;

        if($request->has("inventory")){
          $inventory = $request->input("inventory");
          if($inventory < 0 || !is_numeric($inventory) || intval($inventory)!=$inventory)
          {
            return response()->json(['error' => "Invalid number {$inventory}", 'code' => '500'], 500);
          }
        }

        if($request->has("price")){
          $price = $request->input("price");
          if($price < 0 || !is_numeric($price))
          {
            return response()->json(['error' => "Invalid price amount {$price}", 'code' => '500'], 500);
          }
        }

        if(!$price && !$inventory)
        {
          return response()->json(['error' => "Nothing to update", 'code' => '500'], 500);
        }

        $bookings = Bookings::whereBetween("date", [date("Y-m-d", $startDate), date("Y-m-d", $endDate)])
                        ->where("roomtype", $roomtype->id)
                        ->get();

        $existings = [];
        foreach($bookings as $booking){
            $existings[$booking->date] = $booking;
        }


        $count = 0;
        $new = 0;
        while($startDate <= $endDate) {
            $dow = date('w', $startDate);
            $date = date("Y-m-d", $startDate);

            $startDate = strtotime("+1 day", $startDate);

            if($weekdays[$dow] == 0) continue;

            if( !isset( $existings[ $date ] ) ) {
                $booking = new Bookings;
                $booking->date = $date;
                $booking->price = $roomtype->baseprice;
                $booking->inventory = $roomtype->inventory;
                $booking->roomtype = $roomtype->id;
                $new++;
            }else{
                $booking = $existings[ $date ];
            }

            if($price !== false)
                $booking->price = $price;

            if($inventory !== false)
                $booking->inventory = $inventory;

            $booking->save();
            $count++;
        }

        return response()->json(['saved' => $count, 'created' => $new, 'updated' => $count - $new]);
    }
}
