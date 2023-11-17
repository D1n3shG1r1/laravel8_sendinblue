<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class outbound_model extends Model
{
    use HasFactory;
    protected $table = "sb_sent_emails";
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
}
