<?php

// namespace Feycot\PageAnalyzer\Tests\Feature;

// use Feycot\PageAnalyzer\Tests\TestCase;
// use Illuminate\Support\Carbon;
// use VCR\VCR;

// class UrlCheckTest extends TestCase
// {
//     protected function setUp(): void
//     {
//         parent::setUp();
//         VCR::turnOn();
//         \VCR\VCR::insertCassette('test_hexlet.yml');
//         VCR::configure()->setMode('none');
//     }

//     protected function tearDown(): void
//     {
//         VCR::turnOff();
//     }

//     public function testCheckStore(): void
//     {
//         $checkData = [
//             'url_id' => $this->urlId,
//             'status_code' => '200',
//             'title' => 'Awesome page',
//             'description' => 'Statements of great people',
//             'h1' => 'Do not expect a miracle, miracles yourself!',
//             'created_at' => Carbon::now()
//         ];

//         $params = ['url_id' => $this->urlId];
//         $url = $this->urlFor('urls.checks.store', $params);

//         $response = $this->post($url);
//         $response->assertStatus(302);

//         $urlCheck = $this->db->table('url_checks')->where($checkData)->first();
//         $this->assertNotNull($urlCheck);
//     }
// }
