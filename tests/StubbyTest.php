<?php

namespace Tests;

use Illuminate\Support\Facades\File;

class StubbyTest extends TestCase
{

    /** @test */
    public function it_can_set_ide()
    {
        File::put(base_path('.env'), '');
        $this->artisan('new ide pstorm')
            ->assertExitCode(0);
        $env = File::get(base_path('.env'));
        $this->assertStringContainsString('STUBBY_FILE_OPEN_COMMAND=pstorm', $env);
        File::delete(base_path('.env'));
    }

}
