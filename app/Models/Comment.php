<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

     /**
     * Attributs assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'content',
        'save_info',
        'blog_id', // Relation avec le blog
        'country_code',  // Ajout de la colonne country_code
    ];

    /**
     * Relation avec le modèle Blog.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

}
