# ✂️ Cut It Off - URL Shortener

A lightweight, single-file URL shortener with API support, admin panel, and metadata management.

## Features

- **Single PHP file** - No framework dependencies
- **JSON storage** - No database required
- **API with rate limiting** - Token-based authentication
- **Custom short codes** - Optional user-defined slugs
- **Metadata support** - Add title/description to remember links
- **Overwrite protection** - Configurable protection for existing codes (to keep the shortcode but update long URLs as needed)
- **Admin panel** - Manage all URLs from a web interface
- **Click tracking** - Basic analytics for each link
- **QR code generation** - A QR code is generated to point to the generate short URL

## Setup

1. Upload `index.php` and `rename.htaccess` to your web server
2. Make sure PHP has write permissions to the directory and rename `rename.htaccess` to `.htaccess` (check config in the htaccess file, as it might require you to edit the index.php accordingly)
3. Access `index.php` in your browser
4. On first run, a `config.json` file is created with your API token
5. Adjust manifest.json `start_url` and `scope` to your needs

### Configuration

Edit `config.json` to customize:

```json
{
    "api_token": "your-64-char-hex-token",
    "rate_limit_requests": 60,
    "rate_limit_window": 3600,
    "admin_path": "admin",
    "admin_show_link": true,
	"admin_password" : "your-secure-password",
	"recaptcha_site_key" : "your_site_key",
	"recaptcha_secret_key" : "your_secret_key"
}
```


- `api_token`: Auto-generated on first run. Use this in API requests. Can generate a new one from admin panel (careful, as old one will be lost)
- `rate_limit_requests`: Maximum API requests per window (default: 60). Can edit it from admin panel.
- `rate_limit_window`: Time window in seconds (default: 3600 = 1 hour). Can edit it from admin panel.
- `admin_path`: Path to access the admin panel (`admin` by default, therefore accessible at /admin). NOT editable from admin panel.
- `admin_show_link`: Option to show the link to admin panel from homepage. NOT editable from admin panel.
- `admin_password`: Set this to enable the admin panel. NOT editable from admin panel.
- `recaptcha_site_key`: Empty by default. Fill in (pair with `recaptcha_secret_key`) to enable ReCaptcha on login page. NOT editable from admin panel.
- `recaptcha_secret_key`: Empty by default. Fill in (pair with `recaptcha_site_key`) to enable ReCaptcha on login page. NOT editable from admin panel.

## Web Interface

Access the main page to shorten URLs via the web form. Advanced options include:
- Custom short code
- Title and description
- **Allow overwrite**: If checked, this short code can be updated later by anyone who uses the same code

## Admin Panel

Access `/admin` (or whatever is set in your config.json to:
- View all shortened URLs
- Edit title/description
- Delete URLs
- Copy/Regenerate your API token
- Edit the `rate_limit_requests` and `rate_limit_window` values
- See the API doc

**Note:** Set `admin_password` in `config.json` to enable the admin panel.

---

## API Documentation

All API endpoints require authentication via the `Authorization` header.

### Authentication

Include your API token in requests:

```
Authorization: Bearer YOUR_API_TOKEN
```

Or use the `X-API-Token` header:

```
X-API-Token: YOUR_API_TOKEN
```

### Rate Limiting

Responses include rate limit headers:
- `X-RateLimit-Remaining`: Requests remaining in current window
- `X-RateLimit-Reset`: Unix timestamp when the window resets

Exceeding the limit returns `429 Too Many Requests`.

---

### Create Short URL

**POST** `/api/cutitoff`

Create a new shortened URL.

#### Request Body

```json
{
    "url": "https://example.com/very-long-url",
    "code": "my-custom-code",
    "title": "My Link Title",
    "description": "A description for this link",
    "allow_overwrite": false
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `url` | string | ✓ | The destination URL (must be valid) |
| `code` | string | | Custom short code (alphanumeric, dash, underscore, 1-50 chars) |
| `title` | string | | Optional title for the link |
| `description` | string | | Optional description |
| `allow_overwrite` | boolean | | If `true`, this code can be updated later (default: `false`) |

**Note:** If a custom `code` already exists:
- If the existing URL has `allow_overwrite: true`, it will be updated with the new URL
- If the existing URL has `allow_overwrite: false`, the request returns a `409 Conflict` error

#### Response

```json
{
    "success": true,
    "code": "my-custom-code",
    "short_url": "https://your-domain.com/my-custom-code",
    "original_url": "https://example.com/very-long-url",
    "title": "My Link Title",
    "description": "A description for this link"
}
```

#### Example (cURL)

```bash
curl -X POST https://your-domain.com/api/cutitoff \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://example.com/long-url",
    "code": "my-link",
    "title": "Example Link"
  }'
