<?php

namespace Tests\Feature;

use App\Models\MuseumAd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MuseumAdTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_an_image_ad_for_the_monitor(): void
    {
        Storage::fake('public');

        $this->post(route('queue.ads.store'), [
            'title' => 'Pameran Sejarah',
            'description' => 'Kunjungi pameran museum.',
            'image' => UploadedFile::fake()->image('pameran.jpeg'),
            'duration_seconds' => 12,
            'is_active' => '1',
        ])->assertRedirect(route('queue.admin').'#carousel-management')
            ->assertSessionHas('success');

        $ad = MuseumAd::firstOrFail();
        $this->assertSame('Pameran Sejarah', $ad->title);
        $this->assertSame(12, $ad->duration_seconds);
        $this->assertTrue($ad->is_active);
        Storage::disk('public')->assertExists($ad->image_path);
    }

    public function test_monitor_ads_feed_returns_only_active_ads_in_display_order(): void
    {
        MuseumAd::create([
            'title' => 'Kedua',
            'image_path' => 'museum-ads/second.jpg',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        MuseumAd::create([
            'title' => 'Pertama',
            'image_path' => 'museum-ads/first.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        MuseumAd::create([
            'title' => 'Nonaktif',
            'image_path' => 'museum-ads/inactive.jpg',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $this->getJson(route('queue.ads.data'))
            ->assertOk()
            ->assertJsonCount(2, 'ads')
            ->assertJsonPath('ads.0.title', 'Pertama')
            ->assertJsonPath('ads.1.title', 'Kedua');
    }

    public function test_admin_can_toggle_an_ad_and_delete_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('museum-ads/slide.jpg', 'image contents');

        $ad = MuseumAd::create([
            'title' => 'Pameran Museum',
            'image_path' => 'museum-ads/slide.jpg',
            'is_active' => true,
        ]);

        $this->patch(route('queue.ads.toggle', $ad))
            ->assertRedirect(route('queue.admin').'#carousel-management');
        $this->assertDatabaseHas('museum_ads', ['id' => $ad->id, 'is_active' => false]);

        $this->delete(route('queue.ads.destroy', $ad))
            ->assertRedirect(route('queue.admin').'#carousel-management');
        $this->assertDatabaseMissing('museum_ads', ['id' => $ad->id]);
        Storage::disk('public')->assertMissing('museum-ads/slide.jpg');
    }
}
