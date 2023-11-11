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
    public function scopePopular(Builder $query,$from = null , $to = null){
        return $query->withCount(["reviews"=>function(Builder $q) use($from ,$to)  {
            if ($from && !$to) {
                $q->where("created_at",">=",$from);
            }else if( !$from && $to) {
                $q->where("created_at","<=",$to);
            }else if( $from && $to) {
                $q->whereBetween("created_at", [$from,$to]);
                // $q->where("created_at",">=",$from);
                // $q->where("created_at","<=",$to);
            }
            
            
        }
            
        ])
        ->orderBy("reviews_count","desc");
    }
    public function scopeHighestRated(Builder $query){
        return $query->withAvg("reviews","rating")
        ->orderBy("reviews_avg_rating","desc");
    }
}           