```

---

### List All URLs

**GET** `/api/urls`

Retrieve all shortened URLs.

#### Response

```json
{
    "urls": {
        "abc123": {
            "url": "https://example.com",
            "short_url": "https://your-domain.com/abc123",
            "title": "Example",
            "description": "",
            "created_at": "2024-01-15T10:30:00+00:00",
            "clicks": 42
        }
    }
}
```

#### Example (cURL)

```bash
curl https://your-domain.com/api/urls \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

### Get Single URL

**GET** `/api/urls/{code}`

Retrieve information about a specific shortened URL.

#### Response

```json
{
    "code": "abc123",
    "url": "https://example.com",
    "short_url": "https://your-domain.com/abc123",
    "title": "Example",
    "description": "",
    "created_at": "2024-01-15T10:30:00+00:00",
    "updated_at": "2024-01-15T10:30:00+00:00",
    "clicks": 42
}
```

#### Example (cURL)

```bash
curl https://your-domain.com/api/urls/abc123 \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

### Update URL

**PUT** `/api/urls/{code}`

Update an existing shortened URL's metadata or destination.

#### Request Body

```json
{
    "url": "https://new-destination.com",
    "title": "Updated Title",
    "description": "Updated description"
}
```

All fields are optional. Only provided fields will be updated.

#### Response

```json
{
    "success": true,
    "data": {
        "code": "abc123",
        "url": "https://new-destination.com",
        "short_url": "https://your-domain.com/abc123",
        "title": "Updated Title",
        "description": "Updated description"
    }
}
```

#### Example (cURL)

```bash
curl -X PUT https://your-domain.com/api/urls/abc123 \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title": "New Title"}'
```

---

### Delete URL

**DELETE** `/api/urls/{code}`

Delete a shortened URL.

#### Response

```json
{
    "success": true,
    "message": "URL deleted"
}
```

#### Example (cURL)

```bash
curl -X DELETE https://your-domain.com/api/urls/abc123 \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

---

## Error Responses

All errors return a JSON object with an `error` field:

```json
{
    "error": "Error message here"
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created (new URL) |
| 400 | Bad request (invalid input) |
| 401 | Unauthorized (invalid/missing token) |
| 404 | Not found |
| 409 | Conflict (code exists, overwrite disabled) |
| 429 | Rate limit exceeded |

---

## Security Notes

1. **Keep `config.json` secure** - It contains your API token. The file is created with restricted permissions (0600).

2. **Use HTTPS** - Always use HTTPS in production to protect your API token.

3. **Set a strong admin password** - Use a unique, random password for the admin panel.

4. **Rate limiting** - The built-in rate limiter protects against brute force. Adjust limits in config as needed.

5. **Input validation** - All URLs are validated, and custom codes are sanitized.

---

## Example: Backend Integration (PHP)

```php
<?php
function createShortUrl($longUrl, $title = '', $customCode = null) {
    $apiToken = 'YOUR_API_TOKEN';
    $apiUrl = 'https://your-domain.com/api/cutitoff';
    
    $data = ['url' => $longUrl, 'title' => $title];
    if ($customCode) {
        $data['code'] = $customCode;
    }
    
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiToken,
            'Content-Type: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => 'cURL error: ' . $error];
    }
    
    $result = json_decode($response, true);
    
    if ($result === null) {
        return ['error' => 'Invalid JSON response', 'raw' => $response, 'http_code' => $httpCode];
    }
    
    return $result;
}

// Usage
$result = createShortUrl('https://example.com/long-url', 'My Link');

if (isset($result['error'])) {
    echo 'Error: ' . $result['error'];
} else {
    echo $result['short_url'];
}
```

## Example: Backend Integration (JavaScript/Node.js)

```javascript
async function createShortUrl(longUrl, title = '', customCode = null) {
    const apiToken = 'YOUR_API_TOKEN';
    const apiUrl = 'https://your-domain.com/api/cutitoff';
    
    const data = { url: longUrl, title };
    if (customCode) data.code = customCode;
    
    const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${apiToken}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });
    
    return response.json();
}

// Usage
const result = await createShortUrl('https://example.com/long-url', 'My Link');
console.log(result.short_url);
```
