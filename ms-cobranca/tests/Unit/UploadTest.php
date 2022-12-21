<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;


class UploadTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function can_upload_and_salve_in_storage()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('listDebt.csv');

        $parameters =[
            'listDebt'=>$file,
        ];

        $response = $this->json('post', 'api/process', $parameters, $this->headers());

        $response->assertStatus(200);
    }
}
