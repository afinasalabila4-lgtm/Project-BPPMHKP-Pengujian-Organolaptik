<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

use App\Models\User;

use Illuminate\Support\Facades\Hash;



class UserSeeder extends Seeder
{


    public function run(): void
    {



        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */


        User::updateOrCreate(

            [
                'username'=>'admin'
            ],

            [

                'name'=>'Admin BPPMHKP',

                'username'=>'admin',

                'nip'=>'198501012010011001',

                'password'=>Hash::make('password'),

                'role'=>'admin',

                'status'=>'aktif',

            ]

        );





        /*
        |--------------------------------------------------------------------------
        | Panelis
        |--------------------------------------------------------------------------
        */


        $panelis = [


            [

                'name'=>'Budi Santoso',

                'username'=>'P001',

                'nip'=>'198001012005011001',

            ],



            [

                'name'=>'Ridwan Saputra',

                'username'=>'P002',

                'nip'=>'198203152006021002',

            ],



            [

                'name'=>'Ahmad Fauzi',

                'username'=>'P003',

                'nip'=>'198505202007031003',

            ],



            [

                'name'=>'Siti Rahma',

                'username'=>'P004',

                'nip'=>'199001102008041004',

            ],



            [

                'name'=>'Dewi Lestari',

                'username'=>'P005',

                'nip'=>'199203252009051005',

            ],



            /*
            |----------------------------------------------------------------------------------------
            | Panelis dari plan.md (Meci Desi Yulia & Wirsan dilewati karena sudah ada sebagai user)
            |----------------------------------------------------------------------------------------
            */

            ['name'=>'Nandang Koswara, S.T., M.M', 'username'=>'P006', 'nip'=>'199001012025010006'],

            ['name'=>'Dini Oktaviani, S.Pi', 'username'=>'P007', 'nip'=>'199001012025010007'],

            ['name'=>'Santi Saluri Alam, S.Pi', 'username'=>'P008', 'nip'=>'199001012025010008'],

            ['name'=>'Jimmy Margono, S.St.Pi', 'username'=>'P009', 'nip'=>'199001012025010009'],

            ['name'=>'Agustian Syarib, S.E., M.E.S.M., M.Pd', 'username'=>'P010', 'nip'=>'199001012025010010'],

            ['name'=>'Patullah, A.Md, S.Pi', 'username'=>'P011', 'nip'=>'199001012025010011'],

            ['name'=>'Entan Riyandi, S.St.Pi', 'username'=>'P012', 'nip'=>'199001012025010012'],

            ['name'=>'Yayan Haryani, A.Md', 'username'=>'P013', 'nip'=>'199001012025010013'],

            ['name'=>'Fenny Komalasari, A.Md', 'username'=>'P014', 'nip'=>'199001012025010014'],

            ['name'=>'Dadan Darmanto, A.Md', 'username'=>'P015', 'nip'=>'199001012025010015'],

            ['name'=>'Muhammad Ridwan, S.H', 'username'=>'P016', 'nip'=>'199001012025010016'],

            ['name'=>'Aris Kurniawan, A.Md', 'username'=>'P017', 'nip'=>'199001012025010017'],

            ['name'=>'Hamzah Said, A.Md', 'username'=>'P018', 'nip'=>'199001012025010018'],

            ['name'=>'Sara Tiara Karusha', 'username'=>'P019', 'nip'=>'199001012025010019'],

            ['name'=>'Tiara Sylvana Aringin', 'username'=>'P020', 'nip'=>'199001012025010020'],

            ['name'=>'Firhansyah, S.Pi', 'username'=>'P021', 'nip'=>'199001012025010021'],

            ['name'=>'Mohammad David Brillian, A,Md.T.P', 'username'=>'P022', 'nip'=>'199001012025010022'],

            ['name'=>'Azma Nurizqi Isnasari, S.Si', 'username'=>'P023', 'nip'=>'199001012025010023'],

            ['name'=>'Meila Manshurina Khomsiati, A,Md.T.P', 'username'=>'P024', 'nip'=>'199001012025010024'],

        ];




        foreach($panelis as $data)


        {


            User::updateOrCreate(

                [

                    // cari user lama berdasarkan username
                    'username'=>$data['username']

                ],


                [

                    'name'=>$data['name'],

                    'nip'=>$data['nip'],

                    'password'=>Hash::make('password'),

                    'role'=>'panelis',

                    'status'=>'aktif',
                ]
            );


        }









        /*
        |--------------------------------------------------------------------------
        | Penyelia
        |--------------------------------------------------------------------------
        */


        User::updateOrCreate(

            [

                'username'=>'penyelia'

            ],

            [

                'name'=>'Penyelia',

                'username'=>'penyelia',

                'nip'=>'197808052003011001',

                'password'=>Hash::make('password'),

                'role'=>'penyelia',

                'status'=>'aktif',

            ]

        );











        /*
        |--------------------------------------------------------------------------
        | Analis
        |--------------------------------------------------------------------------
        */


        User::updateOrCreate(

            [

                'username'=>'analis'

            ],

            [

                'name'=>'Analis 01',

                'username'=>'analis',

                'nip'=>'199205152011061001',

                'password'=>Hash::make('password'),

                'role'=>'analis',

                'status'=>'aktif',

            ]

        );











        /*
        |--------------------------------------------------------------------------
        | Penyelia (Pimpinan)
        |--------------------------------------------------------------------------
        */


        User::updateOrCreate(

            [

                'username'=>'pimpinan'

            ],

            [

                'name'=>'Pimpinan',

                'username'=>'pimpinan',

                'nip'=>'197001011990011001',

                'password'=>Hash::make('password'),

                'role'=>'penyelia',

                'status'=>'aktif',

            ]

        );


    }


}