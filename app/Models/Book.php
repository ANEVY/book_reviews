<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews(){
        return $this->hasMany(Review::class);
    }
    public function scopeTitle(Builder $query, string $title){   
        return $query->where("title","like","%".$title."%");
    }
    public function scopeLastest(Builder $query){
        return $query->latest();
    }
    public function scopePopular(Builder $query,$from = null , $to = null){
        return $query->withCount(["reviews"=>function(Builder $q) use($from ,$to)  {
           $this->dateRangeFilter($q,$from, $to);
            
        }])->orderBy("reviews_count","desc");
    }
    private function dateRangeFilter(Builder $query,$from = null , $to = null){
        if ($from && !$to) {
            $query->where("created_at",">=",$from);
        }else if( !$from && $to) {
            $query->where("created_at","<=",$to);
        }else if( $from && $to) {
            $query->whereBetween("created_at", [$from,$to]);

        }
    }
    public function scopeHighestRated(Builder $query){
        return $query->withAvg("reviews","rating")
        ->orderBy("reviews_avg_rating","desc");
    }
    public function scopePopularLastMonth(Builder $query){
        //Get last month from current date
        $currentDate = now();
        $lastMonthDate = now()->subMonth();

        //get the start and end date of the last month
        //do a where query to get books between the start date and endates
        return $query->popular($lastMonthDate, $currentDate);


    }
    public function scopePopularLast6Months(Builder $query){
        //Get last month from current date
        $currentDate = now();
        $last6MonthsDate = now()->subMonths(6);

        //get the start and end date of the last month
        //do a where query to get books between the start date and endates
        return $query->popular($last6MonthsDate, $currentDate);


    }
}           
