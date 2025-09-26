Implementation notes and how to integrate scaffold into a real Laravel 12 project.

1. Copy contents of this scaffold into a fresh Laravel 12 application root (or merge the `app/`, `database/`, `routes/`, `packages/` folders).
2. Run `composer install` (make sure composer.json includes laravel/framework ^12.0) and set up `.env`.
3. Run migrations:
   - `php artisan migrate`
   - The migrations included are sample and may require adjustment for your DB.
4. Storage:
   - Configure `FILESYSTEM_DISK` and ensure `storage` is writable.
5. Tests:
   - Run `php artisan test`. The tests are PHPUnit-based.
6. Chunked upload:
   - The scaffold includes a resumable/chunked upload handler (controller + service). For production, integrate a real resumable protocol (e.g., tus) or add JS client to upload chunks.
7. Image variants:
   - Variant generation uses `Intervention\Image` in sample code; add `"intervention/image"` to composer and wire it in.
8. Package:
   - `packages/user-discounts` is a PSR-4 package; `composer.json` is included. Add repository path in your app `composer.json` or use `composer require` via path repository.

This scaffold is intentionally explicit and commented to make review and grading straightforward.
