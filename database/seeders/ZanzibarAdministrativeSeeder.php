<?php
// database/seeders/ZanzibarAdministrativeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\District;
use App\Models\Ward;
use App\Models\Shehia;

class ZanzibarAdministrativeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Urban West' => [
                'code' => 'UW',
                'districts' => [
                    'Urban' => [
                        'code' => 'URB',
                        'wards' => [
                            'Kilimahewa' => [
                                'code' => 'URB-KLH',
                                'shehias' => [
                                    ['name' => 'Kilimahewa Bondeni', 'code' => 'URB-KLH-001'],
                                    ['name' => 'Kilimahewa Juu', 'code' => 'URB-KLH-002']
                                ]
                            ],
                            'Amani' => [
                                'code' => 'URB-AMN',
                                'shehias' => [
                                    ['name' => 'Amani', 'code' => 'URB-AMN-001'],
                                    ['name' => 'Kwa Wazee', 'code' => 'URB-AMN-002']
                                ]
                            ],
                            'Mwembemakumbi' => [
                                'code' => 'URB-MWM',
                                'shehias' => [
                                    ['name' => 'Maruhubi', 'code' => 'URB-MWM-001'],
                                    ['name' => 'Mwembemakumbi', 'code' => 'URB-MWM-002'],
                                    ['name' => 'Masumbani', 'code' => 'URB-MWM-003']
                                ]
                            ],
                            'Chumbuni' => [
                                'code' => 'URB-CHM',
                                'shehias' => [
                                    ['name' => 'Banko', 'code' => 'URB-CHM-001'],
                                    ['name' => 'Karakana', 'code' => 'URB-CHM-002'],
                                    ['name' => 'Chumbuni', 'code' => 'URB-CHM-003']
                                ]
                            ],
                            'Kwaalinatu' => [
                                'code' => 'URB-KAL',
                                'shehias' => [
                                    ['name' => 'Kwaalinatu', 'code' => 'URB-KAL-001'],
                                    ['name' => 'Matarumbeta', 'code' => 'URB-KAL-002'],
                                    ['name' => 'Kidongo Chekundu', 'code' => 'URB-KAL-003']
                                ]
                            ],
                            'Urusi' => [
                                'code' => 'URB-URS',
                                'shehias' => [
                                    ['name' => 'Jang\'ombe', 'code' => 'URB-URS-001'],
                                    ['name' => 'Urusi', 'code' => 'URB-URS-002']
                                ]
                            ],
                            'Kikwajuni' => [
                                'code' => 'URB-KKJ',
                                'shehias' => [
                                    ['name' => 'Mnazimmoja', 'code' => 'URB-KKJ-001'],
                                    ['name' => 'Kisiwandui', 'code' => 'URB-KKJ-002'],
                                    ['name' => 'Kikwajuni Bondeni', 'code' => 'URB-KKJ-003'],
                                    ['name' => 'Kisimamajongoo', 'code' => 'URB-KKJ-004'],
                                    ['name' => 'Kikwajuni Juu', 'code' => 'URB-KKJ-005']
                                ]
                            ],
                            'Rahaleo' => [
                                'code' => 'URB-RHL',
                                'shehias' => [
                                    ['name' => 'Mwembeladu', 'code' => 'URB-RHL-001'],
                                    ['name' => 'Rahaleo', 'code' => 'URB-RHL-002'],
                                    ['name' => 'Mwembeshauri', 'code' => 'URB-RHL-003'],
                                    ['name' => 'Miembeni', 'code' => 'URB-RHL-004'],
                                    ['name' => 'Muembemadema', 'code' => 'URB-RHL-005']
                                ]
                            ],
                            'Kwahani' => [
                                'code' => 'URB-KWH',
                                'shehias' => [
                                    ['name' => 'Mikunguni', 'code' => 'URB-KWH-001'],
                                    ['name' => 'Kwaalimsha', 'code' => 'URB-KWH-002'],
                                    ['name' => 'Kwahani', 'code' => 'URB-KWH-003']
                                ]
                            ],
                            'Muungano' => [
                                'code' => 'URB-MUG',
                                'shehias' => [
                                    ['name' => 'Muungano', 'code' => 'URB-MUG-001'],
                                    ['name' => 'Sebleni', 'code' => 'URB-MUG-002']
                                ]
                            ],
                            'Shaurimoyo' => [
                                'code' => 'URB-SHM',
                                'shehias' => [
                                    ['name' => 'Saateni', 'code' => 'URB-SHM-001'],
                                    ['name' => 'Shaurimoyo', 'code' => 'URB-SHM-002']
                                ]
                            ],
                            'Kwamtipura' => [
                                'code' => 'URB-KMT',
                                'shehias' => [
                                    ['name' => 'Mapinduzi', 'code' => 'URB-KMT-001'],
                                    ['name' => 'Mkele', 'code' => 'URB-KMT-002'],
                                    ['name' => 'Mboriborini', 'code' => 'URB-KMT-003'],
                                    ['name' => 'Kwamtipura', 'code' => 'URB-KMT-004']
                                ]
                            ],
                            'Sogea' => [
                                'code' => 'URB-SOG',
                                'shehias' => [
                                    ['name' => 'Sogea', 'code' => 'URB-SOG-001'],
                                    ['name' => 'Nyerere', 'code' => 'URB-SOG-002'],
                                    ['name' => 'Kwamtumwajeni', 'code' => 'URB-SOG-003']
                                ]
                            ],
                            'Magomeni' => [
                                'code' => 'URB-MAG',
                                'shehias' => [
                                    ['name' => 'Meya', 'code' => 'URB-MAG-001'],
                                    ['name' => 'Magomeni', 'code' => 'URB-MAG-002']
                                ]
                            ],
                            'Malindi' => [
                                'code' => 'URB-MLI',
                                'shehias' => [
                                    ['name' => 'Shangani', 'code' => 'URB-MLI-001'],
                                    ['name' => 'Kiponda', 'code' => 'URB-MLI-002'],
                                    ['name' => 'Malindi', 'code' => 'URB-MLI-003'],
                                    ['name' => 'Mchangani Mjini', 'code' => 'URB-MLI-004'],
                                    ['name' => 'Mkunazini', 'code' => 'URB-MLI-005'],
                                    ['name' => 'Vikokotoni', 'code' => 'URB-MLI-006']
                                ]
                            ],
                            'Mlandege' => [
                                'code' => 'URB-MLG',
                                'shehias' => [
                                    ['name' => 'Muembetanga', 'code' => 'URB-MLG-001'],
                                    ['name' => 'Mlandege', 'code' => 'URB-MLG-002'],
                                    ['name' => 'Gulioni', 'code' => 'URB-MLG-003'],
                                    ['name' => 'Mitiulaya', 'code' => 'URB-MLG-004'],
                                    ['name' => 'Makadara', 'code' => 'URB-MLG-005']
                                ]
                            ],
                            'Migombani' => [
                                'code' => 'URB-MGB',
                                'shehias' => [
                                    ['name' => 'Kilimani', 'code' => 'URB-MGB-001'],
                                    ['name' => 'Migombani', 'code' => 'URB-MGB-002']
                                ]
                            ],
                            'Mpendae' => [
                                'code' => 'URB-MPD',
                                'shehias' => [
                                    ['name' => 'Kwabintiamrani', 'code' => 'URB-MPD-001'],
                                    ['name' => 'Mpendae', 'code' => 'URB-MPD-002']
                                ]
                            ],
                        ]
                    ],
                    'West A' => [
                        'code' => 'WSA',
                        'wards' => [
                            'Mtoni' => [
                                'code' => 'WSA-MTN',
                                'shehias' => [
                                    ['name' => 'Sharifu Msa', 'code' => 'WSA-MTN-001'],
                                    ['name' => 'Mtoni', 'code' => 'WSA-MTN-002']
                                ]
                            ],
                            'Mwanyanya' => [
                                'code' => 'WSA-MWN',
                                'shehias' => [
                                    ['name' => 'Mwanyanya', 'code' => 'WSA-MWN-001'],
                                    ['name' => 'Kwa Goa', 'code' => 'WSA-MWN-002'],
                                    ['name' => 'Kibweni', 'code' => 'WSA-MWN-003']
                                ]
                            ],
                            'Bububu' => [
                                'code' => 'WSA-BUB',
                                'shehias' => [
                                    ['name' => 'Bububu', 'code' => 'WSA-BUB-001'],
                                    ['name' => 'Kijichi', 'code' => 'WSA-BUB-002'],
                                    ['name' => 'Mbuzini', 'code' => 'WSA-BUB-003']
                                ]
                            ],
                            'Dole' => [
                                'code' => 'WSA-DOL',
                                'shehias' => [
                                    ['name' => 'Chemchem', 'code' => 'WSA-DOL-001'],
                                    ['name' => 'Dole', 'code' => 'WSA-DOL-002'],
                                    ['name' => 'Kizimbani', 'code' => 'WSA-DOL-003']
                                ]
                            ],
                            'Mfenesini' => [
                                'code' => 'WSA-MFE',
                                'shehias' => [
                                    ['name' => 'Mfenesini', 'code' => 'WSA-MFE-001'],
                                    ['name' => 'Mwakaje', 'code' => 'WSA-MFE-002'],
                                    ['name' => 'Bumbwisudi', 'code' => 'WSA-MFE-003']
                                ]
                            ],
                            'Kihinani' => [
                                'code' => 'WSA-KHN',
                                'shehias' => [
                                    ['name' => 'Kama', 'code' => 'WSA-KHN-001'],
                                    ['name' => 'Chuini', 'code' => 'WSA-KHN-002'],
                                    ['name' => 'Kihinani', 'code' => 'WSA-KHN-003'],
                                    ['name' => 'Kikaangoni', 'code' => 'WSA-KHN-004']
                                ]
                            ],
                            'Munduli' => [
                                'code' => 'WSA-MND',
                                'shehias' => [
                                    ['name' => 'Mto Pepo', 'code' => 'WSA-MND-001'],
                                    ['name' => 'Munduli', 'code' => 'WSA-MND-002']
                                ]
                            ],
                            'Welezo' => [
                                'code' => 'WSA-WLZ',
                                'shehias' => [
                                    ['name' => 'Uholanzi', 'code' => 'WSA-WLZ-001'],
                                    ['name' => 'Welezo', 'code' => 'WSA-WLZ-002']
                                ]
                            ],
                            'Mtofaani' => [
                                'code' => 'WSA-MTF',
                                'shehias' => [
                                    ['name' => 'Hawaii', 'code' => 'WSA-MTF-001'],
                                    ['name' => 'Michikichini', 'code' => 'WSA-MTF-002'],
                                    ['name' => 'Mtofaani', 'code' => 'WSA-MTF-003']
                                ]
                            ],
                            'Kidatu' => [
                                'code' => 'WSA-KDT',
                                'shehias' => [
                                    ['name' => 'Mtoni Kidatu', 'code' => 'WSA-KDT-001'],
                                    ['name' => 'Mtoni Chemchem', 'code' => 'WSA-KDT-002']
                                ]
                            ],
                            'Kianga' => [
                                'code' => 'WSA-KNG',
                                'shehias' => [
                                    ['name' => 'Masingini', 'code' => 'WSA-KNG-001'],
                                    ['name' => 'Kianga', 'code' => 'WSA-KNG-002']
                                ]
                            ],
                            'Mwera' => [
                                'code' => 'WSA-MWR',
                                'shehias' => [
                                    ['name' => 'Muembemchomeke', 'code' => 'WSA-MWR-001'],
                                    ['name' => 'Mwera', 'code' => 'WSA-MWR-002']
                                ]
                            ],
                        ]
                    ],
                    'West B' => [
                        'code' => 'WSB',
                        'wards' => [
                            'Kisauni' => [
                                'code' => 'WSB-KSN',
                                'shehias' => [
                                    ['name' => 'Tomondo', 'code' => 'WSB-KSN-001'],
                                    ['name' => 'Kisauni', 'code' => 'WSB-KSN-002'],
                                    ['name' => 'Maungani', 'code' => 'WSB-KSN-003']
                                ]
                            ],
                            'Dimani' => [
                                'code' => 'WSB-DMN',
                                'shehias' => [
                                    ['name' => 'Kombeni', 'code' => 'WSB-DMN-001'],
                                    ['name' => 'Nyamanzi', 'code' => 'WSB-DMN-002'],
                                    ['name' => 'Dimani', 'code' => 'WSB-DMN-003'],
                                    ['name' => 'Bweleo', 'code' => 'WSB-DMN-004'],
                                    ['name' => 'Fumba', 'code' => 'WSB-DMN-005']
                                ]
                            ],
                            'Mambosasa' => [
                                'code' => 'WSB-MBS',
                                'shehias' => [
                                    ['name' => 'Mambosasa', 'code' => 'WSB-MBS-001'],
                                    ['name' => 'Chunga', 'code' => 'WSB-MBS-002']
                                ]
                            ],
                            'Kipungani' => [
                                'code' => 'WSB-KPG',
                                'shehias' => [
                                    ['name' => 'Fuoni Migombani', 'code' => 'WSB-KPG-001'],
                                    ['name' => 'Fuoni Kipungani', 'code' => 'WSB-KPG-002']
                                ]
                            ],
                            'Kibondeni' => [
                                'code' => 'WSB-KBD',
                                'shehias' => [
                                    ['name' => 'Uwandani', 'code' => 'WSB-KBD-001'],
                                    ['name' => 'Kibondeni', 'code' => 'WSB-KBD-002']
                                ]
                            ],
                            'Mbweni' => [
                                'code' => 'WSB-MBW',
                                'shehias' => [
                                    ['name' => 'Kiembesamaki', 'code' => 'WSB-MBW-001'],
                                    ['name' => 'Mbweni', 'code' => 'WSB-MBW-002']
                                ]
                            ],
                            'Mombasa' => [
                                'code' => 'WSB-MOM',
                                'shehias' => [
                                    ['name' => 'Kwa Mchina', 'code' => 'WSB-MOM-001'],
                                    ['name' => 'Mombasa', 'code' => 'WSB-MOM-002'],
                                    ['name' => 'Michungwani', 'code' => 'WSB-MOM-003']
                                ]
                            ],
                            'Chukwani' => [
                                'code' => 'WSB-CHK',
                                'shehias' => [
                                    ['name' => 'Chukwani', 'code' => 'WSB-CHK-001'],
                                    ['name' => 'Shakani', 'code' => 'WSB-CHK-002']
                                ]
                            ],
                            'Jitimai' => [
                                'code' => 'WSB-JTM',
                                'shehias' => [
                                    ['name' => 'Magogoni', 'code' => 'WSB-JTM-001'],
                                    ['name' => 'Jitimai', 'code' => 'WSB-JTM-002'],
                                    ['name' => 'Sokoni', 'code' => 'WSB-JTM-003']
                                ]
                            ],
                            'Mwanakwerekwe' => [
                                'code' => 'WSB-MWK',
                                'shehias' => [
                                    ['name' => 'Mikarafuuni', 'code' => 'WSB-MWK-001'],
                                    ['name' => 'Mwanakwerekwe', 'code' => 'WSB-MWK-002']
                                ]
                            ],
                            'Melinne' => [
                                'code' => 'WSB-MLN',
                                'shehias' => [
                                    ['name' => 'Melinne', 'code' => 'WSB-MLN-001'],
                                    ['name' => 'Taveta', 'code' => 'WSB-MLN-002']
                                ]
                            ],
                            'Pangawe' => [
                                'code' => 'WSB-PNG',
                                'shehias' => [
                                    ['name' => 'Muembe Majogoo', 'code' => 'WSB-PNG-001'],
                                    ['name' => 'Pangawe', 'code' => 'WSB-PNG-002']
                                ]
                            ],
                            'Kinuni' => [
                                'code' => 'WSB-KNN',
                                'shehias' => [
                                    ['name' => 'Kinuni', 'code' => 'WSB-KNN-001'],
                                    ['name' => 'Mnarani', 'code' => 'WSB-KNN-002']
                                ]
                            ],
                            'Kijitoupele' => [
                                'code' => 'WSB-KJT',
                                'shehias' => [
                                    ['name' => 'Uzi', 'code' => 'WSB-KJT-001'],
                                    ['name' => 'Kijitoupele', 'code' => 'WSB-KJT-002']
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            'South' => [
                'code' => 'S',
                'districts' => [
                    'Central' => [
                        'code' => 'CTR',
                        'wards' => [
                            'Cheju' => [
                                'code' => 'CTR-CHJ',
                                'shehias' => [
                                    ['name' => 'Ndijani Mwembepunda', 'code' => 'CTR-CHJ-001'],
                                    ['name' => 'Ndijani Mseweni', 'code' => 'CTR-CHJ-002'],
                                    ['name' => 'Cheju', 'code' => 'CTR-CHJ-003'],
                                    ['name' => 'Zawiyani', 'code' => 'CTR-CHJ-004'],
                                    ['name' => 'Charawe', 'code' => 'CTR-CHJ-005'],
                                    ['name' => 'Ukongoroni', 'code' => 'CTR-CHJ-006'],
                                    ['name' => 'Pete', 'code' => 'CTR-CHJ-007']
                                ]
                            ],
                            'Chwaka' => [
                                'code' => 'CTR-CWK',
                                'shehias' => [
                                    ['name' => 'Pongwe', 'code' => 'CTR-CWK-001'],
                                    ['name' => 'Uroa', 'code' => 'CTR-CWK-002'],
                                    ['name' => 'Marumbi', 'code' => 'CTR-CWK-003'],
                                    ['name' => 'Jendele', 'code' => 'CTR-CWK-004'],
                                    ['name' => 'Chwaka', 'code' => 'CTR-CWK-005']
                                ]
                            ],
                            'Koani' => [
                                'code' => 'CTR-KON',
                                'shehias' => [
                                    ['name' => 'Kidimni', 'code' => 'CTR-KON-001'],
                                    ['name' => 'Koani', 'code' => 'CTR-KON-002'],
                                    ['name' => 'Machui', 'code' => 'CTR-KON-003'],
                                    ['name' => 'Miwani', 'code' => 'CTR-KON-004']
                                ]
                            ],
                            'Kiboje' => [
                                'code' => 'CTR-KBJ',
                                'shehias' => [
                                    ['name' => 'Ghana', 'code' => 'CTR-KBJ-001'],
                                    ['name' => 'Kiboje Mwembeshauri', 'code' => 'CTR-KBJ-002'],
                                    ['name' => 'Tunduni', 'code' => 'CTR-KBJ-003'],
                                    ['name' => 'Mitakawani', 'code' => 'CTR-KBJ-004'],
                                    ['name' => 'Uzini', 'code' => 'CTR-KBJ-005'],
                                    ['name' => 'Kiboje Mkwajuni', 'code' => 'CTR-KBJ-006'],
                                    ['name' => 'Mgeni Haji', 'code' => 'CTR-KBJ-007']
                                ]
                            ],
                            'Bambi' => [
                                'code' => 'CTR-BMB',
                                'shehias' => [
                                    ['name' => 'Mchangani Shamba', 'code' => 'CTR-BMB-001'],
                                    ['name' => 'Kijibwemtu', 'code' => 'CTR-BMB-002'],
                                    ['name' => 'Bambi', 'code' => 'CTR-BMB-003'],
                                    ['name' => 'Mpapa', 'code' => 'CTR-BMB-004'],
                                    ['name' => 'Pagali', 'code' => 'CTR-BMB-005'],
                                    ['name' => 'Umbuji', 'code' => 'CTR-BMB-006']
                                ]
                            ],
                            'Ubago' => [
                                'code' => 'CTR-UBG',
                                'shehias' => [
                                    ['name' => 'Ubago', 'code' => 'CTR-UBG-001'],
                                    ['name' => 'Dunga Bweni', 'code' => 'CTR-UBG-002'],
                                    ['name' => 'Dunga Kiembeni', 'code' => 'CTR-UBG-003']
                                ]
                            ],
                            'Tunguu' => [
                                'code' => 'CTR-TGU',
                                'shehias' => [
                                    ['name' => 'Binguni', 'code' => 'CTR-TGU-001'],
                                    ['name' => 'Jumbi', 'code' => 'CTR-TGU-002'],
                                    ['name' => 'Tunguu', 'code' => 'CTR-TGU-003']
                                ]
                            ],
                            'Bungi' => [
                                'code' => 'CTR-BNG',
                                'shehias' => [
                                    ['name' => 'Bungi', 'code' => 'CTR-BNG-001'],
                                    ['name' => 'Kikungwi', 'code' => 'CTR-BNG-002'],
                                    ['name' => 'Unguja Ukuu Kaebona', 'code' => 'CTR-BNG-003'],
                                    ['name' => 'Tindini', 'code' => 'CTR-BNG-004'],
                                    ['name' => 'Unguja Ukuu Kaepwani', 'code' => 'CTR-BNG-005'],
                                    ['name' => 'Uzi', 'code' => 'CTR-BNG-006'],
                                    ['name' => 'Ng\'ambwa', 'code' => 'CTR-BNG-007']
                                ]
                            ],
                        ]
                    ],
                    'South' => [
                        'code' => 'STH',
                        'wards' => [
                            'Paje' => [
                                'code' => 'STH-PJE',
                                'shehias' => [
                                    ['name' => 'Michamvi', 'code' => 'STH-PJE-001'],
                                    ['name' => 'Dongwe', 'code' => 'STH-PJE-002'],
                                    ['name' => 'Bwejuu', 'code' => 'STH-PJE-003'],
                                    ['name' => 'Paje', 'code' => 'STH-PJE-004']
                                ]
                            ],
                            'Jambiani' => [
                                'code' => 'STH-JMB',
                                'shehias' => [
                                    ['name' => 'Kitogani', 'code' => 'STH-JMB-001'],
                                    ['name' => 'Muungoni', 'code' => 'STH-JMB-002'],
                                    ['name' => 'Jambiani Kibigija', 'code' => 'STH-JMB-003'],
                                    ['name' => 'Jambiani Kikadini', 'code' => 'STH-JMB-004']
                                ]
                            ],
                            'Muyuni' => [
                                'code' => 'STH-MYN',
                                'shehias' => [
                                    ['name' => 'Muyuni A', 'code' => 'STH-MYN-001'],
                                    ['name' => 'Muyuni B', 'code' => 'STH-MYN-002'],
                                    ['name' => 'Muyuni C', 'code' => 'STH-MYN-003'],
                                    ['name' => 'Kizimkazi Dimbani', 'code' => 'STH-MYN-004'],
                                    ['name' => 'Kibuteni', 'code' => 'STH-MYN-005'],
                                    ['name' => 'Kizimkazi Mkunguni', 'code' => 'STH-MYN-006'],
                                    ['name' => 'Mtende', 'code' => 'STH-MYN-007']
                                ]
                            ],
                            'Kiongoni' => [
                                'code' => 'STH-KNG',
                                'shehias' => [
                                    ['name' => 'Kajengwa', 'code' => 'STH-KNG-001'],
                                    ['name' => 'Nganani', 'code' => 'STH-KNG-002'],
                                    ['name' => 'Kijini', 'code' => 'STH-KNG-003'],
                                    ['name' => 'Kiongoni', 'code' => 'STH-KNG-004'],
                                    ['name' => 'Mzuri', 'code' => 'STH-KNG-005'],
                                    ['name' => 'Tasani', 'code' => 'STH-KNG-006']
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            'North' => [
                'code' => 'N',
                'districts' => [
                    'North A' => [
                        'code' => 'NTA',
                        'wards' => [
                            'Chaani' => [
                                'code' => 'NTA-CHN',
                                'shehias' => [
                                    ['name' => 'Chaani Masingini', 'code' => 'NTA-CHN-001'],
                                    ['name' => 'Mchenza Shauri', 'code' => 'NTA-CHN-002'],
                                    ['name' => 'Chaani Kubwa', 'code' => 'NTA-CHN-003']
                                ]
                            ],
                            'Kinyasini' => [
                                'code' => 'NTA-KYN',
                                'shehias' => [
                                    ['name' => 'Bandamaji', 'code' => 'NTA-KYN-001'],
                                    ['name' => 'Kikobweni', 'code' => 'NTA-KYN-002'],
                                    ['name' => 'Kinyasini', 'code' => 'NTA-KYN-003'],
                                    ['name' => 'Kandwi', 'code' => 'NTA-KYN-004'],
                                    ['name' => 'Pwani Mchangani', 'code' => 'NTA-KYN-005']
                                ]
                            ],
                            'Kijini' => [
                                'code' => 'NTA-KJI',
                                'shehias' => [
                                    ['name' => 'Kigongoni', 'code' => 'NTA-KJI-001'],
                                    ['name' => 'Potoa', 'code' => 'NTA-KJI-002'],
                                    ['name' => 'Kijini Matemwe', 'code' => 'NTA-KJI-003'],
                                    ['name' => 'Mbuyutende', 'code' => 'NTA-KJI-004'],
                                    ['name' => 'Kigomani', 'code' => 'NTA-KJI-005'],
                                    ['name' => 'Juga Kuu', 'code' => 'NTA-KJI-006']
                                ]
                            ],
                            'Kivunge' => [
                                'code' => 'NTA-KVG',
                                'shehias' => [
                                    ['name' => 'Muwange', 'code' => 'NTA-KVG-001'],
                                    ['name' => 'Pitanazako', 'code' => 'NTA-KVG-002'],
                                    ['name' => 'Kivunge', 'code' => 'NTA-KVG-003']
                                ]
                            ],
                            'Kidombo' => [
                                'code' => 'NTA-KDB',
                                'shehias' => [
                                    ['name' => 'Kibeni', 'code' => 'NTA-KDB-001'],
                                    ['name' => 'Mkwajuni', 'code' => 'NTA-KDB-002'],
                                    ['name' => 'Kidombo', 'code' => 'NTA-KDB-003'],
                                    ['name' => 'Chutama', 'code' => 'NTA-KDB-004'],
                                    ['name' => 'Moga', 'code' => 'NTA-KDB-005']
                                ]
                            ],
                            'Gamba' => [
                                'code' => 'NTA-GMB',
                                'shehias' => [
                                    ['name' => 'Matemwe Kaskazini', 'code' => 'NTA-GMB-001'],
                                    ['name' => 'Matemwe Kusini', 'code' => 'NTA-GMB-002'],
                                    ['name' => 'Gamba', 'code' => 'NTA-GMB-003']
                                ]
                            ],
                            'Tumbatu' => [
                                'code' => 'NTA-TBT',
                                'shehias' => [
                                    ['name' => 'Mtakuja', 'code' => 'NTA-TBT-001'],
                                    ['name' => 'Gomani', 'code' => 'NTA-TBT-002'],
                                    ['name' => 'Uvivini', 'code' => 'NTA-TBT-003'],
                                    ['name' => 'Jongowe', 'code' => 'NTA-TBT-004']
                                ]
                            ],
                            'Kipange' => [
                                'code' => 'NTA-KPG',
                                'shehias' => [
                                    ['name' => 'Mto wa Pwani', 'code' => 'NTA-KPG-001'],
                                    ['name' => 'Mkokotoni', 'code' => 'NTA-KPG-002'],
                                    ['name' => 'Pale', 'code' => 'NTA-KPG-003'],
                                    ['name' => 'Muwanda', 'code' => 'NTA-KPG-004'],
                                    ['name' => 'Kipange', 'code' => 'NTA-KPG-005'],
                                    ['name' => 'Mchangani', 'code' => 'NTA-KPG-006']
                                ]
                            ],
                            'Bandakuu' => [
                                'code' => 'NTA-BDK',
                                'shehias' => [
                                    ['name' => 'Bandakuu', 'code' => 'NTA-BDK-001'],
                                    ['name' => 'Kiungani', 'code' => 'NTA-BDK-002'],
                                    ['name' => 'Kilindi', 'code' => 'NTA-BDK-003'],
                                    ['name' => 'Kigunda', 'code' => 'NTA-BDK-004']
                                ]
                            ],
                            'Tazari' => [
                                'code' => 'NTA-TZR',
                                'shehias' => [
                                    ['name' => 'Tazari', 'code' => 'NTA-TZR-001'],
                                    ['name' => 'Kilimani Tazari', 'code' => 'NTA-TZR-002'],
                                    ['name' => 'Kidoti', 'code' => 'NTA-TZR-003'],
                                    ['name' => 'Bwereu', 'code' => 'NTA-TZR-004'],
                                    ['name' => 'Fukuchani', 'code' => 'NTA-TZR-005']
                                ]
                            ],
                        ]
                    ],
                    'North B' => [
                        'code' => 'NTB',
                        'wards' => [
                            'Vijibweni' => [
                                'code' => 'NTB-VJB',
                                'shehias' => [
                                    ['name' => 'Donge Mtambile', 'code' => 'NTB-VJB-001'],
                                    ['name' => 'Njia ya Mtoni', 'code' => 'NTB-VJB-002'],
                                    ['name' => 'Donge Vijibweni', 'code' => 'NTB-VJB-003'],
                                    ['name' => 'Majenzi', 'code' => 'NTB-VJB-004'],
                                    ['name' => 'Donge Karange', 'code' => 'NTB-VJB-005']
                                ]
                            ],
                            'Mkataleni' => [
                                'code' => 'NTB-MKT',
                                'shehias' => [
                                    ['name' => 'Donge Pwani', 'code' => 'NTB-MKT-001'],
                                    ['name' => 'Donge Mbiji', 'code' => 'NTB-MKT-002'],
                                    ['name' => 'Mnyimbi', 'code' => 'NTB-MKT-003'],
                                    ['name' => 'Mkataleni', 'code' => 'NTB-MKT-004']
                                ]
                            ],
                            'Mahonda' => [
                                'code' => 'NTB-MHD',
                                'shehias' => [
                                    ['name' => 'Matetema', 'code' => 'NTB-MHD-001'],
                                    ['name' => 'Mahonda', 'code' => 'NTB-MHD-002'],
                                    ['name' => 'Kinduni', 'code' => 'NTB-MHD-003']
                                ]
                            ],
                            'Kitope' => [
                                'code' => 'NTB-KTP',
                                'shehias' => [
                                    ['name' => 'Kwagube', 'code' => 'NTB-KTP-001'],
                                    ['name' => 'Kitope', 'code' => 'NTB-KTP-002'],
                                    ['name' => 'Mbaleni', 'code' => 'NTB-KTP-003'],
                                    ['name' => 'Kilombero', 'code' => 'NTB-KTP-004']
                                ]
                            ],
                            'Kiwengwa' => [
                                'code' => 'NTB-KWG',
                                'shehias' => [
                                    ['name' => 'Mgambo', 'code' => 'NTB-KWG-001'],
                                    ['name' => 'Kisongoni', 'code' => 'NTB-KWG-002'],
                                    ['name' => 'Pangeni', 'code' => 'NTB-KWG-003'],
                                    ['name' => 'Upenja', 'code' => 'NTB-KWG-004'],
                                    ['name' => 'Kiwengwa', 'code' => 'NTB-KWG-005']
                                ]
                            ],
                            'Mafufuni' => [
                                'code' => 'NTB-MFF',
                                'shehias' => [
                                    ['name' => 'Kiongwe Kidogo', 'code' => 'NTB-MFF-001'],
                                    ['name' => 'Mafufuni', 'code' => 'NTB-MFF-002'],
                                    ['name' => 'Makoba', 'code' => 'NTB-MFF-003'],
                                    ['name' => 'Misufini', 'code' => 'NTB-MFF-004'],
                                    ['name' => 'Kidanzini', 'code' => 'NTB-MFF-005']
                                ]
                            ],
                            'Fujoni' => [
                                'code' => 'NTB-FJN',
                                'shehias' => [
                                    ['name' => 'Mangapwani', 'code' => 'NTB-FJN-001'],
                                    ['name' => 'Fujoni', 'code' => 'NTB-FJN-002'],
                                    ['name' => 'Zingwezingwe', 'code' => 'NTB-FJN-003'],
                                    ['name' => 'Kiombamvua', 'code' => 'NTB-FJN-004'],
                                    ['name' => 'Mkadini', 'code' => 'NTB-FJN-005']
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            'South Pemba' => [
                'code' => 'SP',
                'districts' => [
                    'Mkoani' => [
                        'code' => 'MKN',
                        'wards' => [
                            'Dodo' => [
                                'code' => 'MKN-DDO',
                                'shehias' => [
                                    ['name' => 'Dodo', 'code' => 'MKN-DDO-001'],
                                    ['name' => 'Chambani', 'code' => 'MKN-DDO-002'],
                                    ['name' => 'Ukutini', 'code' => 'MKN-DDO-003']
                                ]
                            ],
                            'Ngwachani' => [
                                'code' => 'MKN-NGW',
                                'shehias' => [
                                    ['name' => 'Wambaa', 'code' => 'MKN-NGW-001'],
                                    ['name' => 'Chumbageni', 'code' => 'MKN-NGW-002'],
                                    ['name' => 'Mgagadu', 'code' => 'MKN-NGW-003'],
                                    ['name' => 'Ngwachani', 'code' => 'MKN-NGW-004']
                                ]
                            ],
                            'Kendwa' => [
                                'code' => 'MKN-KDW',
                                'shehias' => [
                                    ['name' => 'Kendwa', 'code' => 'MKN-KDW-001'],
                                    ['name' => 'Mtangani', 'code' => 'MKN-KDW-002'],
                                    ['name' => 'Kiwani', 'code' => 'MKN-KDW-003']
                                ]
                            ],
                            'Mwambe' => [
                                'code' => 'MKN-MWB',
                                'shehias' => [
                                    ['name' => 'Mchakwe', 'code' => 'MKN-MWB-001'],
                                    ['name' => 'Shamiani', 'code' => 'MKN-MWB-002'],
                                    ['name' => 'Mwambe', 'code' => 'MKN-MWB-003'],
                                    ['name' => 'Jombwe', 'code' => 'MKN-MWB-004']
                                ]
                            ],
                            'Mkoani' => [
                                'code' => 'MKN-MKN',
                                'shehias' => [
                                    ['name' => 'Makombeni', 'code' => 'MKN-MKN-001'],
                                    ['name' => 'Ng\'ombeni', 'code' => 'MKN-MKN-002'],
                                    ['name' => 'Uweleni', 'code' => 'MKN-MKN-003'],
                                    ['name' => 'Mbuguani', 'code' => 'MKN-MKN-004'],
                                    ['name' => 'Changaweni', 'code' => 'MKN-MKN-005']
                                ]
                            ],
                            'Michenzani' => [
                                'code' => 'MKN-MCZ',
                                'shehias' => [
                                    ['name' => 'Makoongwe', 'code' => 'MKN-MCZ-001'],
                                    ['name' => 'Mbuyuni', 'code' => 'MKN-MCZ-002'],
                                    ['name' => 'Shidi', 'code' => 'MKN-MCZ-003'],
                                    ['name' => 'Michenzani', 'code' => 'MKN-MCZ-004'],
                                    ['name' => 'Stahabu', 'code' => 'MKN-MCZ-005']
                                ]
                            ],
                            'Chokocho' => [
                                'code' => 'MKN-CKC',
                                'shehias' => [
                                    ['name' => 'Mkanyageni', 'code' => 'MKN-CKC-001'],
                                    ['name' => 'Chokocho', 'code' => 'MKN-CKC-002']
                                ]
                            ],
                            'Mtambile' => [
                                'code' => 'MKN-MTB',
                                'shehias' => [
                                    ['name' => 'Mizingani', 'code' => 'MKN-MTB-001'],
                                    ['name' => 'Mtambile', 'code' => 'MKN-MTB-002'],
                                    ['name' => 'Mjimbini', 'code' => 'MKN-MTB-003'],
                                    ['name' => 'Minazini', 'code' => 'MKN-MTB-004']
                                ]
                            ],
                            'Kangani' => [
                                'code' => 'MKN-KGN',
                                'shehias' => [
                                    ['name' => 'Kisiwapanza', 'code' => 'MKN-KGN-001'],
                                    ['name' => 'Kangani', 'code' => 'MKN-KGN-002'],
                                    ['name' => 'Kuukuu', 'code' => 'MKN-KGN-003'],
                                    ['name' => 'Kengeja', 'code' => 'MKN-KGN-004'],
                                    ['name' => 'Chole', 'code' => 'MKN-KGN-005'],
                                    ['name' => 'Mkungu', 'code' => 'MKN-KGN-006']
                                ]
                            ],
                        ]
                    ],
                    'Chake Chake' => [
                        'code' => 'CHC',
                        'wards' => [
                            'Madungu' => [
                                'code' => 'CHC-MDG',
                                'shehias' => [
                                    ['name' => 'Madungu', 'code' => 'CHC-MDG-001'],
                                    ['name' => 'Chanjaani', 'code' => 'CHC-MDG-002'],
                                    ['name' => 'Shungi', 'code' => 'CHC-MDG-003']
                                ]
                            ],
                            'Tibirinzi' => [
                                'code' => 'CHC-TBR',
                                'shehias' => [
                                    ['name' => 'Tibirinzi', 'code' => 'CHC-TBR-001'],
                                    ['name' => 'Kichungwani', 'code' => 'CHC-TBR-002'],
                                    ['name' => 'Chachani', 'code' => 'CHC-TBR-003'],
                                    ['name' => 'Msingini', 'code' => 'CHC-TBR-004']
                                ]
                            ],
                            'Kilindi' => [
                                'code' => 'CHC-KLD',
                                'shehias' => [
                                    ['name' => 'Kilindi', 'code' => 'CHC-KLD-001'],
                                    ['name' => 'Mgelema', 'code' => 'CHC-KLD-002'],
                                    ['name' => 'Chonga', 'code' => 'CHC-KLD-003']
                                ]
                            ],
                            'Matale' => [
                                'code' => 'CHC-MTL',
                                'shehias' => [
                                    ['name' => 'Matale', 'code' => 'CHC-MTL-001'],
                                    ['name' => 'Mfikiwa', 'code' => 'CHC-MTL-002'],
                                    ['name' => 'Pujini', 'code' => 'CHC-MTL-003']
                                ]
                            ],
                            'Vitongoji' => [
                                'code' => 'CHC-VTG',
                                'shehias' => [
                                    ['name' => 'Ng\'ambwa', 'code' => 'CHC-VTG-001'],
                                    ['name' => 'Uwandani', 'code' => 'CHC-VTG-002'],
                                    ['name' => 'Vitongoji', 'code' => 'CHC-VTG-003']
                                ]
                            ],
                            'Ole' => [
                                'code' => 'CHC-OLE',
                                'shehias' => [
                                    ['name' => 'Mchanga Mrima', 'code' => 'CHC-OLE-001'],
                                    ['name' => 'Mjini Ole', 'code' => 'CHC-OLE-002'],
                                    ['name' => 'Ole', 'code' => 'CHC-OLE-003']
                                ]
                            ],
                            'Kibokoni' => [
                                'code' => 'CHC-KBK',
                                'shehias' => [
                                    ['name' => 'Mgogoni', 'code' => 'CHC-KBK-001'],
                                    ['name' => 'Mvumoni', 'code' => 'CHC-KBK-002'],
                                    ['name' => 'Kibokoni', 'code' => 'CHC-KBK-003'],
                                    ['name' => 'Gombani', 'code' => 'CHC-KBK-004']
                                ]
                            ],
                            'Wara' => [
                                'code' => 'CHC-WRA',
                                'shehias' => [
                                    ['name' => 'Mkoroshoni', 'code' => 'CHC-WRA-001'],
                                    ['name' => 'Wara', 'code' => 'CHC-WRA-002'],
                                    ['name' => 'Wawi', 'code' => 'CHC-WRA-003']
                                ]
                            ],
                            'Kwale' => [
                                'code' => 'CHC-KWL',
                                'shehias' => [
                                    ['name' => 'Ziwani', 'code' => 'CHC-KWL-001'],
                                    ['name' => 'Kwale', 'code' => 'CHC-KWL-002'],
                                    ['name' => 'Mbuzini', 'code' => 'CHC-KWL-003']
                                ]
                            ],
                            'Ndagoni' => [
                                'code' => 'CHC-NDG',
                                'shehias' => [
                                    ['name' => 'Ndagoni', 'code' => 'CHC-NDG-001'],
                                    ['name' => 'Wesha', 'code' => 'CHC-NDG-002'],
                                    ['name' => 'Michungwani', 'code' => 'CHC-NDG-003']
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            'North Pemba' => [
                'code' => 'NP',
                'districts' => [
                    'Wete' => [
                        'code' => 'WTE',
                        'wards' => [
                            'Gando' => [
                                'code' => 'WTE-GND',
                                'shehias' => [
                                    ['name' => 'Gando', 'code' => 'WTE-GND-001'],
                                    ['name' => 'Junguni', 'code' => 'WTE-GND-002'],
                                    ['name' => 'Ukunjwi', 'code' => 'WTE-GND-003'],
                                    ['name' => 'Fundo', 'code' => 'WTE-GND-004']
                                ]
                            ],
                            'Kinyasini' => [
                                'code' => 'WTE-KYN',
                                'shehias' => [
                                    ['name' => 'Mgogoni', 'code' => 'WTE-KYN-001'],
                                    ['name' => 'Kinyasini', 'code' => 'WTE-KYN-002'],
                                    ['name' => 'Finya', 'code' => 'WTE-KYN-003'],
                                    ['name' => 'Kizimbani', 'code' => 'WTE-KYN-004']
                                ]
                            ],
                            'Kiuyu' => [
                                'code' => 'WTE-KYU',
                                'shehias' => [
                                    ['name' => 'Kambini', 'code' => 'WTE-KYU-001'],
                                    ['name' => 'Kiuyu Minungwini', 'code' => 'WTE-KYU-002'],
                                    ['name' => 'Kiuyu Kigongoni', 'code' => 'WTE-KYU-003'],
                                    ['name' => 'Kangagani', 'code' => 'WTE-KYU-004']
                                ]
                            ],
                            'Mchanga Mdogo' => [
                                'code' => 'WTE-MCM',
                                'shehias' => [
                                    ['name' => 'Chwale', 'code' => 'WTE-MCM-001'],
                                    ['name' => 'Kinyikani', 'code' => 'WTE-MCM-002'],
                                    ['name' => 'Mchanga Mdogo', 'code' => 'WTE-MCM-003'],
                                    ['name' => 'Mpambani', 'code' => 'WTE-MCM-004'],
                                    ['name' => 'Kojani', 'code' => 'WTE-MCM-005']
                                ]
                            ],
                            'Mtambwe' => [
                                'code' => 'WTE-MTB',
                                'shehias' => [
                                    ['name' => 'Mtambwe Kaskazini', 'code' => 'WTE-MTB-001'],
                                    ['name' => 'Mtambwe Kusini', 'code' => 'WTE-MTB-002'],
                                    ['name' => 'Kisiwani', 'code' => 'WTE-MTB-003']
                                ]
                            ],
                            'Piki' => [
                                'code' => 'WTE-PKI',
                                'shehias' => [
                                    ['name' => 'Piki', 'code' => 'WTE-PKI-001'],
                                    ['name' => 'Mzambarau Takao', 'code' => 'WTE-PKI-002']
                                ]
                            ],
                            'Pandani' => [
                                'code' => 'WTE-PDN',
                                'shehias' => [
                                    ['name' => 'Pandani', 'code' => 'WTE-PDN-001'],
                                    ['name' => 'Maziwani', 'code' => 'WTE-PDN-002']
                                ]
                            ],
                            'Kiungoni' => [
                                'code' => 'WTE-KGN',
                                'shehias' => [
                                    ['name' => 'Mlindo', 'code' => 'WTE-KGN-001'],
                                    ['name' => 'Mjananza', 'code' => 'WTE-KGN-002'],
                                    ['name' => 'Kiungoni', 'code' => 'WTE-KGN-003'],
                                    ['name' => 'Pembeni', 'code' => 'WTE-KGN-004'],
                                    ['name' => 'Shengejuu', 'code' => 'WTE-KGN-005']
                                ]
                            ],
                            'Jadida' => [
                                'code' => 'WTE-JDD',
                                'shehias' => [
                                    ['name' => 'Selem', 'code' => 'WTE-JDD-001'],
                                    ['name' => 'Kipangani', 'code' => 'WTE-JDD-002'],
                                    ['name' => 'Jadida', 'code' => 'WTE-JDD-003'],
                                    ['name' => 'Mtemani', 'code' => 'WTE-JDD-004'],
                                    ['name' => 'Limbani', 'code' => 'WTE-JDD-005']
                                ]
                            ],
                            'Bopwe' => [
                                'code' => 'WTE-BPW',
                                'shehias' => [
                                    ['name' => 'Bopwe', 'code' => 'WTE-BPW-001'],
                                    ['name' => 'Utaani', 'code' => 'WTE-BPW-002']
                                ]
                            ],
                        ]
                    ],
                    'Micheweni' => [
                        'code' => 'MCW',
                        'wards' => [
                            'Msuka' => [
                                'code' => 'MCW-MSK',
                                'shehias' => [
                                    ['name' => 'Makangale', 'code' => 'MCW-MSK-001'],
                                    ['name' => 'Tondooni', 'code' => 'MCW-MSK-002'],
                                    ['name' => 'Msuka Magharibi', 'code' => 'MCW-MSK-003'],
                                    ['name' => 'Msuka Mashariki', 'code' => 'MCW-MSK-004']
                                ]
                            ],
                            'Konde' => [
                                'code' => 'MCW-KND',
                                'shehias' => [
                                    ['name' => 'Kipange', 'code' => 'MCW-KND-001'],
                                    ['name' => 'Konde', 'code' => 'MCW-KND-002'],
                                    ['name' => 'Kifundi', 'code' => 'MCW-KND-003']
                                ]
                            ],
                            'Micheweni' => [
                                'code' => 'MCW-MCW',
                                'shehias' => [
                                    ['name' => 'Shumba Mjini', 'code' => 'MCW-MCW-001'],
                                    ['name' => 'Majenzi', 'code' => 'MCW-MCW-002'],
                                    ['name' => 'Micheweni', 'code' => 'MCW-MCW-003'],
                                    ['name' => 'Chamboni', 'code' => 'MCW-MCW-004']
                                ]
                            ],
                            'Kiuyu' => [
                                'code' => 'MCW-KYU',
                                'shehias' => [
                                    ['name' => 'Shanake', 'code' => 'MCW-KYU-001'],
                                    ['name' => 'Kiuyu Mbuyuni', 'code' => 'MCW-KYU-002'],
                                    ['name' => 'Maziwa Ng\'ombe', 'code' => 'MCW-KYU-003']
                                ]
                            ],
                            'Tumbe' => [
                                'code' => 'MCW-TMB',
                                'shehias' => [
                                    ['name' => 'Tumbe Magharibi', 'code' => 'MCW-TMB-001'],
                                    ['name' => 'Tumbe Mashariki', 'code' => 'MCW-TMB-002'],
                                    ['name' => 'Mihogoni', 'code' => 'MCW-TMB-003']
                                ]
                            ],
                            'Chimba' => [
                                'code' => 'MCW-CHM',
                                'shehias' => [
                                    ['name' => 'Kinowe', 'code' => 'MCW-CHM-001'],
                                    ['name' => 'Chimba', 'code' => 'MCW-CHM-002'],
                                    ['name' => 'Shumba Viamboni', 'code' => 'MCW-CHM-003']
                                ]
                            ],
                            'Sizini' => [
                                'code' => 'MCW-SZN',
                                'shehias' => [
                                    ['name' => 'Sizini', 'code' => 'MCW-SZN-001'],
                                    ['name' => 'Mjini Wingwi', 'code' => 'MCW-SZN-002']
                                ]
                            ],
                            'Wingwi' => [
                                'code' => 'MCW-WGW',
                                'shehias' => [
                                    ['name' => 'Wingwi Mapofu', 'code' => 'MCW-WGW-001'],
                                    ['name' => 'Wingwi Njuguni', 'code' => 'MCW-WGW-002'],
                                    ['name' => 'Mtemani', 'code' => 'MCW-WGW-003']
                                ]
                            ],
                        ]
                    ],
                ]
            ],
        ];

        // Insert data with is_active = 1
        foreach ($data as $regionName => $regionData) {
            $region = Region::updateOrCreate(
                ['code' => $regionData['code']],
                [
                    'name' => $regionName,
                    'is_active' => 1
                ]
            );

            foreach ($regionData['districts'] as $districtName => $districtData) {
                $district = District::updateOrCreate(
                    ['code' => $districtData['code']],
                    [
                        'region_id' => $region->id,
                        'name' => $districtName,
                        'is_active' => 1
                    ]
                );

                foreach ($districtData['wards'] as $wardName => $wardData) {
                    $ward = Ward::updateOrCreate(
                        [
                            'district_id' => $district->id,
                            'name' => $wardName
                        ],
                        [
                            'code' => $wardData['code'],
                            'is_active' => 1
                        ]
                    );

                    foreach ($wardData['shehias'] as $shehiaData) {
                        Shehia::updateOrCreate(
                            [
                                'ward_id' => $ward->id,
                                'name' => $shehiaData['name']
                            ],
                            [
                                'code' => $shehiaData['code'],
                                'is_active' => 1
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('Zanzibar administrative data seeded successfully!');
        $this->command->info('Regions: ' . Region::count());
        $this->command->info('Districts: ' . District::count());
        $this->command->info('Wards: ' . Ward::count());
        $this->command->info('Shehias: ' . Shehia::count());
    }
}