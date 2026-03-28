<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cities')->insert([
            ['state_id' => 1, 'name' => 'Mumbai', 'postal_code' => '400001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Pune', 'postal_code' => '411001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Nagpur', 'postal_code' => '440001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Nashik', 'postal_code' => '422001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Thane', 'postal_code' => '400601', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Aurangabad', 'postal_code' => '431001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Solapur', 'postal_code' => '413001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Amravati', 'postal_code' => '444601', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Kolhapur', 'postal_code' => '416001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 1, 'name' => 'Sangli', 'postal_code' => '416416', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 2, 'name' => 'Bengaluru', 'postal_code' => '560001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Mysuru', 'postal_code' => '570001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Mangaluru', 'postal_code' => '575001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Hubballi', 'postal_code' => '580001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Belagavi', 'postal_code' => '590001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Shivamogga', 'postal_code' => '577201', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Davanagere', 'postal_code' => '577001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Ballari', 'postal_code' => '583101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Tumakuru', 'postal_code' => '572101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 2, 'name' => 'Udupi', 'postal_code' => '576101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 3, 'name' => 'Ahmedabad', 'postal_code' => '380001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Surat', 'postal_code' => '395001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Vadodara', 'postal_code' => '390001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Rajkot', 'postal_code' => '360001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Bhavnagar', 'postal_code' => '364001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Jamnagar', 'postal_code' => '361001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Gandhinagar', 'postal_code' => '382010', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Junagadh', 'postal_code' => '362001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Anand', 'postal_code' => '388001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 3, 'name' => 'Navsari', 'postal_code' => '396445', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 4, 'name' => 'Ludhiana', 'postal_code' => '141001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Amritsar', 'postal_code' => '143001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Jalandhar', 'postal_code' => '144001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Patiala', 'postal_code' => '147001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Bathinda', 'postal_code' => '151001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Mohali', 'postal_code' => '160055', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Hoshiarpur', 'postal_code' => '146001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Pathankot', 'postal_code' => '145001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Moga', 'postal_code' => '142001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 4, 'name' => 'Ferozepur', 'postal_code' => '152001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 5, 'name' => 'Jaipur', 'postal_code' => '302001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Udaipur', 'postal_code' => '313001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Jodhpur', 'postal_code' => '342001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Kota', 'postal_code' => '324001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Bikaner', 'postal_code' => '334001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Ajmer', 'postal_code' => '305001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Sikar', 'postal_code' => '332001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Alwar', 'postal_code' => '301001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Pali', 'postal_code' => '306001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 5, 'name' => 'Barmer', 'postal_code' => '344001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 6, 'name' => 'Lucknow', 'postal_code' => '226001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Kanpur', 'postal_code' => '208001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Agra', 'postal_code' => '282001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Varanasi', 'postal_code' => '221001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Meerut', 'postal_code' => '250001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Bareilly', 'postal_code' => '243001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Aligarh', 'postal_code' => '202001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Moradabad', 'postal_code' => '244001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Jhansi', 'postal_code' => '284001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 6, 'name' => 'Fatehpur', 'postal_code' => '212601', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 7, 'name' => 'Chennai', 'postal_code' => '600001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Coimbatore', 'postal_code' => '641001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Madurai', 'postal_code' => '625001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Tiruchirappalli', 'postal_code' => '620001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Salem', 'postal_code' => '636001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Tirunelveli', 'postal_code' => '627001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Vellore', 'postal_code' => '632001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Erode', 'postal_code' => '638001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Dindigul', 'postal_code' => '624001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 7, 'name' => 'Kanchipuram', 'postal_code' => '631501', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 8, 'name' => 'Kolkata', 'postal_code' => '700001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Howrah', 'postal_code' => '711001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Siliguri', 'postal_code' => '734001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Durgapur', 'postal_code' => '713201', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Asansol', 'postal_code' => '713301', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Jalpaiguri', 'postal_code' => '735101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Kharagpur', 'postal_code' => '721301', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Burdwan', 'postal_code' => '713101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Midnapore', 'postal_code' => '721101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 8, 'name' => 'Cooch Behar', 'postal_code' => '736101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 9, 'name' => 'Patna', 'postal_code' => '800001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Gaya', 'postal_code' => '823001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Bhagalpur', 'postal_code' => '812001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Munger', 'postal_code' => '811201', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Darbhanga', 'postal_code' => '846001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Muzaffarpur', 'postal_code' => '842001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Arrah', 'postal_code' => '802301', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Sasaram', 'postal_code' => '821115', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Begusarai', 'postal_code' => '851101', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 9, 'name' => 'Katihar', 'postal_code' => '854105', 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['state_id' => 10, 'name' => 'Chandigarh', 'postal_code' => '160001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Faridabad', 'postal_code' => '121001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Gurugram', 'postal_code' => '122001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Ambala', 'postal_code' => '134001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Hisar', 'postal_code' => '125001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Karnal', 'postal_code' => '132001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Rohtak', 'postal_code' => '124001', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Panipat', 'postal_code' => '132103', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Sirsa', 'postal_code' => '125055', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['state_id' => 10, 'name' => 'Jhajjar', 'postal_code' => '124103', 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
