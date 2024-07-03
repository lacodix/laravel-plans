<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lacodix\LaravelPlans\Contracts\Subscriber;
use Lacodix\LaravelPlans\Models\Traits\HasSubscriptions;

class User extends Model implements Subscriber
{
    use HasFactory;
    use HasSubscriptions;

    protected $guarded = [];
}
