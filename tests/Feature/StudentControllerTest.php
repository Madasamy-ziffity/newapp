<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase; // Ensures the database is refreshed for each test

    /** @test */
    public function it_can_store_a_new_student()
    {
        // Arrange: Create the data to be sent to the store method
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ];

        // Act: Send a POST request to the store method
        $response = $this->post(route('students.store'), $data);

        // Assert: Check if the student was created in the database
        $this->assertDatabaseHas('students', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '1234567890',
        ]);

        // Assert: Check if the response is a redirect to the students.index route
        $response->assertRedirect(route('students.index'));

        // Assert: Check if the success message is in the session
        $response->assertSessionHas('success', 'Student created successfully.');
    }

    /** @test */
    public function it_validates_required_fields()
    {
        // Act: Send a POST request with missing required fields
        $response = $this->post(route('students.store'), []);

        // Assert: Check if the session has validation errors
        $response->assertSessionHasErrors(['name', 'email', 'phone']);
    }

    /** @test */
    public function it_checks_email_is_unique()
    {
        // Arrange: Create a student to test the unique email constraint
        Student::create([
            'name' => 'Existing Student',
            'email' => 'existing@example.com',
            'phone' => '1234567890',
        ]);

        // Act: Try to create another student with the same email
        $response = $this->post(route('students.store'), [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'phone' => '0987654321',
        ]);

        // Assert: Check if the session has an error for the email field
        $response->assertSessionHasErrors(['email']);
    }
}
