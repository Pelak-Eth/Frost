<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Verifies the three Blade pages that previously hosted TinyMCE now render
 * the Quill editor markup and have shed every TinyMCE remnant.
 */
class AnnouncementEditorMarkupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\PreventRequestForgery::class);
    }

    private function createAdminUser(): User
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Admin', 'description' => 'Admin role',
        ]);
        $managerRole = Role::firstOrCreate(['name' => 'manager'], [
            'display_name' => 'Manager', 'description' => 'Manager role',
        ]);

        $user = User::factory()->create([
            'store' => 1,
            'password' => Hash::make('password'),
        ]);

        $user->roles()->attach([$adminRole->id, $managerRole->id]);

        return $user->fresh();
    }

    public function test_announcement_create_page_renders_quill_markup(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get('/announcements/create');

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('data-quill', $html);
        $this->assertStringContainsString('data-quill-target="content"', $html);
        $this->assertStringContainsString('data-quill-toolbar="full"', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+type="hidden"[^>]+name="content"/', $html);
        $this->assertStringNotContainsString('tinymce', strtolower($html));
    }

    public function test_announcement_edit_page_seeds_hidden_input_with_existing_content(): void
    {
        $user = $this->createAdminUser();
        $announcement = Announcement::create([
            'user_id' => $user->id,
            'type' => 'default',
            'title' => 'Test announcement',
            'content' => '<p>hello world</p>',
            'sticky' => false,
        ]);

        $response = $this->actingAs($user)->get('/announcements/' . $announcement->id . '/edit');

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('data-quill-target="content"', $html);
        $this->assertStringContainsString('data-quill-toolbar="full"', $html);
        // The hidden input value attribute carries the existing HTML, escaped.
        $this->assertStringContainsString('value="&lt;p&gt;hello world&lt;/p&gt;"', $html);
        $this->assertStringNotContainsString('tinymce', strtolower($html));
    }

    public function test_dashboard_renders_compact_quill_for_comment_modal(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $html = $response->getContent();

        $this->assertStringContainsString('data-quill-target="reply"', $html);
        $this->assertStringContainsString('data-quill-toolbar="compact"', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+type="hidden"[^>]+id="reply"/', $html);
        $this->assertStringNotContainsString('tinymce', strtolower($html));
    }
}
