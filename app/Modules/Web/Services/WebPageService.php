<?php

namespace App\Modules\Web\Services;

use App\Enums\DarkMode;
use App\Modules\Banners\Models\BannerModel;
use App\Modules\Banners\Models\BannerQuoteModel;
use App\Modules\Enquiries\Models\Enquiry;
use App\Modules\FAQs\Models\FAQModel;
use App\Modules\Languages\Models\LanguageModel;
use App\Modules\Pages\Models\PageModel;
use Illuminate\Support\Collection;

class WebPageService
{
    public function getData(string $slug): PageModel
    {
        return PageModel::where('url', $slug)->firstOrFail();
    }
    
    public function getBannerImage(): BannerModel
    {
        return BannerModel::inRandomOrder()->firstOrFail();
    }
    
    public function getBannerQuote(): BannerQuoteModel
    {
        return BannerQuoteModel::inRandomOrder()->firstOrFail();
    }
    
    public function getFaqs(): Collection
    {
        return FAQModel::lazy(100)->collect();
    }
    
    public function getLanguages(): Collection
    {
        return LanguageModel::lazy(100)->collect();
    }
    
    public function createEnquiry(array $data): Enquiry
    {
        return Enquiry::create($data);
    }

    public function toggleDarkMode(): void
    {
        $user = request()->user();
        $user->update(
            [
                'dark_mode' => $user->dark_mode == DarkMode::No->value() ? DarkMode::Yes->value() : DarkMode::No->value(),
            ]
        );
    }
}