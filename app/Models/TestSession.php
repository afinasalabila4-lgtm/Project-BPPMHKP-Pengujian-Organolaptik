<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class TestSession extends Model
{

    use HasFactory;



    protected $fillable = [

        'sample_id',

        'tanggal_pengujian',

        'status',

        'catatan',

    ];




    public function sample()
    {
        return $this->belongsTo(
            Sample::class
        );
    }


    public function assessments()
    {
        return $this->hasMany(
            Assessment::class
        );
    }



    public function sessionUsers()
    {

        return $this->hasMany(SessionUser::class);

    }



    public function nilaiAkhirMutuSudahKeluar(): bool
    {

        return $this->assessments()->count() > 0;

    }



    public function selesaikanOtomatisJikaLengkap(): void
    {

        if (
            $this->status !== 'selesai'
            && $this->nilaiAkhirMutuSudahKeluar()
        ) {

            $this->update([
                'status' => 'selesai'
            ]);

        }

    }


}