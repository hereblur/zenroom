<?php

namespace App\Http\Controllers;

use App\Models\Roomtypes;
use App\Models\Bookings;
use Illuminate\Http\Request;
use \Exception;

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
                            Bookings::findByDateRange($begin, $end)
                        );
    }

    public function store(Request $request, $date, $roomtype)
    {
        try
        {
            $this->validate($request, [
                  'price' => 'sometimes|nullable|numeric|min:0|regex:/^([0-9]+)$/',
                  'inventory' => 'sometimes|nullable|numeric|min:0',
            ]);

            $date = explode("_", "{$date}_{$date}");
            $weekdays = $request->input("weekdays");

            $bookings = Bookings::findBookings($roomtype, $date[0], $date[1], $weekdays);

            foreach ($bookings as $booking) {
                if($request->has("inventory") && $request->get("inventory")){
                    $booking->inventory = $request->get("inventory");
                }

                if($request->has("price") && $request->get("price")){
                    $booking->price = $request->get("price");
                }

                $booking->save();
            }
            return $bookings;

        }catch(Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }

    }
 }
