<?php

namespace Tests\Feature\Theme;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The active_theme setting is inserted by a migration; RefreshDatabase
        // runs all migrations, so the setting exists for every test.
    }

    public function test_api_exposes_theme_list_and_default_active_theme(): void
    {
        $this->getJson('/api/site-settings')
            ->assertOk()
            ->assertJsonPath('data.active_theme', 'classic')
            ->assertJsonPath('data.settings.active_theme', 'classic')
            ->assertJsonCount(2, 'data.themes')
            ->assertJsonPath('data.themes.0.key', 'classic')
            ->assertJsonPath('data.themes.1.key', 'showroom');
    }

    public function test_admin_can_switch_the_active_theme(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.settings.site-settings.update'), [
                'active_theme' => 'showroom',
            ])
            ->assertRedirect(route('admin.settings.site-settings'));

        $this->assertDatabaseHas('site_settings', [
            'key' => 'active_theme',
            'value' => 'showroom',
        ]);

        $this->getJson('/api/site-settings')
            ->assertOk()
            ->assertJsonPath('data.active_theme', 'showroom');
    }

    public function test_admin_cannot_set_an_unregistered_theme(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('admin.settings.site-settings'))
            ->post(route('admin.settings.site-settings.update'), [
                'active_theme' => 'does-not-exist',
            ])
            ->assertSessionHasErrors('active_theme');

        $this->assertDatabaseHas('site_settings', [
            'key' => 'active_theme',
            'value' => 'classic',
        ]);
    }

    public function test_admin_settings_page_renders_theme_selector(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.settings.site-settings'))
            ->assertOk()
            ->assertSee('Homepage Theme')
            ->assertSee('Classic')
            ->assertSee('Showroom');
    }
}
