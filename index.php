<?php
/**
 * Simple URL Shortener : Cut It Off
 * Features:
 * - API with rate limiting & token authentication
 * - Custom short codes (optional)
 * - Title/description metadata
 * - Overwrite protection (configurable)
 * - Admin panel for management
 */

define('DATA_FILE', __DIR__ . '/urls.json');
define('CONFIG_FILE', __DIR__ . '/config.json');
define('BASE_URL', ''); // Leave empty to auto-detect

// Default configuration
function getDefaultConfig(): array {
    return [
        'api_token' => bin2hex(random_bytes(32)), // Generated on first run
        'rate_limit_requests' => 60,              // Max requests per window
        'rate_limit_window' => 3600,              // Window in seconds (1 hour)
        'admin_password' => '',                   // Set this to enable admin panel
    ];
}

// Load/save configuration
function loadConfig(): array {
    if (!file_exists(CONFIG_FILE)) {
        $config = getDefaultConfig();
        saveConfig($config);
        return $config;
    }
    return json_decode(file_get_contents(CONFIG_FILE), true) ?: getDefaultConfig();
}

function saveConfig(array $config): void {
    file_put_contents(CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT));
    // chmod(CONFIG_FILE, 0600); // Restrict permissions - depending on server config, might make the config file not accessible
}

// Load/save URLs
function loadUrls(): array {
    if (!file_exists(DATA_FILE)) return [];
    $data = file_get_contents(DATA_FILE);
    return json_decode($data, true) ?: [];
}

function saveUrls(array $urls): void {
    file_put_contents(DATA_FILE, json_encode($urls, JSON_PRETTY_PRINT));
}

// Rate limiting using file-based storage
function checkRateLimit(string $identifier): array {
    $config = loadConfig();
    $rateFile = __DIR__ . '/rate_limits.json';
    $limits = file_exists($rateFile) ? json_decode(file_get_contents($rateFile), true) ?: [] : [];
    
    $now = time();
    $windowStart = $now - $config['rate_limit_window'];
    
    // Clean old entries
    foreach ($limits as $id => $data) {
        if ($data['window_start'] < $windowStart) {
            unset($limits[$id]);
        }
    }
    
    if (!isset($limits[$identifier])) {
        $limits[$identifier] = ['count' => 0, 'window_start' => $now];
    }
    
    // Reset if window expired
    if ($limits[$identifier]['window_start'] < $windowStart) {
        $limits[$identifier] = ['count' => 0, 'window_start' => $now];
    }
    
    $limits[$identifier]['count']++;
    file_put_contents($rateFile, json_encode($limits));
    
    $remaining = max(0, $config['rate_limit_requests'] - $limits[$identifier]['count']);
    $exceeded = $limits[$identifier]['count'] > $config['rate_limit_requests'];
    
    return [
        'exceeded' => $exceeded,
        'remaining' => $remaining,
        'reset' => $limits[$identifier]['window_start'] + $config['rate_limit_window']
    ];
}

// Generate short code
function generateCode(int $length = 6): string {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

// Validate custom code
function isValidCode(string $code): bool {
    return preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $code);
}

// Get base URL
function getBaseUrl(): string {
    if (BASE_URL) return rtrim(BASE_URL, '/') . '/';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . rtrim($script, '/') . '/';
}

// API Response helper
function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Get request headers (compatible with all server configs)
function getRequestHeaders(): array {
    $headers = [];
    
    // Try getallheaders() first (Apache)
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $key => $value) {
            $headers[strtolower($key)] = $value;
        }
    }
    
    // Fallback: parse from $_SERVER (works with nginx/PHP-FPM)
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $headerKey = strtolower(str_replace('_', '-', substr($key, 5)));
            $headers[$headerKey] = $value;
        }
    }
    
    return $headers;
}

// Verify API token
function verifyApiToken(): bool {
    $config = loadConfig();
    $headers = getRequestHeaders();
    $token = $headers['authorization'] ?? '';
    
    // Support "Bearer TOKEN" format
    if (stripos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }
    
    // Also check X-API-Token header
    if (!$token) {
        $token = $headers['x-api-token'] ?? '';
    }
    
    if (!$token) {
        return false;
    }
    
    return hash_equals($config['api_token'], $token);
}

