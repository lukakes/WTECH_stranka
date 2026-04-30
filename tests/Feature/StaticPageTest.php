<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaticPageTest extends TestCase
{
    public function test_about_page_can_be_rendered(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('About us')
            ->assertSee('Welcome to our little corner');
    }

    public function test_contact_page_can_be_rendered(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Contact us')
            ->assertSee('Message');
    }
}
