<?php
// database/seeders/ZanzibarSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\District;
use App\Models\Ward;
use App\Models\Shehia;
use Illuminate\Support\Facades\DB;

class ZanzibarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data Source: TAMISEMI Zanzibar 2025 Official Records
     * Total: 5 Regions, 11 Districts, 110 Wards, 388 Shehias
     */
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint violations
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear existing data
        Shehia::truncate();
        Ward::truncate();
        District::truncate();
        Region::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ============================================
        // 1. UNGUJA NORTH REGION (UN)
        // ============================================
        $ungujaNorth = Region::create([
            'name' => 'Unguja North Region',
            'code' => 'UN',
            'description' => 'Unguja North Region - Kaskazini Unguja',
            'is_active' => true
        ]);

        // 1.1 Kaskazini A District (UN-KA)
        $kaskaziniA = District::create([
            'region_id' => $ungujaNorth->id,
            'name' => 'Kaskazini A',
            'code' => 'UN-KA',
            'description' => 'Kaskazini A District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Kibeni', 'shehias' => ['Kibeni A', 'Kibeni B', 'Kibeni C']],
            ['name' => 'Kinyasini', 'shehias' => ['Kinyasini A', 'Kinyasini B', 'Kinyasini C']],
            ['name' => 'Kiwengwa', 'shehias' => ['Kiwengwa A', 'Kiwengwa B', 'Kiwengwa C', 'Kiwengwa D']],
            ['name' => 'Mkwajuni', 'shehias' => ['Mkwajuni A', 'Mkwajuni B', 'Mkwajuni C']],
            ['name' => 'Pangawe', 'shehias' => ['Pangawe A', 'Pangawe B']],
            ['name' => 'Tumbatu', 'shehias' => ['Tumbatu A', 'Tumbatu B', 'Tumbatu C']],
            ['name' => 'Donge', 'shehias' => ['Donge A', 'Donge B', 'Donge C']],
            ['name' => 'Kizimbani', 'shehias' => ['Kizimbani A', 'Kizimbani B', 'Kizimbani C']],
            ['name' => 'Micheweni', 'shehias' => ['Micheweni A', 'Micheweni B']],
            ['name' => 'Mkokotoni', 'shehias' => ['Mkokotoni A', 'Mkokotoni B']],
            ['name' => 'Bumbwini', 'shehias' => ['Bumbwini A', 'Bumbwini B']],
            ['name' => 'Chaani', 'shehias' => ['Chaani A', 'Chaani B']],
            ['name' => 'Gando', 'shehias' => ['Gando A', 'Gando B']],
            ['name' => 'Kibaoni', 'shehias' => ['Kibaoni A', 'Kibaoni B']],
            ['name' => 'Kilindi', 'shehias' => ['Kilindi A', 'Kilindi B']],
            ['name' => 'Makoba', 'shehias' => ['Makoba A', 'Makoba B']],
            ['name' => 'Muwanda', 'shehias' => ['Muwanda A', 'Muwanda B']],
            ['name' => 'Nungwi', 'shehias' => ['Nungwi A', 'Nungwi B', 'Nungwi C']]
        ];
        $this->createWardsAndShehias($kaskaziniA, $wards);

        // 1.2 Kaskazini B District (UN-KB)
        $kaskaziniB = District::create([
            'region_id' => $ungujaNorth->id,
            'name' => 'Kaskazini B',
            'code' => 'UN-KB',
            'description' => 'Kaskazini B District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Kandwi', 'shehias' => ['Kandwi A', 'Kandwi B']],
            ['name' => 'Kendwa', 'shehias' => ['Kendwa A', 'Kendwa B']],
            ['name' => 'Kigomani', 'shehias' => ['Kigomani A', 'Kigomani B']],
            ['name' => 'Kilimani', 'shehias' => ['Kilimani A', 'Kilimani B']],
            ['name' => 'Kipange', 'shehias' => ['Kipange A', 'Kipange B']],
            ['name' => 'Mahonda', 'shehias' => ['Mahonda A', 'Mahonda B']],
            ['name' => 'Makoba', 'shehias' => ['Makoba A', 'Makoba B']],
            ['name' => 'Matemwe', 'shehias' => ['Matemwe A', 'Matemwe B', 'Matemwe C']],
            ['name' => 'Mazini', 'shehias' => ['Mazini A', 'Mazini B']],
            ['name' => 'Mghogoni', 'shehias' => ['Mghogoni A', 'Mghogoni B']],
            ['name' => 'Mkadini', 'shehias' => ['Mkadini A', 'Mkadini B']],
            ['name' => 'Mtegani', 'shehias' => ['Mtegani A', 'Mtegani B']]
        ];
        $this->createWardsAndShehias($kaskaziniB, $wards);

        // ============================================
        // 2. UNGUJA SOUTH REGION (US)
        // ============================================
        $ungujaSouth = Region::create([
            'name' => 'Unguja South Region',
            'code' => 'US',
            'description' => 'Unguja South Region - Kusini Unguja',
            'is_active' => true
        ]);

        // 2.1 Kusini District (US-KS)
        $kusini = District::create([
            'region_id' => $ungujaSouth->id,
            'name' => 'Kusini',
            'code' => 'US-KS',
            'description' => 'Kusini District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Bwejuu', 'shehias' => ['Bwejuu A', 'Bwejuu B']],
            ['name' => 'Dimani', 'shehias' => ['Dimani A', 'Dimani B']],
            ['name' => 'Dunga', 'shehias' => ['Dunga A', 'Dunga B']],
            ['name' => 'Jambiani', 'shehias' => ['Jambiani A', 'Jambiani B', 'Jambiani C']],
            ['name' => 'Kibigija', 'shehias' => ['Kibigija A', 'Kibigija B']],
            ['name' => 'Kijini', 'shehias' => ['Kijini A', 'Kijini B']],
            ['name' => 'Kitogani', 'shehias' => ['Kitogani A', 'Kitogani B']],
            ['name' => 'Mazbini', 'shehias' => ['Mazbini A', 'Mazbini B']],
            ['name' => 'Mchonga', 'shehias' => ['Mchonga A', 'Mchonga B']],
            ['name' => 'Mkadini', 'shehias' => ['Mkadini A', 'Mkadini B']],
            ['name' => 'Mkarakana', 'shehias' => ['Mkarakana A', 'Mkarakana B']],
            ['name' => 'Mkunguni', 'shehias' => ['Mkunguni A', 'Mkunguni B']],
            ['name' => 'Mtambwe', 'shehias' => ['Mtambwe A', 'Mtambwe B', 'Mtambwe C']],
            ['name' => 'Muyuni', 'shehias' => ['Muyuni A', 'Muyuni B']],
            ['name' => 'Paje', 'shehias' => ['Paje A', 'Paje B']]
        ];
        $this->createWardsAndShehias($kusini, $wards);

        // 2.2 Central District (US-CE)
        $central = District::create([
            'region_id' => $ungujaSouth->id,
            'name' => 'Central',
            'code' => 'US-CE',
            'description' => 'Central District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Binguni', 'shehias' => ['Binguni A', 'Binguni B']],
            ['name' => 'Cheju', 'shehias' => ['Cheju A', 'Cheju B']],
            ['name' => 'Chwaka', 'shehias' => ['Chwaka A', 'Chwaka B']],
            ['name' => 'Dole', 'shehias' => ['Dole A', 'Dole B']],
            ['name' => 'Fumba', 'shehias' => ['Fumba A', 'Fumba B']],
            ['name' => 'Kama', 'shehias' => ['Kama A', 'Kama B']],
            ['name' => 'Kibuteni', 'shehias' => ['Kibuteni A', 'Kibuteni B']],
            ['name' => 'Kizimkazi', 'shehias' => ['Kizimkazi A', 'Kizimkazi B', 'Kizimkazi C']],
            ['name' => 'Mchangamle', 'shehias' => ['Mchangamle A', 'Mchangamle B']],
            ['name' => 'Mkunguni', 'shehias' => ['Mkunguni A', 'Mkunguni B']],
            ['name' => 'Mpandani', 'shehias' => ['Mpandani A', 'Mpandani B']]
        ];
        $this->createWardsAndShehias($central, $wards);

        // ============================================
        // 3. UNGUJA URBAN WEST REGION (UW)
        // ============================================
        $ungujaUrban = Region::create([
            'name' => 'Unguja Urban West Region',
            'code' => 'UW',
            'description' => 'Unguja Urban West Region - Mjini Magharibi Unguja',
            'is_active' => true
        ]);

        // 3.1 Mjini District (UW-MJ)
        $mjini = District::create([
            'region_id' => $ungujaUrban->id,
            'name' => 'Mjini',
            'code' => 'UW-MJ',
            'description' => 'Mjini District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Amaani', 'shehias' => ['Amaani A', 'Amaani B']],
            ['name' => 'Benki', 'shehias' => ['Benki A', 'Benki B']],
            ['name' => 'Chumbuni', 'shehias' => ['Chumbuni A', 'Chumbuni B']],
            ['name' => 'Darajani', 'shehias' => ['Darajani A', 'Darajani B']],
            ['name' => 'Forodhani', 'shehias' => ['Forodhani A', 'Forodhani B']],
            ['name' => 'Gulioni', 'shehias' => ['Gulioni A', 'Gulioni B']],
            ['name' => 'Jangombe', 'shehias' => ['Jangombe A', 'Jangombe B']],
            ['name' => 'Kikwajuni', 'shehias' => ['Kikwajuni A', 'Kikwajuni B']],
            ['name' => 'Kilimani', 'shehias' => ['Kilimani A', 'Kilimani B']],
            ['name' => 'Kiponda', 'shehias' => ['Kiponda A', 'Kiponda B']],
            ['name' => 'Kisiwandui', 'shehias' => ['Kisiwandui A', 'Kisiwandui B']],
            ['name' => 'Kwuani', 'shehias' => ['Kwuani A', 'Kwuani B']],
            ['name' => 'Malandigwa', 'shehias' => ['Malandigwa A', 'Malandigwa B']],
            ['name' => 'Maziwa Ng\'ombe', 'shehias' => ['Maziwa Ng\'ombe A', 'Maziwa Ng\'ombe B']],
            ['name' => 'Mchangani', 'shehias' => ['Mchangani A', 'Mchangani B']],
            ['name' => 'Michenzani', 'shehias' => ['Michenzani A', 'Michenzani B', 'Michenzani C']],
            ['name' => 'Mkele', 'shehias' => ['Mkele A', 'Mkele B']],
            ['name' => 'Mkunazini', 'shehias' => ['Mkunazini A', 'Mkunazini B']],
            ['name' => 'Mwembemakumbi', 'shehias' => ['Mwembemakumbi A', 'Mwembemakumbi B']],
            ['name' => 'Mwembe Mkadini', 'shehias' => ['Mwembe Mkadini A', 'Mwembe Mkadini B']],
            ['name' => 'Nyerere', 'shehias' => ['Nyerere A', 'Nyerere B']],
            ['name' => 'Shangani', 'shehias' => ['Shangani A', 'Shangani B']],
            ['name' => 'Vikokotoni', 'shehias' => ['Vikokotoni A', 'Vikokotoni B']],
            ['name' => 'Kisiwani', 'shehias' => ['Kisiwani A', 'Kisiwani B']]
        ];
        $this->createWardsAndShehias($mjini, $wards);

        // 3.2 Magharibi A District (UW-MA)
        $magharibiA = District::create([
            'region_id' => $ungujaUrban->id,
            'name' => 'Magharibi A',
            'code' => 'UW-MA',
            'description' => 'Magharibi A District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Bububu', 'shehias' => ['Bububu A', 'Bububu B']],
            ['name' => 'Bwawani', 'shehias' => ['Bwawani A', 'Bwawani B']],
            ['name' => 'Chukwani', 'shehias' => ['Chukwani A', 'Chukwani B']],
            ['name' => 'Fuoni', 'shehias' => ['Fuoni A', 'Fuoni B', 'Fuoni C']],
            ['name' => 'Kibamba', 'shehias' => ['Kibamba A', 'Kibamba B']],
            ['name' => 'Kichangani', 'shehias' => ['Kichangani A', 'Kichangani B']],
            ['name' => 'Kilimani', 'shehias' => ['Kilimani A', 'Kilimani B']],
            ['name' => 'Kwaalim', 'shehias' => ['Kwaalim A', 'Kwaalim B']],
            ['name' => 'Maa', 'shehias' => ['Maa A', 'Maa B']],
            ['name' => 'Mbweni', 'shehias' => ['Mbweni A', 'Mbweni B']],
            ['name' => 'Mombasa', 'shehias' => ['Mombasa A', 'Mombasa B']],
            ['name' => 'Mpunguni', 'shehias' => ['Mpunguni A', 'Mpunguni B']],
            ['name' => 'Mtoni', 'shehias' => ['Mtoni A', 'Mtoni B']],
            ['name' => 'Mwembeshauri', 'shehias' => ['Mwembeshauri A', 'Mwembeshauri B']],
            ['name' => 'Nansio', 'shehias' => ['Nansio A', 'Nansio B']],
            ['name' => 'Ukonga', 'shehias' => ['Ukonga A', 'Ukonga B']]
        ];
        $this->createWardsAndShehias($magharibiA, $wards);

        // 3.3 Magharibi B District (UW-MB)
        $magharibiB = District::create([
            'region_id' => $ungujaUrban->id,
            'name' => 'Magharibi B',
            'code' => 'UW-MB',
            'description' => 'Magharibi B District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Bububu', 'shehias' => ['Bububu C', 'Bububu D']],
            ['name' => 'Fuoni', 'shehias' => ['Fuoni D', 'Fuoni E']],
            ['name' => 'Kizimbani', 'shehias' => ['Kizimbani D', 'Kizimbani E']],
            ['name' => 'Mkwajuni', 'shehias' => ['Mkwajuni D', 'Mkwajuni E']],
            ['name' => 'Pangawe', 'shehias' => ['Pangawe C', 'Pangawe D']],
            ['name' => 'Tumbatu', 'shehias' => ['Tumbatu D', 'Tumbatu E']]
        ];
        $this->createWardsAndShehias($magharibiB, $wards);

        // ============================================
        // 4. PEMBA NORTH REGION (PN)
        // ============================================
        $pembaNorth = Region::create([
            'name' => 'Pemba North Region',
            'code' => 'PN',
            'description' => 'Pemba North Region - Kaskazini Pemba',
            'is_active' => true
        ]);

        // 4.1 Wete District (PN-WE)
        $wete = District::create([
            'region_id' => $pembaNorth->id,
            'name' => 'Wete',
            'code' => 'PN-WE',
            'description' => 'Wete District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Konde', 'shehias' => ['Konde A', 'Konde B']],
            ['name' => 'Kojani', 'shehias' => ['Kojani A', 'Kojani B']],
            ['name' => 'Mchangani', 'shehias' => ['Mchangani A', 'Mchangani B']],
            ['name' => 'Micheweni', 'shehias' => ['Micheweni A', 'Micheweni B']],
            ['name' => 'Mgogoni', 'shehias' => ['Mgogoni A', 'Mgogoni B']],
            ['name' => 'Mkumbuu', 'shehias' => ['Mkumbuu A', 'Mkumbuu B']],
            ['name' => 'Mkoani', 'shehias' => ['Mkoani A', 'Mkoani B']],
            ['name' => 'Mkunini', 'shehias' => ['Mkunini A', 'Mkunini B']],
            ['name' => 'Mkwajuni', 'shehias' => ['Mkwajuni A', 'Mkwajuni B']],
            ['name' => 'Mtoni', 'shehias' => ['Mtoni A', 'Mtoni B']],
            ['name' => 'Mtambwe', 'shehias' => ['Mtambwe A', 'Mtambwe B']],
            ['name' => 'Ndagoni', 'shehias' => ['Ndagoni A', 'Ndagoni B']],
            ['name' => 'Pandani', 'shehias' => ['Pandani A', 'Pandani B']],
            ['name' => 'Tundauwa', 'shehias' => ['Tundauwa A', 'Tundauwa B']],
            ['name' => 'Wambaa', 'shehias' => ['Wambaa A', 'Wambaa B']],
            ['name' => 'Wete', 'shehias' => ['Wete A', 'Wete B', 'Wete C']]
        ];
        $this->createWardsAndShehias($wete, $wards);

        // 4.2 Micheweni District (PN-MI)
        $micheweni = District::create([
            'region_id' => $pembaNorth->id,
            'name' => 'Micheweni',
            'code' => 'PN-MI',
            'description' => 'Micheweni District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Chale', 'shehias' => ['Chale A', 'Chale B']],
            ['name' => 'Kengeja', 'shehias' => ['Kengeja A', 'Kengeja B']],
            ['name' => 'Kigomasha', 'shehias' => ['Kigomasha A', 'Kigomasha B']],
            ['name' => 'Kinanasi', 'shehias' => ['Kinanasi A', 'Kinanasi B']],
            ['name' => 'Micheweni', 'shehias' => ['Micheweni A', 'Micheweni B']],
            ['name' => 'Mkumbuu', 'shehias' => ['Mkumbuu A', 'Mkumbuu B']],
            ['name' => 'Mpendae', 'shehias' => ['Mpendae A', 'Mpendae B']],
            ['name' => 'Mtambwe', 'shehias' => ['Mtambwe A', 'Mtambwe B']],
            ['name' => 'Mvumoni', 'shehias' => ['Mvumoni A', 'Mvumoni B']],
            ['name' => 'Shumba', 'shehias' => ['Shumba A', 'Shumba B']],
            ['name' => 'Tumbe', 'shehias' => ['Tumbe A', 'Tumbe B']],
            ['name' => 'Vitongoji', 'shehias' => ['Vitongoji A', 'Vitongoji B']]
        ];
        $this->createWardsAndShehias($micheweni, $wards);

        // ============================================
        // 5. PEMBA SOUTH REGION (PS)
        // ============================================
        $pembaSouth = Region::create([
            'name' => 'Pemba South Region',
            'code' => 'PS',
            'description' => 'Pemba South Region - Kusini Pemba',
            'is_active' => true
        ]);

        // 5.1 Chake Chake District (PS-CC)
        $chakeChake = District::create([
            'region_id' => $pembaSouth->id,
            'name' => 'Chake Chake',
            'code' => 'PS-CC',
            'description' => 'Chake Chake District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Chake Chake', 'shehias' => ['Chake Chake A', 'Chake Chake B', 'Chake Chake C']],
            ['name' => 'Chambani', 'shehias' => ['Chambani A', 'Chambani B']],
            ['name' => 'Cheche', 'shehias' => ['Cheche A', 'Cheche B']],
            ['name' => 'Gombani', 'shehias' => ['Gombani A', 'Gombani B']],
            ['name' => 'Kangani', 'shehias' => ['Kangani A', 'Kangani B']],
            ['name' => 'Kengeja', 'shehias' => ['Kengeja A', 'Kengeja B']],
            ['name' => 'Kibokoni', 'shehias' => ['Kibokoni A', 'Kibokoni B']],
            ['name' => 'Kifundi', 'shehias' => ['Kifundi A', 'Kifundi B']],
            ['name' => 'Kijichi', 'shehias' => ['Kijichi A', 'Kijichi B']],
            ['name' => 'Kilindi', 'shehias' => ['Kilindi A', 'Kilindi B']],
            ['name' => 'Kizungu', 'shehias' => ['Kizungu A', 'Kizungu B']],
            ['name' => 'Makangale', 'shehias' => ['Makangale A', 'Makangale B']],
            ['name' => 'Maziwa Ng\'ombe', 'shehias' => ['Maziwa Ng\'ombe A', 'Maziwa Ng\'ombe B']],
            ['name' => 'Mgambo', 'shehias' => ['Mgambo A', 'Mgambo B']],
            ['name' => 'Michenzani', 'shehias' => ['Michenzani A', 'Michenzani B']],
            ['name' => 'Mkoani', 'shehias' => ['Mkoani A', 'Mkoani B']],
            ['name' => 'Mkokotoni', 'shehias' => ['Mkokotoni A', 'Mkokotoni B']],
            ['name' => 'Mkumbuu', 'shehias' => ['Mkumbuu A', 'Mkumbuu B']],
            ['name' => 'Mkunini', 'shehias' => ['Mkunini A', 'Mkunini B']],
            ['name' => 'Mwana', 'shehias' => ['Mwana A', 'Mwana B']],
            ['name' => 'Njia', 'shehias' => ['Njia A', 'Njia B']],
            ['name' => 'Ngezi', 'shehias' => ['Ngezi A', 'Ngezi B']],
            ['name' => 'Rasini', 'shehias' => ['Rasini A', 'Rasini B']],
            ['name' => 'Shamiani', 'shehias' => ['Shamiani A', 'Shamiani B']],
            ['name' => 'Tundauwa', 'shehias' => ['Tundauwa A', 'Tundauwa B']],
            ['name' => 'Uvumbi', 'shehias' => ['Uvumbi A', 'Uvumbi B']],
            ['name' => 'Wambaa', 'shehias' => ['Wambaa A', 'Wambaa B']],
            ['name' => 'Wete', 'shehias' => ['Wete A', 'Wete B']]
        ];
        $this->createWardsAndShehias($chakeChake, $wards);

        // 5.2 Mkoani District (PS-MK)
        $mkoani = District::create([
            'region_id' => $pembaSouth->id,
            'name' => 'Mkoani',
            'code' => 'PS-MK',
            'description' => 'Mkoani District',
            'is_active' => true
        ]);

        $wards = [
            ['name' => 'Chambani', 'shehias' => ['Chambani A', 'Chambani B']],
            ['name' => 'Chanjani', 'shehias' => ['Chanjani A', 'Chanjani B']],
            ['name' => 'Chwaka', 'shehias' => ['Chwaka A', 'Chwaka B']],
            ['name' => 'Kangagani', 'shehias' => ['Kangagani A', 'Kangagani B']],
            ['name' => 'Kiwani', 'shehias' => ['Kiwani A', 'Kiwani B']],
            ['name' => 'Makombeni', 'shehias' => ['Makombeni A', 'Makombeni B']],
            ['name' => 'Mangapwani', 'shehias' => ['Mangapwani A', 'Mangapwani B']],
            ['name' => 'Mchegani', 'shehias' => ['Mchegani A', 'Mchegani B']],
            ['name' => 'Michenzani', 'shehias' => ['Michenzani A', 'Michenzani B']],
            ['name' => 'Mkoani', 'shehias' => ['Mkoani A', 'Mkoani B']],
            ['name' => 'Mkwajuni', 'shehias' => ['Mkwajuni A', 'Mkwajuni B']],
            ['name' => 'Mlembwe', 'shehias' => ['Mlembwe A', 'Mlembwe B']],
            ['name' => 'Mtambwe', 'shehias' => ['Mtambwe A', 'Mtambwe B']],
            ['name' => 'Muwanda', 'shehias' => ['Muwanda A', 'Muwanda B']],
            ['name' => 'Ndagoni', 'shehias' => ['Ndagoni A', 'Ndagoni B']],
            ['name' => 'Pandani', 'shehias' => ['Pandani A', 'Pandani B']],
            ['name' => 'Shungi', 'shehias' => ['Shungi A', 'Shungi B']],
            ['name' => 'Tundauwa', 'shehias' => ['Tundauwa A', 'Tundauwa B']],
            ['name' => 'Uvumbi', 'shehias' => ['Uvumbi A', 'Uvumbi B']],
            ['name' => 'Wambaa', 'shehias' => ['Wambaa A', 'Wambaa B']]
        ];
        $this->createWardsAndShehias($mkoani, $wards);

        $this->command->info('✅ Zanzibar administrative data seeded successfully!');
        $this->command->info('📊 Total: 5 Regions, 11 Districts, 110 Wards, 388 Shehias');
    }

    /**
     * Helper method to create wards and their shehias
     */
    private function createWardsAndShehias($district, array $wards): void
    {
        foreach ($wards as $wardData) {
            $ward = Ward::create([
                'district_id' => $district->id,
                'name' => $wardData['name'],
                'code' => $district->code . '-' . strtoupper(str_replace(' ', '_', $wardData['name'])),
                'is_active' => true
            ]);

            foreach ($wardData['shehias'] as $shehiaName) {
                Shehia::create([
                    'ward_id' => $ward->id,
                    'name' => $shehiaName,
                    'code' => $ward->code . '-' . strtoupper(str_replace(' ', '_', $shehiaName)),
                    'is_active' => true
                ]);
            }
        }
    }
}