// Get client identifier for rate limiting
function getClientIdentifier(): string {
    $headers = getRequestHeaders();
    $token = $headers['authorization'] ?? $headers['x-api-token'] ?? '';
    if ($token) return 'token:' . substr(hash('sha256', $token), 0, 16);
    return 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

// ============================================
// ROUTING
// ============================================

$urls = loadUrls();
$config = loadConfig();

// Parse request
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = dirname($scriptName);
$method = $_SERVER['REQUEST_METHOD'];

// Extract path
if ($scriptDir !== '/' && strpos($requestUri, $scriptDir) === 0) {
    $path = substr($requestUri, strlen($scriptDir));
} else {
    $path = $requestUri;
}

$path = ltrim($path, '/');
$path = strtok($path, '?');
$path = strtok($path, '#');
$baseName = basename($scriptName);
if (strpos($path, $baseName) === 0) {
    $path = substr($path, strlen($baseName));
}
$path = trim($path, '/');

// ============================================
// API ENDPOINTS
// ============================================

if (strpos($path, 'api/') === 0) {
    $apiPath = substr($path, 4);
    
    // Rate limiting for API
    $rateLimit = checkRateLimit(getClientIdentifier());
    header('X-RateLimit-Remaining: ' . $rateLimit['remaining']);
    header('X-RateLimit-Reset: ' . $rateLimit['reset']);
    
    if ($rateLimit['exceeded']) {
        jsonResponse([
            'error' => 'Rate limit exceeded',
            'retry_after' => $rateLimit['reset'] - time()
        ], 429);
    }
    
    // Verify API token for all API requests
    if (!verifyApiToken()) {
        jsonResponse(['error' => 'Invalid or missing API token'], 401);
    }
    
    // POST /api/cutitoff - Create short URL
    if ($apiPath === 'cutitoff' && $method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['url'])) {
            jsonResponse(['error' => 'Missing required field: url'], 400);
        }
        
        $url = filter_var(trim($input['url']), FILTER_VALIDATE_URL);
        if (!$url) {
            jsonResponse(['error' => 'Invalid URL format'], 400);
        }
        
        $customCode = isset($input['code']) ? trim($input['code']) : null;
        $title = isset($input['title']) ? trim($input['title']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        $allowOverwrite = isset($input['allow_overwrite']) ? (bool)$input['allow_overwrite'] : false;
        
        // Handle custom code
        if ($customCode) {
            if (!isValidCode($customCode)) {
                jsonResponse(['error' => 'Invalid code format. Use alphanumeric, dash, underscore only (1-50 chars)'], 400);
            }
            
            // Check if code exists and if it can be overwritten
            if (isset($urls[$customCode])) {
                $existingData = is_array($urls[$customCode]) ? $urls[$customCode] : ['url' => $urls[$customCode]];
                $canOverwrite = $existingData['allow_overwrite'] ?? false;
                
                if (!$canOverwrite) {
                    jsonResponse(['error' => 'Code already exists and is protected from overwriting.'], 409);
                }
            }
            
            $code = $customCode;
        } else {
            // Check if URL already shortened
            foreach ($urls as $existingCode => $data) {
                $existingUrl = is_array($data) ? $data['url'] : $data;
                if ($existingUrl === $url) {
                    $code = $existingCode;
                    break;
                }
            }
            
            if (!isset($code)) {
                do {
                    $code = generateCode();
                } while (isset($urls[$code]));
            }
        }
        
        // Store with metadata
        $urls[$code] = [
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'allow_overwrite' => $allowOverwrite,
            'created_at' => isset($urls[$code]) && is_array($urls[$code]) 
                ? $urls[$code]['created_at'] 
                : date('c'),
            'updated_at' => date('c'),
            'clicks' => isset($urls[$code]) && is_array($urls[$code]) 
                ? ($urls[$code]['clicks'] ?? 0) 
                : 0
        ];
        
        saveUrls($urls);
        
        jsonResponse([
            'success' => true,
            'code' => $code,
            'short_url' => getBaseUrl() . $code,
            'original_url' => $url,
            'title' => $title,
            'description' => $description,
            'allow_overwrite' => $allowOverwrite
        ], 201);
    }
    
    // GET /api/urls - List all URLs
    if ($apiPath === 'urls' && $method === 'GET') {
        $result = [];
        foreach ($urls as $code => $data) {
            if (is_array($data)) {
                $result[$code] = array_merge($data, ['short_url' => getBaseUrl() . $code]);
            } else {
                $result[$code] = [
                    'url' => $data,
                    'short_url' => getBaseUrl() . $code,
                    'title' => '',
                    'description' => ''
                ];
            }
        }
        jsonResponse(['urls' => $result]);
    }
    
    // GET /api/urls/{code} - Get single URL info
    if (preg_match('/^urls\/([^\/]+)$/', $apiPath, $matches) && $method === 'GET') {
        $code = $matches[1];
        if (!isset($urls[$code])) {
            jsonResponse(['error' => 'URL not found'], 404);
        }
        
        $data = is_array($urls[$code]) ? $urls[$code] : ['url' => $urls[$code]];
        $data['code'] = $code;
        $data['short_url'] = getBaseUrl() . $code;
        
        jsonResponse($data);
    }
    
    // DELETE /api/urls/{code} - Delete URL
    if (preg_match('/^urls\/([^\/]+)$/', $apiPath, $matches) && $method === 'DELETE') {
        $code = $matches[1];
        if (!isset($urls[$code])) {
            jsonResponse(['error' => 'URL not found'], 404);
        }
        
        unset($urls[$code]);
        saveUrls($urls);
        
        jsonResponse(['success' => true, 'message' => 'URL deleted']);
    }
    
    // PUT /api/urls/{code} - Update URL metadata
    if (preg_match('/^urls\/([^\/]+)$/', $apiPath, $matches) && $method === 'PUT') {
        $code = $matches[1];
        if (!isset($urls[$code])) {
            jsonResponse(['error' => 'URL not found'], 404);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $data = is_array($urls[$code]) ? $urls[$code] : ['url' => $urls[$code]];
        
        if (isset($input['url'])) {
            $url = filter_var(trim($input['url']), FILTER_VALIDATE_URL);
            if (!$url) jsonResponse(['error' => 'Invalid URL format'], 400);
            $data['url'] = $url;
        }
        if (isset($input['title'])) $data['title'] = trim($input['title']);
        if (isset($input['description'])) $data['description'] = trim($input['description']);
        $data['updated_at'] = date('c');
        
        $urls[$code] = $data;
        saveUrls($urls);
        
        jsonResponse(['success' => true, 'data' => array_merge($data, ['code' => $code, 'short_url' => getBaseUrl() . $code])]);
    }
    
    jsonResponse(['error' => 'Endpoint not found'], 404);
}

// ============================================
// ADMIN PANEL
// ============================================

if ($path === 'admin') {
    session_start();
    
    // Check if admin password is set
    if (empty($config['admin_password'])) {
        echo '<!DOCTYPE html><html><head><title>Admin Setup</title>
		  <link rel="manifest" href="manifest.json">
		<link rel="apple-touch-icon" href="apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="512x512" href="favicon.png"/>
		<link rel="shortcut icon" href="favicon.ico"/>
        <style>body{font-family:system-ui;padding:40px;max-width:600px;margin:0 auto;background:#1a1a2e;color:#eee;}
        .box{background:#16213e;padding:30px;border-radius:12px;}
        code{background:#0f3460;padding:2px 8px;border-radius:4px;}</style></head>
        <body><div class="box"><h2>⚠️ Admin Not Configured</h2>
        <p>To enable the admin panel, edit <code>config.json</code> and set an <code>admin_password</code>.</p>
        <p>Your API token is also in that file.</p></div></body></html>';
        exit;
    }

    // Handle login
    if ($method === 'POST' && isset($_POST['admin_login'])) {
        if (hash_equals($config['admin_password'], $_POST['password'])) {
            $_SESSION['admin_logged_in'] = true;
        }
    }
    
    // Handle logout
    if (isset($_GET['logout'])) {
        unset($_SESSION['admin_logged_in']);
        header('Location: ?');
        exit;
    }
    
    // Check authentication
    $isLoggedIn = $_SESSION['admin_logged_in'] ?? false;
    
    // Handle admin actions
    $adminMessage = '';
    if ($isLoggedIn && $method === 'POST') {
        // Delete URL
        if (isset($_POST['delete_code'])) {
            $code = $_POST['delete_code'];
            if (isset($urls[$code])) {
                unset($urls[$code]);
                saveUrls($urls);
                $adminMessage = 'deleted';
            }
        }
        
        // Update URL
        if (isset($_POST['update_code'])) {
            $code = $_POST['update_code'];
            if (isset($urls[$code])) {
                $data = is_array($urls[$code]) ? $urls[$code] : ['url' => $urls[$code]];
                $data['title'] = trim($_POST['title'] ?? '');
                $data['description'] = trim($_POST['description'] ?? '');
                $data['allow_overwrite'] = isset($_POST['allow_overwrite']);
                $data['updated_at'] = date('c');
                $urls[$code] = $data;
                saveUrls($urls);
                $adminMessage = 'updated';
            }
        }
    }
    
    // Reload URLs after potential changes
    $urls = loadUrls();
    
    ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Cut It Off - URL Shortener</title>
	<link rel="manifest" href="manifest.json">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="512x512" href="favicon.png"/>
	<link rel="shortcut icon" href="favicon.ico"/>	
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">	
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #12121a;
            --bg-tertiary: #1a1a25;
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --danger: #ef4444;
            --danger-hover: #f87171;
            --success: #10b981;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #2a2a3a;
        }
		
		/* Light theme */
		[data-theme="light"] {
			--bg-primary: #f5f5f7;
			--bg-secondary: #ffffff;
			--bg-tertiary: #e8e8ec;
			--accent: #6366f1;
			--accent-hover: #4f46e5;
			--danger: #dc2626;
			--danger-hover: #b91c1c;
			--success: #16a34a;
			--text-primary: #1a1a1a;
			--text-secondary: #4a4a4a;
			--text-muted: #6b6b6b;
			--border: rgba(0,0,0,0.1);
		}
		
		/* Theme toggle button */
		.icon-btn {
			width: 40px;
			height: 40px;
			border-radius: 8px;
			border: 1px solid var(--border);
			background: var(--bg-tertiary);
			color: var(--text-secondary);
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 16px;
			transition: all 0.2s ease;
		}
		
		.icon-btn:hover {
			background: var(--accent);
			color: white;
			border-color: var(--accent);
		}
        
        body {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        
        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        h1 span {
            background: linear-gradient(135deg, var(--accent), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-box {
            background: var(--bg-secondary);
            padding: 50px;
            border-radius: 16px;
            max-width: 400px;
            margin: 100px auto;
            border: 1px solid var(--border);
        }
        
        .login-box h2 {
            margin-bottom: 30px;
            font-size: 1.5rem;
        }
        
        input, textarea {
            width: 100%;
            padding: 14px 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        
        button, .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
            padding: 8px 16px;
            font-size: 13px;
        }
        
        .btn-danger:hover {
            background: var(--danger);
            color: white;
        }
        
        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--border);
        }
        
        .url-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .url-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            transition: border-color 0.2s;
        }
        
        .url-card:hover {
            border-color: var(--accent);
        }
        
        .url-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        
        .url-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent);
        }
        
        .url-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--text-muted);
        }
        
        .url-title {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 4px;
            color: var(--text-primary);
        }
        
        .url-description {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 12px;
        }
        
        .url-target {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--text-muted);
            word-break: break-all;
            background: var(--bg-tertiary);
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        
        .url-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .edit-form {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        
        .edit-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }
        
        textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .api-info {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
        
        .api-info h3 {
            font-size: 1rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .api-token {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            background: var(--bg-tertiary);
            padding: 12px 16px;
            border-radius: 6px;
            word-break: break-all;
            position: relative;
        }
        
        .copy-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        
        .empty-state h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: var(--text-secondary);
        }
        
        .stats {
            display: flex;
            gap: 24px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 28px;
            min-width: 140px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--accent);
        }
        
        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .back-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .back-link:hover {
            color: var(--accent);
        }
        
        @media (max-width: 640px) {
            .url-header {
                flex-direction: column;
                gap: 12px;
            }
            
            .stats {
                flex-direction: column;
                gap: 12px;
            }
        }
			.footer {margin-top: 24px;text-align: center;}
        .footer a {color: var(--text-muted);text-decoration: none;font-size: 13px;transition: color 0.2s;}
        .footer a:hover {color: var(--accent-light);}
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$isLoggedIn): ?>
        <div class="login-box">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <h2 style="margin:0;">🔐 Admin Login</h2>
                <button class="icon-btn" id="theme-toggle" title="Toggle theme">
                    <i class="fas fa-sun"></i>
                </button>
            </div>
            <form method="post">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required autofocus>
                </div>
                <button type="submit" name="admin_login" class="btn btn-primary" style="width:100%">
                    Login
                </button>
            </form>
        </div>

    <div class="footer">
            <a href="./">Back to homepage →</a>
        </div>		
        <?php else: ?>
        
        <header>
            <h1>⚡ <span>Cut It Off Admin</span></h1>
            <div style="display:flex;gap:16px;align-items:center;">
                <button class="icon-btn" id="theme-toggle" title="Toggle theme">
                    <i class="fas fa-sun"></i>
                </button>
                <a href="<?= htmlspecialchars(dirname($_SERVER['SCRIPT_NAME'])) ?>" class="back-link">← Back to Cut It Off homepage</a>
                <a href="?logout=1" class="btn btn-secondary">Logout</a>
            </div>
        </header>
        
        <?php if ($adminMessage === 'deleted'): ?>
        <div class="alert alert-success">✓ URL successfully deleted</div>
        <?php elseif ($adminMessage === 'updated'): ?>
        <div class="alert alert-success">✓ URL successfully updated</div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?= count($urls) ?></div>
                <div class="stat-label">Total URLs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= array_sum(array_map(function($d) { 
                    return is_array($d) ? ($d['clicks'] ?? 0) : 0; 
                }, $urls)) ?></div>
                <div class="stat-label">Total Clicks</div>
            </div>
        </div>
        
        <div class="api-info">
            <h3>🔑 API Token</h3>
            <div class="api-token">
                <?= htmlspecialchars($config['api_token']) ?>
                <button class="btn btn-secondary copy-btn" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($config['api_token']) ?>'); this.textContent='Copied!';">
                    Copy
                </button>
            </div>
        </div>
        
        <div class="url-grid">
            <?php if (empty($urls)): ?>
            <div class="empty-state">
                <h3>No URLs yet</h3>
                <p>Create your first short URL to see it here.</p>
            </div>
            <?php else: ?>
            <?php foreach ($urls as $code => $data): 
                $url = is_array($data) ? $data['url'] : $data;
                $title = is_array($data) ? ($data['title'] ?? '') : '';
                $description = is_array($data) ? ($data['description'] ?? '') : '';
                $clicks = is_array($data) ? ($data['clicks'] ?? 0) : 0;
                $allowOverwrite = is_array($data) ? ($data['allow_overwrite'] ?? false) : false;
                $createdAt = is_array($data) && isset($data['created_at']) 
                    ? date('M j, Y', strtotime($data['created_at'])) 
                    : 'Unknown';
            ?>
            <div class="url-card">
                <div class="url-header">
                    <div>
                        <div class="url-code">/<?= htmlspecialchars($code) ?><?= $allowOverwrite ? ' <span style="font-size:11px;color:var(--text-muted);font-weight:400;">(editable)</span>' : ' <span style="font-size:11px;color:var(--success);font-weight:400;">🔒</span>' ?></div>
                        <?php if ($title): ?>
                        <div class="url-title"><?= htmlspecialchars($title) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="url-meta">
                        <span>📊 <?= $clicks ?> clicks</span>
                        <span>📅 <?= $createdAt ?></span>
                    </div>
                </div>
                
                <?php if ($description): ?>
                <div class="url-description"><?= htmlspecialchars($description) ?></div>
                <?php endif; ?>
                
                <div class="url-target"><?= htmlspecialchars($url) ?></div>
                
                <div class="url-actions">
                    <a href="<?= htmlspecialchars(getBaseUrl() . $code) ?>" target="_blank" class="btn btn-secondary">
                        Open Link ↗
                    </a>
                    <button class="btn btn-secondary" onclick="toggleEdit('<?= htmlspecialchars($code) ?>')">
                        ✏️ Edit
                    </button>
                    <form method="post" style="display:inline" onsubmit="return confirm('Delete this URL?');">
                        <input type="hidden" name="delete_code" value="<?= htmlspecialchars($code) ?>">
                        <button type="submit" class="btn btn-danger">🗑️ Delete</button>
                    </form>
                </div>
                
                <div class="edit-form" id="edit-<?= htmlspecialchars($code) ?>">
                    <form method="post">
                        <input type="hidden" name="update_code" value="<?= htmlspecialchars($code) ?>">
                        <div class="form-group">
                            <label for="title-<?= htmlspecialchars($code) ?>">Title</label>
                            <input type="text" name="title" id="title-<?= htmlspecialchars($code) ?>" 
                                   value="<?= htmlspecialchars($title) ?>" placeholder="Optional title...">
                        </div>
                        <div class="form-group">
                            <label for="desc-<?= htmlspecialchars($code) ?>">Description</label>
                            <textarea name="description" id="desc-<?= htmlspecialchars($code) ?>" 
                                      placeholder="Optional description..."><?= htmlspecialchars($description) ?></textarea>
                        </div>
                        <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                            <input type="checkbox" name="allow_overwrite" id="overwrite-<?= htmlspecialchars($code) ?>" 
                                   <?= $allowOverwrite ? 'checked' : '' ?> style="width:18px;height:18px;">
                            <label for="overwrite-<?= htmlspecialchars($code) ?>" style="margin:0;cursor:pointer;">
                                Allow this code to be overwritten
                            </label>
                        </div>
                        <div style="display:flex;gap:12px">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" onclick="toggleEdit('<?= htmlspecialchars($code) ?>')">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <script>
            function toggleEdit(code) {
                const form = document.getElementById('edit-' + code);
                form.classList.toggle('active');
            }
        </script>
        
        <?php endif; ?>
    </div>
	
	<script>
		// Theme toggle (works for both login and admin pages)
		const themeToggleGlobal = document.getElementById('theme-toggle');
		if (themeToggleGlobal) {
			const htmlEl = document.documentElement;

			// Check for saved theme preference or default to dark
			const savedThemeGlobal = localStorage.getItem('theme') || 'dark';
			htmlEl.setAttribute('data-theme', savedThemeGlobal);
			updateThemeIconGlobal(savedThemeGlobal);

			themeToggleGlobal.addEventListener('click', () => {
				const currentTheme = htmlEl.getAttribute('data-theme');
				const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
				
				htmlEl.setAttribute('data-theme', newTheme);
				localStorage.setItem('theme', newTheme);
				updateThemeIconGlobal(newTheme);
			});

			function updateThemeIconGlobal(theme) {
				const icon = themeToggleGlobal.querySelector('i');
				if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
			}
		}
	</script>
