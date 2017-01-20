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

    $filterResult = function($result){
      return array(
            "date"      => $result['date'],
            "occupied"  => $result['occupied'],
            "roomtypeId"  => $result['roomtype'],
            "roomtypeName" => $result['type']['type']
      );
    };

    return array_map($filterResult, Bookings::select("date",  "occupied", "roomtype")
            ->with('type')
            ->whereBetween("date", [$begin, $end])
            ->get()
            ->toArray());
  }

  public function store(Request $request, $date, $roomtype)
  {
    $occupied = $request->input("occupied");
    $roomtype = Roomtypes::find( $roomtype );

    if(!$roomtype) 
    {
      return response()->json(['error' => 'Invalid room type.', 'code' => '500'], 500);
    }

    if($occupied < 0 || !is_integer($occupied))
    {
      return response()->json(['error' => "Invalid number", 'code' => '500'], 500);
    }

    if($roomtype->inventory < $occupied  )
    {
      return response()->json(['error' => "You are overbooking? {$roomtype->inventory} < {$occupied}", 'code' => '500'], 500);
    }

    $booking = Bookings::where("date", $date)
                        ->where("roomtype", $roomtype->id)
                        ->first();

    if(!$booking){
      $booking = new Bookings;
      $booking->date = $date;
      $booking->roomtype = $roomtype->id;
    }

    $booking->occupied = $occupied;
    $booking->save();

    return $booking;
  }
}
