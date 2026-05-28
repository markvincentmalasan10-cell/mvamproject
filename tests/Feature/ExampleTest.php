<?php

namespace Tests\Feature;

use App\Models\UserAccount;
use App\Models\Student;
use App\Models\Degree;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_the_login_page_returns_a_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_admin_can_log_in(): void
    {
        UserAccount::updateOrCreate([
            'username' => 'admin'
        ], [
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
            'is_first_login' => false,
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('user_role', 'admin');
    }

    public function test_admin_crud_pages_load(): void
    {
        $this->withSession([
            'user_account_id' => 1,
            'logged_user' => 'admin',
            'user_role' => 'admin',
            'is_first_login' => false,
        ]);

        $this->get('/student')->assertStatus(200);
        $this->get('/teacher')->assertStatus(200);
        $this->get('/degrees')->assertStatus(200);
        $this->get('/students/export/excel')->assertStatus(200);
        $this->get('/students/export/pdf')->assertStatus(200);
    }

    public function test_crud_sync_returns_versions(): void
    {
        $this->get('/crud-sync')
            ->assertStatus(200)
            ->assertJsonStructure(['versions']);
    }

    public function test_admin_student_crud_urls_work(): void
    {
        $this->withSession([
            'user_account_id' => 1,
            'logged_user' => 'admin',
            'user_role' => 'admin',
            'is_first_login' => false,
        ]);

        $this->get('/student/create')->assertStatus(200);

        $this->postJson('/students', [
            'fname' => 'Maria',
            'mname' => 'Santos',
            'lname' => 'Reyes',
            'email' => 'maria.reyes@example.com',
            'contactno' => '09170000000',
            'degree_id' => null,
            'username' => 'mariareyes',
            'password' => 'secret123',
        ])->assertStatus(200);

        $student = Student::where('email', 'maria.reyes@example.com')->firstOrFail();

        $this->get("/student/{$student->id}")->assertStatus(200);
        $this->get("/student/{$student->id}/edit")->assertStatus(200);

        $this->putJson("/students/{$student->id}", [
            'fname' => 'Maria',
            'mname' => null,
            'lname' => 'Reyes',
            'email' => 'maria.updated@example.com',
            'contactno' => '09171111111',
            'degree_id' => null,
        ])->assertStatus(200);

        $this->deleteJson("/students/{$student->id}")->assertStatus(200);
    }

    public function test_admin_degree_crud_urls_work(): void
    {
        $this->withSession([
            'user_account_id' => 1,
            'logged_user' => 'admin',
            'user_role' => 'admin',
            'is_first_login' => false,
        ]);

        $this->get('/degrees')->assertStatus(200);
        $this->get('/degrees/create')->assertStatus(200);

        $this->postJson('/degrees', [
            'degree_title' => 'BS Information Technology',
        ])->assertStatus(200);

        $degree = Degree::where('degree_title', 'BS Information Technology')->firstOrFail();

        $this->get("/degrees/{$degree->id}")->assertStatus(200);
        $this->get("/degrees/{$degree->id}/edit")->assertStatus(200);

        $this->putJson("/degrees/{$degree->id}", [
            'degree_title' => 'BS Computer Science',
        ])->assertStatus(200);

        $this->deleteJson("/degrees/{$degree->id}")->assertStatus(200);
    }
}
