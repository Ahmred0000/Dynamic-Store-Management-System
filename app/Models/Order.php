<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $fillable = ['order_number', 'customer_name', 'total_price', 'status', 'user_id', 'notes'];

    public function items() { return $this->hasMany(OrderItem::class); }

    // دي العلاقة اللي الـ Dashboard بتدور عليها
    public function user() {
        return $this->belongsTo(User::class);
    }
}
