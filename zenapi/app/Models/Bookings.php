<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Roomtypes;
use Illuminate\Support\Collection;
use \Exception;

class Bookings extends Model
{
    public static function findByDateRange($begin, $end){
      return self::select("date", "inventory", "roomtype", "price")
                 ->with('type')
                 ->whereBetween("date", [$begin, $end])
                 ->get()
                 ->toArray();
    }

    public static function findBookings($roomtype, $start, $end, $weekdays = null){

        $startDate = strtotime($start);
        $endDate = strtotime($end);

        if ($endDate < $startDate) {
            throw new Exception("Invalid date range {$start}-{$end}.");
        }

        $bookings = self::whereBetween("date", [date("Y-m-d", $startDate), date("Y-m-d", $endDate)])
                        ->where("roomtype", $roomtype)
                        ->get();

        $existings = [];
        foreach($bookings as $booking){
            $existings[$booking->date] = $booking;
        }

        if( !$weekdays )
          $weekdays = [1,1,1,1,1,1,1,1];

        $bookings = collect([]);
        while($startDate <= $endDate) {
            $dow = date('w', $startDate);
            $date = date("Y-m-d", $startDate);

            $startDate = strtotime("+1 day", $startDate);

            if($weekdays[$dow] == 0) continue;

            if( !isset( $existings[ $date ] ) ) {

                $default = Roomtypes::find($roomtype);

                $booking = new Bookings;
                $booking->date = $date;
                $booking->price = $default->baseprice;
                $booking->inventory = $default->inventory;
                $booking->roomtype = $default->id;

            }else{
                $booking = $existings[ $date ];
            }

            $bookings->push($booking);
        }

        return $bookings;
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Roomtypes', 'roomtype');
    }
}
