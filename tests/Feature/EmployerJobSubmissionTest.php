<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerJobSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_submit_a_job_listing(): void
    {
        $employer = User::factory()->create([
            'role' => 'employer',
        ]);

        $response = $this->actingAs($employer)->postJson('/employer/jobs', [
            'title' => 'Driver',
            'company_name' => 'Acme Logistics',
            'location' => 'Doha, Qatar',
            'employment_type' => 'full-time',
            'posts' => '10',
            'description' => 'We need a reliable driver to manage deliveries across the city and maintain safe driving standards.',
            'requirements' => 'Candidates must have a valid driving licence and at least two years of experience in delivery operations.',
            'application_deadline' => now()->addWeek()->toDateString(),
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('job_posts', [
            'employer_id' => $employer->id,
            'title' => 'Driver',
        ]);
    }
}
