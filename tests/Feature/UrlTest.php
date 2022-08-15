<?php

namespace Feycot\PageAnalyzer\Tests\Feature;

use Feycot\PageAnalyzer\Tests\TestCase;

class UrlTest extends TestCase
{
    public function testIndex(): void
    {
        $url = $this->urlFor('urls.index');
        $this->get($url)
            ->assertOk()
            ->assertSee('Анализатор страниц');
    }

    public function testShow(): void
    {
        $params = ['id' => $this->urlId];
        $url = $this->urlFor('urls.show', $params);

        $this->get($url)
            ->assertOk()
            ->assertSee('Анализатор страниц');
    }

    public function testStore(): void
    {
        $name = 'http://url.test';
        $data = [
            'url' => ['name' => $name]
        ];

        $this->post($this->urlFor('urls.store'), $data)
            ->assertStatus(302);

        $url = $this->db->table('urls')->where('name', $name)->first();

        $this->assertNotNull($url);
    }
}
