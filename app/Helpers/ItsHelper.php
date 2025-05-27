<?php

namespace App\Helpers;

use App\Models\Fianut\User;

class ItsHelper
{
     public static function generateInstanceCode(string $instanceName): string
     {
          $firstLetter = strtoupper(substr(trim($instanceName), 0, 1));
          $datePart = date('nd'); // e.g., 520 for May 20

          $baseCode = $firstLetter . $datePart;

          $existingCount = User::where('instance_code', 'like', $baseCode . '%')->count();
          $counter = str_pad($existingCount + 1, 2, '0', STR_PAD_LEFT);

          return $baseCode . $counter;
     }

     public static function generateReferralCode(string $companyName): string
     {
          // Sanitize and split company name
          $words = explode(' ', strtoupper(preg_replace('/[^A-Za-z0-9 ]/', '', $companyName)));

          // Take first 2 letters of up to 3 words
          $prefix = '';
          foreach (array_slice($words, 0, 3) as $word) {
               $prefix .= substr($word, 0, 2);
          }

          $prefix = substr($prefix, 0, 6); // Limit to 6 chars max

          // Add 3 random digits from timestamp or rand
          $random = mt_rand(100, 999);
          $baseCode = $prefix . $random;

          $referralCode = $baseCode;
          $counter = 1;

          // Ensure uniqueness
          while (User::where('referral_code', $referralCode)->exists()) {
               $referralCode = $baseCode . $counter;
               $counter++;
          }

          return $referralCode;
     }
}