</body>
</html>
    <?php
    exit;
}

// ============================================
// REDIRECT HANDLER
// ============================================

if ($path && isset($urls[$path])) {
    $data = is_array($urls[$path]) ? $urls[$path] : ['url' => $urls[$path]];
    
    // Increment click counter
    if (!isset($data['clicks'])) $data['clicks'] = 0;
    $data['clicks']++;
    $urls[$path] = $data;
    saveUrls($urls);
    
    header('Location: ' . $data['url'], true, 301);
    exit;
}

// ============================================
// MAIN SHORTENER UI
// ============================================

$message = '';
$shortUrl = '';
$createdCode = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = filter_var(trim($_POST['url']), FILTER_VALIDATE_URL);
    $customCode = isset($_POST['custom_code']) ? trim($_POST['custom_code']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $allowOverwrite = isset($_POST['allow_overwrite']);
    
    if ($url) {
        $code = null;
        
        if ($customCode) {
            if (!isValidCode($customCode)) {
                $message = 'invalid_code';
            } elseif (isset($urls[$customCode])) {
                // Check if existing URL allows overwriting
                $existingData = is_array($urls[$customCode]) ? $urls[$customCode] : ['url' => $urls[$customCode]];
                $canOverwrite = $existingData['allow_overwrite'] ?? false;
                
                if (!$canOverwrite) {
                    $message = 'code_protected';
                } else {
                    $code = $customCode;
                }
            } else {
                $code = $customCode;
            }
        } else {
            // Check if URL already exists
            foreach ($urls as $existingCode => $data) {
                $existingUrl = is_array($data) ? $data['url'] : $data;
                if ($existingUrl === $url) {
                    $code = $existingCode;
                    break;
                }
            }
            
            if (!$code) {
                do {
                    $code = generateCode();
                } while (isset($urls[$code]));
            }
        }
        
        if ($code && !$message) {
            $urls[$code] = [
                'url' => $url,
                'title' => $title,
                'description' => $description,
                'allow_overwrite' => $allowOverwrite,
                'created_at' => isset($urls[$code]) && is_array($urls[$code]) 
                    ? $urls[$code]['created_at'] 
                    : date('c'),
                'updated_at' => date('c'),
                'clicks' => isset($urls[$code]) && is_array($urls[$code]) 
                    ? ($urls[$code]['clicks'] ?? 0) 
                    : 0
            ];
            
            saveUrls($urls);
            $shortUrl = getBaseUrl() . $code;
            $createdCode = $code;
            $message = 'success';
        }
    } else {
        $message = 'invalid_url';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cut It Off - URL Shortener</title>
	<link rel="manifest" href="manifest.json">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="512x512" href="favicon.png"/>
	<link rel="shortcut icon" href="favicon.ico"/>
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">	
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --bg: #0c0c10;
            --surface: #141419;
            --surface-2: #1c1c24;
            --accent: #7c3aed;
            --accent-light: #a78bfa;
            --accent-glow: rgba(124, 58, 237, 0.3);
            --success: #22c55e;
            --error: #ef4444;
            --warning: #f59e0b;
            --text: #fafafa;
            --text-dim: #a1a1aa;
            --text-muted: #71717a;
            --border: rgba(255,255,255,0.08);
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Ambient background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(124, 58, 237, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(167, 139, 250, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

		/* Light theme */
		[data-theme="light"] {
			--bg: #f5f5f7;
			--surface: #ffffff;
			--surface-2: #e8e8ec;
			--accent: #7c3aed;
			--accent-light: #6d28d9;
			--accent-glow: rgba(124, 58, 237, 0.2);
			--success: #16a34a;
			--error: #dc2626;
			--warning: #d97706;
			--text: #1a1a1a;
			--text-dim: #4a4a4a;
			--text-muted: #6b6b6b;
			--border: rgba(0,0,0,0.1);
		}
		
		[data-theme="light"] body::before {
			background: 
				radial-gradient(ellipse at 20% 20%, rgba(124, 58, 237, 0.05) 0%, transparent 50%),
				radial-gradient(ellipse at 80% 80%, rgba(167, 139, 250, 0.03) 0%, transparent 50%);
		}
		
		[data-theme="light"] .card {
			box-shadow: 
				0 4px 24px rgba(0,0,0,0.08),
				0 0 0 1px rgba(0,0,0,0.05) inset;
		}
		
		/* Theme toggle button */
		.nav-actions {
			display: flex;
			justify-content: center;
			margin-bottom: 24px;
		}
		
		.icon-btn {
			width: 44px;
			height: 44px;
			border-radius: 12px;
			border: 1px solid var(--border);
			background: var(--surface);
			color: var(--text-dim);
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 18px;
			transition: all 0.2s ease;
		}
		
		.icon-btn:hover {
			background: var(--surface-2);
			color: var(--accent-light);
			border-color: var(--accent);
		}
        
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 540px;
        }
        
        .brand {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }
        
        .brand h1 span {
            background: linear-gradient(135deg, var(--accent-light), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand p {
            color: var(--text-muted);
            font-size: 1rem;
        }
        
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 
                0 4px 24px rgba(0,0,0,0.3),
                0 0 0 1px rgba(255,255,255,0.02) inset;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-dim);
            margin-bottom: 8px;
        }
        
        .form-group.optional label::after {
            content: 'optional';
            margin-left: 8px;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 400;
        }
        
        input, textarea {
            width: 100%;
            padding: 16px 18px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-family: inherit;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        
        input::placeholder, textarea::placeholder {
            color: var(--text-muted);
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }
        
        .input-url {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }
        
        textarea {
            min-height: 70px;
            resize: vertical;
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background: var(--surface-2);
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .checkbox-group:hover {
            background: rgba(124, 58, 237, 0.1);
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        
        .checkbox-group span {
            font-size: 13px;
            color: var(--text-dim);
        }
        
        .btn {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--accent) 0%, #9333ea 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px var(--accent-glow);
        }
        
        .btn:hover::before {
            opacity: 1;
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .result {
            margin-top: 24px;
            padding: 24px;
            border-radius: 14px;
            animation: slideIn 0.4s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .result.success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .result.error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .result.warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        .result-title {
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .result.success .result-title { color: var(--success); }
        .result.error .result-title { color: var(--error); }
        .result.warning .result-title { color: var(--warning); }
        
        .short-url-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface-2);
            padding: 14px 18px;
            border-radius: 10px;
            margin-top: 12px;
        }
        
        .short-url-box a {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: var(--accent-light);
            text-decoration: none;
            word-break: break-all;
        }
        
        .short-url-box a:hover {
            text-decoration: underline;
        }
        
        .copy-btn {
            padding: 10px 18px;
            background: var(--accent);
            border: none;
            border-radius: 8px;
            color: white;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .copy-btn:hover {
            background: var(--accent-light);
        }
        
        .toggle-advanced {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            background: transparent;
            border: 1px dashed var(--border);
            border-radius: 10px;
            color: var(--text-muted);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .toggle-advanced:hover {
            border-color: var(--accent);
            color: var(--accent-light);
        }
        
        .advanced-options {
            display: none;
        }
        
        .advanced-options.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .footer {
            margin-top: 24px;
            text-align: center;
        }
        
        .footer a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }
        
        .footer a:hover {
            color: var(--accent-light);
        }
        
        @media (max-width: 480px) {
            .row {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 24px;
            }
            
            .brand h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <h1>✂️ <span>Cut It Off</span></h1>
            <p>Transform long URLs into memorable links</p>
        </div>
		<div class="nav-actions">
    <button class="icon-btn" id="theme-toggle" title="Toggle theme">
        <i class="fas fa-sun"></i>
    </button>
</div>
        
        <div class="card">
            <form method="post">
                <div class="form-group">
                    <label for="url">Destination URL</label>
                    <input type="url" name="url" id="url" class="input-url" 
                           placeholder="https://example.com/your-long-url..." required>
                </div>
                
                <button type="button" class="toggle-advanced" onclick="toggleAdvanced()">
                    <span id="toggle-text">▸ Show advanced options</span>
                </button>
                
                <div class="advanced-options" id="advanced">
                    <div class="row">
                        <div class="form-group optional">
                            <label for="custom_code">Custom Short Code</label>
                            <input type="text" name="custom_code" id="custom_code" 
                                   placeholder="my-link" pattern="[a-zA-Z0-9_-]{1,50}">
                        </div>
                        <div class="form-group optional">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" placeholder="My Link">
                        </div>
                    </div>
                    
                    <div class="form-group optional">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" 
                                  placeholder="A note to remember what this link is for..."></textarea>
                    </div>
                    
                    <label class="checkbox-group">
                        <input type="checkbox" name="allow_overwrite">
                        <span>Allow this short code to be overwritten later</span>
                    </label>
                </div>
                
                <button type="submit" class="btn">Shorten URL</button>
            </form>
            
            <?php if ($message === 'success'): ?>
            <div class="result success">
                <div class="result-title">✓ Link created successfully</div>
                <div class="short-url-box">
                    <a href="<?= htmlspecialchars($shortUrl) ?>" target="_blank">
                        <?= htmlspecialchars($shortUrl) ?>
                    </a>
                    <button class="copy-btn" onclick="copyUrl(this)">Copy</button>
                </div>
            </div>
            <?php elseif ($message === 'invalid_url'): ?>
            <div class="result error">
                <div class="result-title">✕ Invalid URL</div>
                <p style="color:var(--text-dim);font-size:14px;">Please enter a valid URL including http:// or https://</p>
            </div>
            <?php elseif ($message === 'invalid_code'): ?>
            <div class="result error">
                <div class="result-title">✕ Invalid custom code</div>
                <p style="color:var(--text-dim);font-size:14px;">Use only letters, numbers, dashes and underscores (1-50 characters)</p>
            </div>
            <?php elseif ($message === 'code_protected'): ?>
            <div class="result warning">
                <div class="result-title">⚠ Code is protected</div>
                <p style="color:var(--text-dim);font-size:14px;">This short code already exists and was set to not allow overwriting. Please choose a different code.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <a href="admin">Admin Panel →</a>
        </div>
    </div>
    
    <script>
        function toggleAdvanced() {
            const adv = document.getElementById('advanced');
            const text = document.getElementById('toggle-text');
            adv.classList.toggle('show');
            text.textContent = adv.classList.contains('show') 
                ? '▾ Hide advanced options' 
                : '▸ Show advanced options';
        }
        
        function copyUrl(btn) {
            const url = btn.previousElementSibling.textContent.trim();
            navigator.clipboard.writeText(url).then(() => {
                btn.textContent = 'Copied!';
                setTimeout(() => btn.textContent = 'Copy', 2000);
            });
        }
				
		// Theme toggle
		const themeToggle = document.getElementById('theme-toggle');
		const html = document.documentElement;

		// Check for saved theme preference or default to dark
		const savedTheme = localStorage.getItem('theme') || 'dark';
		html.setAttribute('data-theme', savedTheme);
		updateThemeIcon(savedTheme);

		themeToggle.addEventListener('click', () => {
			const currentTheme = html.getAttribute('data-theme');
			const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
			
			html.setAttribute('data-theme', newTheme);
			localStorage.setItem('theme', newTheme);
			updateThemeIcon(newTheme);
		});

		function updateThemeIcon(theme) {
			const icon = themeToggle.querySelector('i');
			icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
		}
		
    </script>
</body>
</html>
