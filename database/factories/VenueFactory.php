<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
class VenueFactory extends Factory
{

    public function definition(): array
    {
        $venues = [
            'قاعة المؤتمرات الكبرى',
            'مسرح المدينة الثقافي',
            'فندق الشيراتون - الصالة الملكية',
            'الملعب الرياضي المغلق',
            'مساحة العمل المشتركة (Co-working)',
            'حديقة الأوركيد للمناسبات',
        ];

        return [
            'name' => fake()->randomElement($venues),
        ];
    }
}
