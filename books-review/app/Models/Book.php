<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;


class Book extends Model
{
    use HasFactory;

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder{
        return $query->where('title', 'LIKE', '%' . $title . '%' );
    }

    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
        return $query->withCount([
            'reviews' => fn(Builder $query) => $this->dateRangeFilter($query, $from, $to)
        ]);
    }

    public function scopeWithAvgRating(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
        return $query->withAvg([
            'reviews' => fn(Builder $query) => $this->dateRangeFilter($query, $from, $to)
        ], 'rating');
    }

    public function scopePopular(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
        return $query->withReviewsCount()
        ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null):Builder{
        $test = $query->withAvgRating();
        return $query->withAvgRating()
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $min):Builder|QueryBuilder{
        return $query->having('reviews_count', '>=', $min);
    }

    private function dateRangeFilter(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
       
        if($from && !$to){
            return $query->where('created_at', '>=', $from);
        }elseif(!$from && $to){
            return$query->where('created_at', '<=', $to);
        }elseif($from && $to){
            return $query->whereBetween('created_at', [$from, $to]);
        }else{
            return $query->latest();
        }
    }

    public function scopePopularLastMonth(Builder $query):Builder|QueryBuilder{
        return $query->popular(now()->subMonth(), now())
        ->highestRated(now()->subMonth(), now())
        ->minReviews(2);
    }

    public function scopePopularLastSixMonth(Builder $query):Builder|QueryBuilder{
        return $query->popular(now()->subMonth(6), now())
        ->highestRated(now()->subMonth(6), now())
        ->minReviews(2);
    }

    public function scopeHighestRatedLastMonth(Builder $query):Builder|QueryBuilder{
        return $query->highestRated(now()->subMonth(), now())
        ->popular(now()->subMonth(), now())
        ->minReviews(2);
    }

    public function scopeHighestRatedLastSixMonth(Builder $query):Builder|QueryBuilder{
        return $query->highestRated(now()->subMonth(6), now())
        ->popular(now()->subMonth(6), now())
        ->minReviews(2);
    }

    protected static function booted(){
        static::updated(fn(Book $book) => cache()->forget('book:' . $book->id));
        static::deleted(fn(Book $book) => cache()->forget('book:' . $book->id));

    }
}
