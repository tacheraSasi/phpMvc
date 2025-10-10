# Utils, Facades & Helpers Reference Guide

Complete reference for all utilities, facades, and helper functions available in the PHP MVC Framework.

## Table of Contents

- [Utility Classes](#utility-classes)
  - [StringUtil](#stringutil)
  - [ArrayUtil](#arrayutil)
  - [DateUtil](#dateutil)
  - [FileUtil](#fileutil)
  - [CryptoUtil](#cryptoutil)
- [Facades](#facades)
  - [DB](#db-facade)
  - [Cache](#cache-facade)
  - [Response](#response-facade)
  - [Request](#request-facade)
  - [Validator](#validator-facade)
- [Helper Functions](#helper-functions)
  - [API Helpers](#api-helpers)
  - [String Helpers](#string-helpers)
  - [Array Helpers](#array-helpers)
  - [HTTP Helpers](#http-helpers)

---

## Utility Classes

### StringUtil

**Namespace:** `Core\Utils\StringUtil`

String manipulation and formatting utilities.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `camelCase(string $string)` | Convert to camelCase | `StringUtil::camelCase('hello-world')` → `'helloWorld'` |
| `snakeCase(string $string)` | Convert to snake_case | `StringUtil::snakeCase('HelloWorld')` → `'hello_world'` |
| `kebabCase(string $string)` | Convert to kebab-case | `StringUtil::kebabCase('HelloWorld')` → `'hello-world'` |
| `random(int $length, string $chars)` | Generate random string | `StringUtil::random(16)` → `'aB3xY...'` |
| `truncate(string $string, int $length, string $suffix)` | Truncate string | `StringUtil::truncate('Long text', 5)` → `'Long ...'` |
| `startsWith(string $haystack, string $needle)` | Check if starts with | `StringUtil::startsWith('hello', 'he')` → `true` |
| `endsWith(string $haystack, string $needle)` | Check if ends with | `StringUtil::endsWith('hello', 'lo')` → `true` |
| `contains(string $haystack, string $needle)` | Check if contains | `StringUtil::contains('hello', 'll')` → `true` |
| `slug(string $string, string $separator)` | Create URL slug | `StringUtil::slug('Hello World')` → `'hello-world'` |
| `uuid()` | Generate UUID v4 | `StringUtil::uuid()` → `'550e8400-e29b-...'` |
| `mask(string $string, int $visible, string $mask)` | Mask string | `StringUtil::mask('1234567890', 4)` → `'1234******'` |
| `titleCase(string $string)` | Convert to Title Case | `StringUtil::titleCase('hello world')` → `'Hello World'` |
| `limitWords(string $string, int $words, string $suffix)` | Limit words | `StringUtil::limitWords('one two three', 2)` → `'one two...'` |

---

### ArrayUtil

**Namespace:** `Core\Utils\ArrayUtil`

Array manipulation utilities with dot notation support.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `get(array $array, string $key, $default)` | Get value using dot notation | `ArrayUtil::get($arr, 'user.name')` |
| `set(array &$array, string $key, $value)` | Set value using dot notation | `ArrayUtil::set($arr, 'user.name', 'John')` |
| `has(array $array, string $key)` | Check if key exists | `ArrayUtil::has($arr, 'user.email')` |
| `forget(array &$array, string $key)` | Remove key | `ArrayUtil::forget($arr, 'user.age')` |
| `flatten(array $array, int $depth)` | Flatten array | `ArrayUtil::flatten([[1, 2], [3]])` → `[1, 2, 3]` |
| `only(array $array, array $keys)` | Get only specified keys | `ArrayUtil::only($arr, ['name', 'email'])` |
| `except(array $array, array $keys)` | Get all except keys | `ArrayUtil::except($arr, ['password'])` |
| `pluck(array $array, string $key, ?string $indexKey)` | Extract values | `ArrayUtil::pluck($users, 'email')` |
| `groupBy(array $array, string $key)` | Group by key | `ArrayUtil::groupBy($users, 'role')` |
| `isAssoc(array $array)` | Check if associative | `ArrayUtil::isAssoc(['a' => 1])` → `true` |
| `chunk(array $array, int $size)` | Split into chunks | `ArrayUtil::chunk([1,2,3,4], 2)` |
| `shuffle(array $array)` | Shuffle preserving keys | `ArrayUtil::shuffle($arr)` |
| `first(array $array, $default)` | Get first element | `ArrayUtil::first($arr)` |
| `last(array $array, $default)` | Get last element | `ArrayUtil::last($arr)` |
| `wrap($value)` | Wrap in array | `ArrayUtil::wrap('test')` → `['test']` |

---

### DateUtil

**Namespace:** `Core\Utils\DateUtil`

Date and time manipulation utilities.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `now()` | Current timestamp | `DateUtil::now()` → `1672531200` |
| `currentDate(string $format, ?string $timezone)` | Current date | `DateUtil::currentDate()` → `'2024-01-01 12:00:00'` |
| `format(string $date, string $format)` | Format date | `DateUtil::format('2024-01-01', 'd/m/Y')` |
| `timeAgo(string $date)` | Human-readable diff | `DateUtil::timeAgo('2024-01-01')` → `'2 months ago'` |
| `addDays(string $date, int $days, string $format)` | Add days | `DateUtil::addDays('2024-01-01', 7)` |
| `subDays(string $date, int $days, string $format)` | Subtract days | `DateUtil::subDays('2024-01-01', 7)` |
| `isPast(string $date)` | Check if past | `DateUtil::isPast('2024-01-01')` |
| `isFuture(string $date)` | Check if future | `DateUtil::isFuture('2025-01-01')` |
| `startOfDay(?string $date, string $format)` | Start of day | `DateUtil::startOfDay()` → `'2024-01-01 00:00:00'` |
| `endOfDay(?string $date, string $format)` | End of day | `DateUtil::endOfDay()` → `'2024-01-01 23:59:59'` |
| `diffInDays(string $date1, string $date2)` | Days difference | `DateUtil::diffInDays($d1, $d2)` → `7` |
| `toIso8601(string $date)` | ISO 8601 format | `DateUtil::toIso8601($date)` |
| `fromIso8601(string $date, string $format)` | From ISO 8601 | `DateUtil::fromIso8601($iso)` |
| `isValid(string $date, string $format)` | Validate date | `DateUtil::isValid('2024-01-01')` |
| `toTimestamp(string $date)` | To timestamp | `DateUtil::toTimestamp('2024-01-01')` |
| `fromTimestamp(int $timestamp, string $format)` | From timestamp | `DateUtil::fromTimestamp(1672531200)` |

---

### FileUtil

**Namespace:** `Core\Utils\FileUtil`

File system operations utilities.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `get(string $path)` | Read file | `FileUtil::get('file.txt')` |
| `put(string $path, string $contents)` | Write file | `FileUtil::put('file.txt', 'data')` |
| `append(string $path, string $contents)` | Append to file | `FileUtil::append('log.txt', 'entry')` |
| `delete(string $path)` | Delete file | `FileUtil::delete('file.txt')` |
| `exists(string $path)` | Check existence | `FileUtil::exists('file.txt')` |
| `extension(string $path)` | Get extension | `FileUtil::extension('file.php')` → `'php'` |
| `name(string $path)` | Get name without ext | `FileUtil::name('file.txt')` → `'file'` |
| `basename(string $path)` | Get basename | `FileUtil::basename('/path/file.txt')` → `'file.txt'` |
| `dirname(string $path)` | Get directory | `FileUtil::dirname('/path/file.txt')` → `'/path'` |
| `size(string $path)` | Get file size | `FileUtil::size('file.txt')` → `1024` |
| `mimeType(string $path)` | Get MIME type | `FileUtil::mimeType('image.jpg')` → `'image/jpeg'` |
| `lastModified(string $path)` | Last modified time | `FileUtil::lastModified('file.txt')` |
| `copy(string $source, string $dest)` | Copy file | `FileUtil::copy('a.txt', 'b.txt')` |
| `move(string $source, string $dest)` | Move file | `FileUtil::move('a.txt', 'b.txt')` |
| `makeDirectory(string $path, int $perms, bool $recursive)` | Create directory | `FileUtil::makeDirectory('dir')` |
| `isDirectory(string $path)` | Check if directory | `FileUtil::isDirectory('path')` |
| `isFile(string $path)` | Check if file | `FileUtil::isFile('path')` |
| `files(string $dir, bool $recursive)` | List files | `FileUtil::files('dir', true)` |
| `deleteDirectory(string $dir, bool $preserve)` | Delete directory | `FileUtil::deleteDirectory('dir')` |
| `humanFileSize(int $bytes, int $precision)` | Human-readable size | `FileUtil::humanFileSize(1024)` → `'1 KB'` |

---

### CryptoUtil

**Namespace:** `Core\Utils\CryptoUtil`

Cryptographic operations and security utilities.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `hashPassword(string $password)` | Hash password (bcrypt) | `CryptoUtil::hashPassword('secret')` |
| `verifyPassword(string $password, string $hash)` | Verify password | `CryptoUtil::verifyPassword('secret', $hash)` |
| `randomToken(int $length)` | Random secure token | `CryptoUtil::randomToken(32)` |
| `hash(string $data, string $algorithm)` | Generate hash | `CryptoUtil::hash('data', 'sha256')` |
| `hmac(string $data, string $key, string $algo)` | Generate HMAC | `CryptoUtil::hmac('data', 'key')` |
| `encrypt(string $data, string $key)` | Encrypt (AES-256-CBC) | `CryptoUtil::encrypt('secret', 'key')` |
| `decrypt(string $data, string $key)` | Decrypt | `CryptoUtil::decrypt($encrypted, 'key')` |
| `generateJWT(array $payload, string $secret, int $expiry)` | Generate JWT | `CryptoUtil::generateJWT($data, 'secret')` |
| `verifyJWT(string $token, string $secret)` | Verify JWT | `CryptoUtil::verifyJWT($token, 'secret')` |
| `generateApiKey(string $prefix, int $length)` | Generate API key | `CryptoUtil::generateApiKey('sk', 32)` |
| `checksum(string $data, string $algo)` | Generate checksum | `CryptoUtil::checksum('data', 'md5')` |
| `secureCompare(string $known, string $user)` | Constant-time compare | `CryptoUtil::secureCompare($a, $b)` |

---

## Facades

### DB Facade

**Namespace:** `Core\Facades\DB`

Static access to database operations.

#### Methods

```php
// Raw query
DB::query("SELECT * FROM users WHERE id = ?", [1]);

// Query builder
DB::table('users')->where('email', $email)->first();

// Transactions
DB::beginTransaction();
DB::commit();
DB::rollback();
```

---

### Cache Facade

**Namespace:** `Core\Facades\Cache`

File-based caching system.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `put(string $key, $value, int $ttl)` | Store in cache | `Cache::put('key', $value, 3600)` |
| `get(string $key, $default)` | Get from cache | `Cache::get('key', 'default')` |
| `has(string $key)` | Check if exists | `Cache::has('key')` |
| `forget(string $key)` | Remove from cache | `Cache::forget('key')` |
| `flush()` | Clear all cache | `Cache::flush()` |
| `remember(string $key, callable $callback, int $ttl)` | Get or store | `Cache::remember('key', fn() => $data, 3600)` |
| `forever(string $key, $value)` | Store forever | `Cache::forever('key', $value)` |

---

### Response Facade

**Namespace:** `Core\Facades\Response`

HTTP response helpers.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `json(array $data, int $status)` | JSON response | `Response::json(['data' => $data])` |
| `success($data, string $message, int $status)` | Success response | `Response::success($data, 'Success')` |
| `error(string $message, int $status, array $errors)` | Error response | `Response::error('Error', 400)` |
| `created($data, string $message)` | 201 Created | `Response::created($data)` |
| `noContent()` | 204 No Content | `Response::noContent()` |
| `unauthorized(string $message)` | 401 Unauthorized | `Response::unauthorized()` |
| `forbidden(string $message)` | 403 Forbidden | `Response::forbidden()` |
| `notFound(string $message)` | 404 Not Found | `Response::notFound()` |
| `validationError(array $errors, string $message)` | 422 Validation | `Response::validationError($errors)` |
| `redirect(string $url, int $status)` | Redirect | `Response::redirect('/home')` |
| `download(string $file, ?string $name)` | File download | `Response::download($path)` |

---

### Request Facade

**Namespace:** `Core\Facades\Request`

HTTP request data access.

#### Methods

| Method | Description | Example |
|--------|-------------|---------|
| `get(?string $key, $default)` | Get input | `Request::get('name', 'default')` |
| `all()` | Get all input | `Request::all()` |
| `has(string $key)` | Check if exists | `Request::has('email')` |
| `only(array $keys)` | Get only keys | `Request::only(['name', 'email'])` |
| `except(array $keys)` | Get except keys | `Request::except(['password'])` |
| `query(?string $key, $default)` | Get query param | `Request::query('page', 1)` |
| `post(?string $key, $default)` | Get POST data | `Request::post('name')` |
| `json()` | Get JSON input | `Request::json()` |
| `method()` | HTTP method | `Request::method()` → `'GET'` |
| `isMethod(string $method)` | Check method | `Request::isMethod('POST')` |
| `isGet()` | Check if GET | `Request::isGet()` |
| `isPost()` | Check if POST | `Request::isPost()` |
| `isAjax()` | Check if AJAX | `Request::isAjax()` |
| `header(string $key, $default)` | Get header | `Request::header('Authorization')` |
| `headers()` | Get all headers | `Request::headers()` |
| `ip()` | Client IP | `Request::ip()` |
| `userAgent()` | User agent | `Request::userAgent()` |
| `uri()` | Request URI | `Request::uri()` |
| `url()` | Full URL | `Request::url()` |
| `isSecure()` | Check HTTPS | `Request::isSecure()` |
| `file(string $key)` | Get uploaded file | `Request::file('avatar')` |
| `hasFile(string $key)` | Check file upload | `Request::hasFile('avatar')` |

---

## Helper Functions

### API Helpers

Global functions for API responses.

| Function | Description | Example |
|----------|-------------|---------|
| `json_response(array $data, int $status)` | Send JSON | `json_response(['data' => $data])` |
| `success_response($data, string $message, int $status)` | Success | `success_response($data, 'OK')` |
| `error_response(string $message, int $status, array $errors)` | Error | `error_response('Error', 400)` |
| `api_response(bool $success, string $message, $data, int $status)` | Generic | `api_response(true, 'OK', $data)` |
| `paginate_response(array $data, int $total, int $page, int $perPage)` | Paginated | `paginate_response($data, 100, 1, 10)` |
| `unauthorized(string $message)` | 401 | `unauthorized('Login required')` |
| `forbidden(string $message)` | 403 | `forbidden('Access denied')` |
| `not_found(string $message)` | 404 | `not_found('Resource not found')` |
| `validation_error(array $errors, string $message)` | 422 | `validation_error($errors)` |
| `cors_headers(array $origins, array $methods, array $headers)` | CORS | `cors_headers(['*'])` |

### String Helpers

| Function | Description | Example |
|----------|-------------|---------|
| `str_contains(string $haystack, string $needle)` | Check contains | `str_contains('hello', 'll')` |
| `str_starts_with(string $haystack, string $needle)` | Check starts | `str_starts_with('hello', 'he')` |
| `str_ends_with(string $haystack, string $needle)` | Check ends | `str_ends_with('hello', 'lo')` |
| `str_limit(string $string, int $length, string $suffix)` | Limit length | `str_limit('Long text', 10)` |
| `str_slug(string $string, string $separator)` | Create slug | `str_slug('Hello World')` |
| `str_random(int $length)` | Random string | `str_random(16)` |
| `str_camel(string $string)` | camelCase | `str_camel('hello-world')` |
| `str_snake(string $string)` | snake_case | `str_snake('HelloWorld')` |
| `str_kebab(string $string)` | kebab-case | `str_kebab('HelloWorld')` |
| `str_title(string $string)` | Title Case | `str_title('hello world')` |
| `str_words(string $string, int $words, string $suffix)` | Limit words | `str_words('one two three', 2)` |
| `str_mask(string $string, int $visible, string $mask)` | Mask string | `str_mask('1234567890', 4)` |
| `str_uuid()` | UUID v4 | `str_uuid()` |
| `str_escape(string $string)` | Escape HTML | `str_escape('<script>')` |
| `str_after(string $string, string $needle)` | After substring | `str_after('hello:world', ':')` |
| `str_before(string $string, string $needle)` | Before substring | `str_before('hello:world', ':')` |

### Array Helpers

| Function | Description | Example |
|----------|-------------|---------|
| `array_get(array $array, string $key, $default)` | Get with dot | `array_get($arr, 'user.name')` |
| `array_set(array &$array, string $key, $value)` | Set with dot | `array_set($arr, 'user.name', 'John')` |
| `array_has(array $array, string $key)` | Has with dot | `array_has($arr, 'user.email')` |
| `array_only(array $array, array $keys)` | Filter keys | `array_only($arr, ['name'])` |
| `array_except(array $array, array $keys)` | Exclude keys | `array_except($arr, ['password'])` |
| `array_pluck(array $array, string $key, ?string $indexKey)` | Extract | `array_pluck($users, 'email')` |
| `array_flatten(array $array, int $depth)` | Flatten | `array_flatten([[1, 2], [3]])` |
| `array_first(array $array, $default)` | First element | `array_first($arr)` |
| `array_last(array $array, $default)` | Last element | `array_last($arr)` |
| `array_wrap($value)` | Wrap in array | `array_wrap('test')` → `['test']` |
| `array_is_assoc(array $array)` | Check assoc | `array_is_assoc($arr)` |

### HTTP Helpers

| Function | Description | Example |
|----------|-------------|---------|
| `request_method()` | HTTP method | `request_method()` → `'GET'` |
| `is_get()` | Check GET | `is_get()` → `true` |
| `is_post()` | Check POST | `is_post()` → `false` |
| `is_ajax()` | Check AJAX | `is_ajax()` → `false` |
| `is_json_request()` | Check JSON | `is_json_request()` |
| `get_json_input()` | Get JSON body | `get_json_input()` |
| `get_header(string $key, $default)` | Get header | `get_header('Authorization')` |
| `get_bearer_token()` | Extract token | `get_bearer_token()` |
| `client_ip()` | Client IP | `client_ip()` → `'127.0.0.1'` |
| `request_uri()` | Request URI | `request_uri()` → `'/api/users'` |
| `current_url()` | Full URL | `current_url()` |
| `is_https()` | Check HTTPS | `is_https()` → `false` |
| `user_agent()` | User agent | `user_agent()` |
| `set_http_header(string $name, string $value)` | Set header | `set_http_header('X-Custom', 'value')` |
| `set_status_code(int $code)` | Set status | `set_status_code(404)` |
| `download_file(string $file, ?string $name)` | Download | `download_file('file.pdf')` |
| `redirect_to_url(string $url, int $status)` | Redirect | `redirect_to_url('/home')` |
| `base_url(string $path)` | Base URL | `base_url('/api')` |

---

## Usage Examples

See the `app/examples/UtilsDemoApi.php` controller for practical examples of using these utilities in API endpoints.

To test utilities:
```bash
php app/examples/utils_usage.php
```
