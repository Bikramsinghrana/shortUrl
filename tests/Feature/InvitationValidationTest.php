<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_superadmin_sees_validation_message_for_duplicate_company_name(): void
    {
        $superAdmin = User::factory()->create(['company_id' => null]);
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

        Company::create([
            'name' => 'Acme Corp',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)
            ->from(route('invitations.create'))
            ->post(route('invitations.store'), [
                'name' => 'Invited Admin',
                'email' => 'invite-admin@example.com',
                'role' => RoleEnum::ADMIN->value,
                'company_option' => 'new',
                'company_name' => 'Acme Corp',
            ]);

        $response->assertRedirect(route('invitations.create'));
        $response->assertSessionHasErrors(['company_name']);
    }
}
