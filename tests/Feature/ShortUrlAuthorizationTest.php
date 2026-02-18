<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole(RoleEnum::ADMIN->value);

        $response = $this->actingAs($admin)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/admin',
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_member_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->create(['company_id' => $company->id]);
        $member->assignRole(RoleEnum::MEMBER->value);

        $response = $this->actingAs($member)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/member',
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_superadmin_cannot_create_short_urls(): void
    {
        $superAdmin = User::factory()->create(['company_id' => null]);
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $response = $this->actingAs($superAdmin)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com/superadmin',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_only_sees_short_urls_created_in_their_own_company(): void
    {
        $ownCompany = Company::factory()->create();
        $otherCompany = Company::factory()->create();

        $admin = User::factory()->create(['company_id' => $ownCompany->id]);
        $admin->assignRole(RoleEnum::ADMIN->value);

        $ownCompanyUser = User::factory()->create(['company_id' => $ownCompany->id]);
        $ownCompanyUser->assignRole(RoleEnum::MANAGER->value);

        $otherCompanyUser = User::factory()->create(['company_id' => $otherCompany->id]);
        $otherCompanyUser->assignRole(RoleEnum::MANAGER->value);

        $ownCompanyUrl = ShortUrl::create([
            'user_id' => $ownCompanyUser->id,
            'company_id' => $ownCompany->id,
            'original_url' => 'https://own-company.test',
            'short_code' => 'OWN123',
            'is_active' => true,
        ]);

        $otherCompanyUrl = ShortUrl::create([
            'user_id' => $otherCompanyUser->id,
            'company_id' => $otherCompany->id,
            'original_url' => 'https://other-company.test',
            'short_code' => 'OTH123',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('short-urls.index'));

        $response->assertOk();

        $shortUrls = $response->viewData('shortUrls')->getCollection();
        $this->assertTrue($shortUrls->contains('id', $ownCompanyUrl->id));
        $this->assertFalse($shortUrls->contains('id', $otherCompanyUrl->id));
    }

    public function test_member_only_sees_short_urls_created_by_themselves(): void
    {
        $company = Company::factory()->create();

        $member = User::factory()->create(['company_id' => $company->id]);
        $member->assignRole(RoleEnum::MEMBER->value);

        $anotherUser = User::factory()->create(['company_id' => $company->id]);
        $anotherUser->assignRole(RoleEnum::MANAGER->value);

        $ownUrl = ShortUrl::create([
            'user_id' => $member->id,
            'company_id' => $company->id,
            'original_url' => 'https://self-url.test',
            'short_code' => 'SELF12',
            'is_active' => true,
        ]);

        $otherUrl = ShortUrl::create([
            'user_id' => $anotherUser->id,
            'company_id' => $company->id,
            'original_url' => 'https://other-url.test',
            'short_code' => 'OTHER1',
            'is_active' => true,
        ]);

        $response = $this->actingAs($member)->get(route('short-urls.index'));

        $response->assertOk();

        $shortUrls = $response->viewData('shortUrls')->getCollection();
        $this->assertTrue($shortUrls->contains('id', $ownUrl->id));
        $this->assertFalse($shortUrls->contains('id', $otherUrl->id));
    }

    public function test_short_urls_are_publicly_resolvable_and_redirect_to_original_url(): void
    {
        $company = Company::factory()->create();
        $owner = User::factory()->create(['company_id' => $company->id]);
        $owner->assignRole(RoleEnum::MANAGER->value);

        $shortUrl = ShortUrl::create([
            'user_id' => $owner->id,
            'company_id' => $company->id,
            'original_url' => 'https://example.com/target',
            'short_code' => 'PUB123',
            'is_active' => true,
        ]);

        $this->get('/' . $shortUrl->short_code)
            ->assertRedirect($shortUrl->original_url);
    }
}
