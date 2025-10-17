<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DraftEmbed extends Model
{
    protected $table = 'embeds';

    public function draft()
    {
        return $this->belongsTo('App\Draft');
    }

    public function getHTMLIdAttribute()
    {
        return cleanupId('embed'.$this->id);
    }

    public function resolve(): ?Url
    {
        try {
            return Url::resolve($this->url);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return null;
    }
}
