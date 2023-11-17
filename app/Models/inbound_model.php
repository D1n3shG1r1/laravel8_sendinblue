<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inbound_model extends Model
{
    use HasFactory;
    protected $table = "sb_inbound_parsed_emails";
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;
}
