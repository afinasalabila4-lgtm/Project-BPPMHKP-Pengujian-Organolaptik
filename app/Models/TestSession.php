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



    public function semuaPanelisSudahMengisi(): bool
    {

        $jumlahPanelis = $this
            ->sessionUsers()
            ->where('role', 'panelis')
            ->count();

        return $jumlahPanelis > 0
            && $this->assessments()->count() >= $jumlahPanelis;

    }



    public function selesaikanOtomatisJikaLengkap(): void
    {

        if (
            $this->status !== 'selesai'
            && $this->semuaPanelisSudahMengisi()
        ) {

            $this->update([
                'status' => 'selesai'
            ]);

        }

    }


}