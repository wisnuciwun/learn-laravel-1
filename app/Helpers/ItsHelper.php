<?php

namespace App\Helpers;

use App\Models\Fianut\Images;
use App\Models\Fianut\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItsHelper
{
     public static function verifyToken(string $token)
     {
          $verified = User::with(['instance'])->whereNotNull('token')->where('token', $token)->first();

          if (!$verified) {
               abort(401, 'Hi, I need token please');
          }

          return $verified;
     }

     public static function verifyAsAdmin(string $token): void
     {
          $verified = User::where('token', $token)->whereNotNull('token')->where('name', '8uset9w4dmin')->first();

          if (!$verified) {
               abort(401, 'Hi, I need token please');
          }
     }


     public static function generateTransactionCode(string $instanceCode): string
     {
          $timestamp = (int) (microtime(true) * 1000);
          $encodedTime = strtoupper(base_convert($timestamp, 10, 36)); // e.g. "LQ1VJ83"
          $random = strtoupper(Str::random(3)); // e.g. "Z8M"

          return "{$encodedTime}-{$instanceCode}-{$random}";
     }


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

     public static function saveImage(string $type, bool $replace, $existing_img_path, $req = null, $image_name = 'image')
     {
          if ($req->hasFile($image_name)) {
               // Upload new image
               if ($type == 'system') {
                    $path = $req->file($image_name)->store('public/fianut/system');
               } else if ($type == 'client') {
                    $path = $req->file($image_name)->store('public/fianut/client');
               } else {
                    $path = $req->file($image_name)->store('public/fianut/other');
               }

               // Delete the old image if it exists
               if ($replace) {
                    Storage::delete($existing_img_path);
               }

               return $path;
          }
     }
}
