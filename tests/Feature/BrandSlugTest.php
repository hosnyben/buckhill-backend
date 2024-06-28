<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Brand;

use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandSlugTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test for slug generating in case of missing slug.
     */
    public function test_brand_slug(): void
    {
        $brand = new Brand();
        $brand->title = 'Hello World';
        $brand->save();

        $this->assertNotEmpty($brand->slug);
        $this->assertStringContainsString('hello-world', $brand->slug, 'Slug does not contain the title');
    }

    /**
     * A basic unit test for slug generating in case of title has special characters.
     */
    public function test_brand_slug_with_special_characters(): void
    {
        $brand = new Brand();
        $brand->title = 'Hello World!@#$%^&*()_+';
        $brand->save();

        $this->assertNotEmpty($brand->slug);
        $this->assertStringContainsString('hello-world', $brand->slug, 'Slug does not contain the title');
    }

    /**
     * A basic unit test for slug generating in case of title has special latin letters.
     */
    public function test_brand_slug_with_special_latin_letters(): void
    {
        $brand = new Brand();
        $brand->title = 'Hello World ñéèëê';
        $brand->save();

        $this->assertNotEmpty($brand->slug);
        $this->assertStringContainsString('hello-world', $brand->slug, 'Slug does not contain the title');
    }

    /**
     * A basic unit test for slug generating in case of title has special letters.
     */
    public function test_brand_slug_with_special_letters(): void
    {
        $brand = new Brand();
        $brand->title = 'Hello World 你好世界';
        $brand->save();

        $this->assertNotEmpty($brand->slug);
        $this->assertStringContainsString('hello-world', $brand->slug, 'Slug does not contain the title');
    }
}
