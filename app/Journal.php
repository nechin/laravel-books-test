<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Journal
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $book_id
 * @property-read \App\Book $book
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal filterByDate(\Carbon\Carbon $from = null, \Carbon\Carbon $to = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Journal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Journal extends Model
{
    protected $table = 'journal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'book_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function scopeFilterByDate(Builder $query, Carbon $from = null, Carbon $to = null)
    {
        if (is_null($from) && is_null($to)) {
            return $query;
        }

        return $query->when($from, function (Builder $query, $from) {
            $query->where('created_at', '>=', $from);
        })
        ->when($to, function (Builder $query, $to) {
            $query->where('created_at', '<=', $to);
        });
    }
}
