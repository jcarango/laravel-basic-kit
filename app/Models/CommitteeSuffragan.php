<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeSuffragan extends Pivot
{
    protected $table = 'committee_suffragan';

    public function committee(): BelongsTo
    {
        return $this->belongsTo(PoliticalCommittee::class, 'committee_id');
    }

    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class);
    }
}
