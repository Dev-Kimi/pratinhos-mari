<?php
function fb_load_credentials() {
    $path1 = __DIR__ . '/firebase_key.json';
    if (!file_exists($path1)) return null;
    $json = file_get_contents($path1);
    return json_decode($json, true);
}
function fb_b64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function fb_get_access_token($scopes = ['https://www.googleapis.com/auth/datastore']) {
    $creds = fb_load_credentials();
    if (!$creds) return null;
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $iat = time();
    $claims = [
        'iss' => $creds['client_email'],
        'scope' => implode(' ', $scopes),
        'aud' => $creds['token_uri'],
        'iat' => $iat,
        'exp' => $iat + 3600
    ];
    $segments = [];
    $segments[] = fb_b64url_encode(json_encode($header));
    $segments[] = fb_b64url_encode(json_encode($claims));
    $signing_input = implode('.', $segments);
    $privateKey = $creds['private_key'];
    openssl_sign($signing_input, $signature, openssl_pkey_get_private($privateKey), 'sha256');
    $segments[] = fb_b64url_encode($signature);
    $assertion = implode('.', $segments);
    $ch = curl_init($creds['token_uri']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $assertion
        ])
    ]);
    $resp = curl_exec($ch);
    if ($resp === false) return null;
    $data = json_decode($resp, true);
    curl_close($ch);
    return $data['access_token'] ?? null;
}
function fb_firestore_base() {
    $creds = fb_load_credentials();
    if (!$creds) return null;
    $project = $creds['project_id'];
    return "https://firestore.googleapis.com/v1/projects/$project/databases/(default)/documents";
}
function fb_http($method, $url, $body = null, $token = null) {
    $ch = curl_init($url);
    $headers = ['Content-Type: application/json'];
    if ($token) $headers[] = "Authorization: Bearer $token";
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($body) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $resp = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return [$status, $resp];
}
function fb_firestore_create($collectionPath, $docId, $fields) {
    $token = fb_get_access_token();
    $base = fb_firestore_base();
    if (!$token || !$base) return null;
    $url = $base . '/' . trim($collectionPath, '/') . '?documentId=' . urlencode($docId);
    $body = ['fields' => $fields];
    list($status, $resp) = fb_http('POST', $url, $body, $token);
    if ($status >= 200 && $status < 300) {
        return json_decode($resp, true);
    }
    return null;
}
function fb_firestore_create_sub($documentPath, $subCollection, $fields) {
    $token = fb_get_access_token();
    $base = fb_firestore_base();
    if (!$token || !$base) return null;
    $url = $base . '/' . trim($documentPath, '/') . '/' . trim($subCollection, '/');
    $body = ['fields' => $fields];
    list($status, $resp) = fb_http('POST', $url, $body, $token);
    if ($status >= 200 && $status < 300) {
        return json_decode($resp, true);
    }
    return null;
}
function fb_val_string($v) { return ['stringValue' => (string)$v]; }
function fb_val_int($v) { return ['integerValue' => (string)intval($v)]; }
function fb_val_double($v) { return ['doubleValue' => (float)$v]; }
function fb_val_timestamp($iso) { return ['timestampValue' => $iso]; }
