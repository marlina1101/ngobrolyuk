<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'created_by'];

    // Pembuat grup
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Semua anggota (many-to-many via group_user)
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Semua pesan dalam grup
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    // Cek apakah user tertentu adalah anggota grup ini
    public function hasMember($userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    // Cek apakah user tertentu adalah pembuat grup ini
    public function isCreator($userId): bool
    {
        return (int) $this->created_by === (int) $userId;
    }
}