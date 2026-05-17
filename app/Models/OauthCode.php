<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class OauthCode extends Model
{
    use MassPrunable;

    public function prunable(): Builder
    {
        return static::where("expires_at", "<", now());
    }

    public $timestamps = false;

    public $incrementing = false;

    protected $primaryKey = "code";

    protected $keyType = "string";

    protected $fillable = ["code", "user_id", "expires_at"];

    protected $casts = [
        "expires_at" => "datetime",
    ];
}
