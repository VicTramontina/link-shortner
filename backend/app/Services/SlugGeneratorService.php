<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Str;

/**
 * Slug Generator Service
 *
 * Generates unique slugs for shortened links.
 */
class SlugGeneratorService
{
    /**
     * Characters allowed in the slug.
     */
    private const ALLOWED_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Minimum slug length.
     */
    private const MIN_LENGTH = 6;

    /**
     * Maximum slug length.
     */
    private const MAX_LENGTH = 8;

    /**
     * Generate a unique slug.
     *
     * @param int|null $length The desired length (6-8 characters). If null, random length is used.
     * @return string The generated unique slug.
     */
    public function generate(?int $length = null): string
    {
        $length = $length ?? rand(self::MIN_LENGTH, self::MAX_LENGTH);
        $length = max(self::MIN_LENGTH, min(self::MAX_LENGTH, $length));

        do {
            $slug = $this->generateRandomString($length);
        } while ($this->slugExists($slug));

        return $slug;
    }

    /**
     * Validate if a custom slug is available.
     *
     * @param string $slug The slug to validate.
     * @return bool True if slug is valid and available.
     */
    public function isValid(string $slug): bool
    {
        if (strlen($slug) < self::MIN_LENGTH || strlen($slug) > self::MAX_LENGTH) {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $slug)) {
            return false;
        }

        return !$this->slugExists($slug);
    }

    /**
     * Check if a slug already exists in the database.
     *
     * @param string $slug The slug to check.
     * @param int|null $excludeId Exclude this link ID from the check (for updates).
     * @return bool True if slug exists.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Link::withTrashed()->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Generate a random alphanumeric string.
     *
     * @param int $length The length of the string.
     * @return string The generated string.
     */
    private function generateRandomString(int $length): string
    {
        $chars = self::ALLOWED_CHARS;
        $charLength = strlen($chars);
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $charLength - 1)];
        }

        return $result;
    }
}
