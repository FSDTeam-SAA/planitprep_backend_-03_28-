# PlanitPrep Postman Docs

This folder contains an importable Postman collection and environment for the Laravel API in this repository.

Files:
- `PlanitPrep API.postman_collection.json`
- `PlanitPrep Local.postman_environment.json`

## Import order

1. Import the environment file.
2. Import the collection file.
3. Select the `PlanitPrep Local` environment in Postman.
4. Set `authToken` after login.

## Base auth model

Almost every API route in [routes/api.php](c:/Users/Tauhid%20Hasan/Desktop/planitprep_backend/routes/api.php) is wrapped by `CheckValidRequestBasedOnUserAndKey`, so requests need these headers:

- `user: {{apiUser}}`
- `key: {{apiKey}}`

The default values come from [config/app.php](c:/Users/Tauhid%20Hasan/Desktop/planitprep_backend/config/app.php):

- `API_USER=DietitianApi`
- `API_KEY=9wych3yrke61pf0f92ztiodm30inv9o70lg1a11vwpctu5b5kz`

Protected endpoints also require:

- `Authorization: Bearer {{authToken}}`

Get the bearer token from:

- `POST /api/v1/verify-otp`
- `POST /api/v1/social-login`
- `POST /api/v1/refresh`

## Recommended flow

1. Call `Auth / Authenticate`.
2. Call `Auth / Verify OTP`.
3. Copy the returned token into `authToken`.
4. Use the protected folders.

## Known API quirks from the current codebase

- `verify-otp` currently accepts the hardcoded OTP `123456` in [AuthController.php](c:/Users/Tauhid%20Hasan/Desktop/planitprep_backend/app/Http/Controllers/Api/AuthController.php).
- `get-user-meal` validates `user_id` but reads `userid` inside [ChatController.php](c:/Users/Tauhid%20Hasan/Desktop/planitprep_backend/app/Http/Controllers/Api/ChatController.php). The Postman request includes both fields.
- `cancel-subscription`, `verify-payment`, and `update-isFavourite` are routed in [api.php](c:/Users/Tauhid%20Hasan/Desktop/planitprep_backend/routes/api.php) but the controller methods are missing, so those requests may fail at runtime.
- `get-daily-average` currently uses a hardcoded user id in [UserController.php](c:/Users/Tauhid%20Hasan/Desktop/planitprep_backend/app/Http/Controllers/Api/UserController.php), so treat its output cautiously.
- `stripe/webhook` is intended for Stripe callbacks and usually should not be triggered manually except for testing signed webhook payloads.

## Scope

This documentation covers the API routes defined in [routes/api.php](c:/Users/Tauhid%20Hasan/Desktop/planitprep_backend/routes/api.php). It does not document the Blade/web routes from `routes/web.php`.
