<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_upload_file(): void
    {
        Storage::fake();
        $file = UploadedFile::fake()->create('data.json');

        $response = $this->postJson('/api/file/store', [
            'file' => $file,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'data.json')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ]
            ]);

        $this->assertDatabaseHas('file_uploads', [
            'name' => 'data.json',
            'path' => 'files/' . $file->hashName(),
        ]);

        Storage::disk('local')->assertExists('/files/' . $file->hashName());
    }

    public function test_error_validation_without_file(): void
    {
        $response = $this->postJson('/api/file/store', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('file')
            ->assertJsonPath('errors.file.0', 'The file field is required.');
    }

    public function test_error_validation_wrong_format_file(): void
    {
        $response = $this->postJson('/api/file/store', [
            'file' => UploadedFile::fake()->create('data.txt'),
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('file')
            ->assertJsonPath('errors.file.0', 'The file field must be a file of type: json.');
    }

    public function test_error_validation_wrong_type_field_file(): void
    {
        $response = $this->postJson('/api/file/store', [
            'file' => 'Some wrong string',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('file')
            ->assertJsonPath('errors.file.0', 'The file field must be a file.')
            ->assertJsonPath('errors.file.1', 'The file field must be a file of type: json.');
    }

    public function test_error_validation_wrong_size_file(): void
    {
        $response = $this->postJson('/api/file/store', [
            'file' => UploadedFile::fake()
                                    ->create('data.json')
                                    ->size(3000),
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('file')
            ->assertJsonPath('errors.file.0', 'The file field must not be greater than 2048 kilobytes.');
    }
}
