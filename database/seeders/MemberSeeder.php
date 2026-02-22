<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('si_LK'); // Sri Lankan locale
        $faker->addProvider(new \Faker\Provider\en_US\Person($faker)); // fallback for names

        $sriLankanDistricts = [
            ['district' => 'Colombo',      'divisions' => ['Colombo Central', 'Colombo East', 'Colombo West', 'Dehiwala', 'Ratmalana']],
            ['district' => 'Gampaha',      'divisions' => ['Gampaha', 'Negombo', 'Minuwangoda', 'Ja-Ela', 'Katana']],
            ['district' => 'Kalutara',     'divisions' => ['Kalutara', 'Beruwala', 'Aluthgama', 'Panadura', 'Horana']],
            ['district' => 'Kandy',        'divisions' => ['Kandy', 'Akurana', 'Gampola', 'Nawalapitiya', 'Hatton']],
            ['district' => 'Matale',       'divisions' => ['Matale', 'Dambulla', 'Rattota', 'Ukuwela', 'Galewela']],
            ['district' => 'Kurunegala',   'divisions' => ['Kurunegala', 'Kuliyapitiya', 'Maho', 'Pannala', 'Wariyapola']],
            ['district' => 'Galle',        'divisions' => ['Galle', 'Hikkaduwa', 'Ambalangoda', 'Elpitiya', 'Karandeniya']],
            ['district' => 'Matara',       'divisions' => ['Matara', 'Weligama', 'Akuressa', 'Kamburupitiya', 'Hakmana']],
        ];

        $sriLankanFirstNames = [
            'Male'   => ['Kasun', 'Nuwan', 'Chamara', 'Lakmal', 'Pradeep', 'Mahesh', 'Tharaka', 'Dinesh', 'Asanka', 'Chathura', 'Buddhika', 'Sajith', 'Randika', 'Isuru', 'Harsha'],
            'Female' => ['Dilini', 'Nadeesha', 'Sachini', 'Thilini', 'Anusha', 'Kavindi', 'Malsha', 'Hiruni', 'Sanduni', 'Piumika', 'Chathurika', 'Nimasha', 'Dulani', 'Amaya', 'Sewwandi'],
        ];

        $sriLankanLastNames = ['Perera', 'Silva', 'Fernando', 'Dissanayake', 'Bandara', 'Rajapaksa', 'Wickramasinghe', 'Jayasuriya', 'Gunawardena', 'Herath', 'Seneviratne', 'Madushanka', 'Rathnayake', 'Pathirana', 'Kumara'];

        $schools = ['Nalanda College', 'Royal College', 'Ananda College', 'Dharmaraja College', 'Trinity College', 'Mahinda College', 'Richmond College', 'Zahira College', 'DS Senanayake College', 'Visakha Vidyalaya', 'Musaeus College', 'Bishop\'s College', 'Holy Family Convent', 'Mahamaya Girls\' College'];

        $occupations = ['Engineer', 'Teacher', 'Doctor', 'Lawyer', 'Accountant', 'Businessman', 'Lecturer', 'Nurse', 'Pharmacist', 'Architect', 'IT Specialist', 'Government Officer', 'Bank Officer', 'Entrepreneur', 'Farmer'];

        $cities = ['Colombo', 'Kandy', 'Galle', 'Negombo', 'Kurunegala', 'Matara', 'Gampaha', 'Ratnapura', 'Badulla', 'Anuradhapura', 'Polonnaruwa', 'Trincomalee', 'Jaffna', 'Batticaloa', 'Hambantota'];

        $usedNics = [];
        $usedMembershipNumbers = [];

        for ($i = 1; $i <= 50; $i++) {
            $gender    = $faker->randomElement(['Male', 'Female']);
            $firstName = $faker->randomElement($sriLankanFirstNames[$gender]);
            $lastName  = $faker->randomElement($sriLankanLastNames);
            $initials  = strtoupper(substr($firstName, 0, 1)) . '.';
            $nameWithInitials = "$initials $lastName";

            $birthday   = $faker->dateTimeBetween('-65 years', '-18 years');
            $birthYear  = (int) $birthday->format('Y');
            $married    = $faker->boolean(65);

            // Generate unique NIC
            do {
                $nic = $this->generateNic($birthYear, $faker);
            } while (in_array($nic, $usedNics));
            $usedNics[] = $nic;

            // Membership number (only for 'existing' type)
            $type = $faker->randomElement(['new', 'existing']);
            $membershipNumber = null;
            if ($type === 'existing') {
                do {
                    $membershipNumber = $faker->numberBetween(1000, 9999);
                } while (in_array($membershipNumber, $usedMembershipNumbers));
                $usedMembershipNumbers[] = $membershipNumber;
            }

            // District & divisions
            $location          = $faker->randomElement($sriLankanDistricts);
            $district          = $location['district'];
            $electionDivision  = $faker->randomElement($location['divisions']);
            $gramaNiladhari    = $faker->randomElement($location['divisions']) . ' ' . $faker->randomElement(['North', 'South', 'East', 'West', 'Central']);

            // Children (only if married)
            $childrenInfo = null;
            if ($married && $faker->boolean(60)) {
                $childCount   = $faker->numberBetween(1, 3);
                $childrenInfo = [];
                for ($c = 0; $c < $childCount; $c++) {
                    $childrenInfo[] = [
                        'name'   => $faker->randomElement(array_merge($sriLankanFirstNames['Male'], $sriLankanFirstNames['Female'])) . ' ' . $lastName,
                        'school' => $faker->randomElement($schools),
                    ];
                }
            }

            // Siblings (only if unmarried)
            $siblingsInfo = null;
            if (!$married && $faker->boolean(50)) {
                $siblingCount = $faker->numberBetween(1, 3);
                $siblingNames = [];
                for ($s = 0; $s < $siblingCount; $s++) {
                    $sibGender     = $faker->randomElement(['Male', 'Female']);
                    $siblingNames[] = $faker->randomElement($sriLankanFirstNames[$sibGender]) . ' ' . $lastName;
                }
                $siblingsInfo = implode(', ', $siblingNames);
            }

            // School info
            $schoolRegisterYear = $faker->numberBetween($birthYear + 5, min($birthYear + 18, (int) now()->year));
            $dateJoinedSchool   = Carbon::create($schoolRegisterYear, $faker->numberBetween(1, 3), $faker->numberBetween(1, 28));

            DB::table('members')->insert([
                'membership_number'        => $membershipNumber,
                'name_with_initials'       => $nameWithInitials,
                'birthday'                 => $birthday->format('Y-m-d'),
                'gender'                   => $gender,
                'married'                  => $married,
                'nic_number'               => $nic,
                'phone_number'             => '07' . $faker->numberBetween(0, 9) . $faker->numerify('#######'),
                'email'                    => $faker->boolean(70) ? strtolower($firstName . '.' . $lastName . $faker->numberBetween(1, 99) . '@' . $faker->randomElement(['gmail.com', 'yahoo.com', 'hotmail.com'])) : null,
                'occupation'               => $faker->randomElement($occupations),
                'address'                  => $faker->numberBetween(1, 250) . ', ' . $faker->randomElement(['Galle Road', 'Kandy Road', 'High Level Road', 'Negombo Road', 'Peradeniya Road', 'Main Street']),
                'current_city'             => $faker->randomElement($cities),
                'district'                 => $district,
                'election_division'        => $electionDivision,
                'grama_niladhari_division' => $gramaNiladhari,
                'school_register_year'     => $schoolRegisterYear,
                'admission_number'         => $faker->boolean(60) ? strtoupper($faker->bothify('??####')) : null,
                'date_joined_school'       => $dateJoinedSchool->format('Y-m-d'),
                'children_info'            => $childrenInfo ? json_encode($childrenInfo) : null,
                'siblings_info'            => $siblingsInfo,
                'type'                     => $type,
                'created_at'               => now(),
                'updated_at'               => now(),
            ]);
        }

        $this->command->info('✅ 50 members seeded successfully.');
    }

    private function generateNic(int $birthYear, \Faker\Generator $faker): string
    {
        // New format (12 digits): 199012345678
        if ($birthYear >= 1968 && $faker->boolean(60)) {
            $dayOfYear = $faker->numberBetween(1, 366);
            return sprintf('%04d%03d%04d', $birthYear, $dayOfYear, $faker->numberBetween(0, 9999));
        }

        // Old format (9 digits + V/X): 901234567V
        $shortYear = substr((string) $birthYear, -2);
        $dayOfYear = $faker->numberBetween(1, 366);
        $serial    = $faker->numberBetween(0, 9999);
        $suffix    = $faker->randomElement(['V', 'X']);

        return sprintf('%s%03d%04d%s', $shortYear, $dayOfYear, $serial, $suffix);
    }
}
