<?php

namespace App\Helpers;

/**
 * Canonical master list of Bangladesh's 64 districts keyed by name
 * with their division. Single source of truth used by migrations,
 * seeders and the admin delivery-zone district picker.
 */
final class BdDistricts
{
    /**
     * @var array<string, string> district name => division name
     */
    public const DISTRICTS = [
        // Dhaka Division
        'Dhaka' => 'Dhaka Division',
        'Faridpur' => 'Dhaka Division',
        'Gazipur' => 'Dhaka Division',
        'Gopalganj' => 'Dhaka Division',
        'Kishoreganj' => 'Dhaka Division',
        'Madaripur' => 'Dhaka Division',
        'Manikganj' => 'Dhaka Division',
        'Munshiganj' => 'Dhaka Division',
        'Narayanganj' => 'Dhaka Division',
        'Narsingdi' => 'Dhaka Division',
        'Rajbari' => 'Dhaka Division',
        'Shariatpur' => 'Dhaka Division',
        'Tangail' => 'Dhaka Division',

        // Mymensingh Division
        'Jamalpur' => 'Mymensingh Division',
        'Mymensingh' => 'Mymensingh Division',
        'Netrokona' => 'Mymensingh Division',
        'Sherpur' => 'Mymensingh Division',

        // Chittagong Division
        'Bandarban' => 'Chittagong Division',
        'Brahmanbaria' => 'Chittagong Division',
        'Chandpur' => 'Chittagong Division',
        'Chittagong' => 'Chittagong Division',
        'Comilla' => 'Chittagong Division',
        'Cox\'s Bazar' => 'Chittagong Division',
        'Feni' => 'Chittagong Division',
        'Khagrachhari' => 'Chittagong Division',
        'Lakshmipur' => 'Chittagong Division',
        'Noakhali' => 'Chittagong Division',
        'Rangamati' => 'Chittagong Division',

        // Khulna Division
        'Bagerhat' => 'Khulna Division',
        'Chuadanga' => 'Khulna Division',
        'Jessore' => 'Khulna Division',
        'Jhenaidah' => 'Khulna Division',
        'Khulna' => 'Khulna Division',
        'Kushtia' => 'Khulna Division',
        'Magura' => 'Khulna Division',
        'Meherpur' => 'Khulna Division',
        'Narail' => 'Khulna Division',
        'Satkhira' => 'Khulna Division',

        // Rajshahi Division
        'Bogra' => 'Rajshahi Division',
        'Joypurhat' => 'Rajshahi Division',
        'Naogaon' => 'Rajshahi Division',
        'Natore' => 'Rajshahi Division',
        'Nawabganj' => 'Rajshahi Division',
        'Pabna' => 'Rajshahi Division',
        'Rajshahi' => 'Rajshahi Division',
        'Sirajganj' => 'Rajshahi Division',

        // Rangpur Division
        'Dinajpur' => 'Rangpur Division',
        'Gaibandha' => 'Rangpur Division',
        'Kurigram' => 'Rangpur Division',
        'Lalmonirhat' => 'Rangpur Division',
        'Nilphamari' => 'Rangpur Division',
        'Panchagarh' => 'Rangpur Division',
        'Rangpur' => 'Rangpur Division',
        'Thakurgaon' => 'Rangpur Division',

        // Sylhet Division
        'Habiganj' => 'Sylhet Division',
        'Maulvibazar' => 'Sylhet Division',
        'Sunamganj' => 'Sylhet Division',
        'Sylhet' => 'Sylhet Division',

        // Barisal Division
        'Barguna' => 'Barisal Division',
        'Barisal' => 'Barisal Division',
        'Bhola' => 'Barisal Division',
        'Jhalokati' => 'Barisal Division',
        'Patuakhali' => 'Barisal Division',
        'Pirojpur' => 'Barisal Division',
    ];

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return self::DISTRICTS;
    }

    /**
     * Districts grouped by division, preserving canonical order.
     *
     * @return array<string, list<string>>
     */
    public static function groupedByDivision(): array
    {
        $grouped = [];

        foreach (self::DISTRICTS as $name => $division) {
            $grouped[$division][] = $name;
        }

        return $grouped;
    }

    /**
     * Districts of Greater Dhaka (used for legacy zone `type` hints).
     *
     * @return list<string>
     */
    public static function greaterDhaka(): array
    {
        return [
            'Dhaka', 'Gazipur', 'Narayanganj', 'Munshiganj',
            'Manikganj', 'Narsingdi', 'Madaripur', 'Shariatpur',
            'Tangail', 'Kishoreganj', 'Faridpur', 'Gopalganj', 'Rajbari',
        ];
    }
}
