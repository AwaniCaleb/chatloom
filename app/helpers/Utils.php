<?php
class Utils
{
    /**
     * Sanitize input data.
     */
    public static function sanitize($data)
    {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    /**
     * Redirect to a given URL.
     */
    public static function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    public static function callExternalApi($url, $params = [], $method = 'GET')
    {
        $ch = curl_init();

        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log("External API Error: " . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return json_decode($response, true);
    }

    public static function decodeJWT($jwt)
    {
        // A simple JWT decode function for demonstration.
        // In production, use a robust JWT library.
        list($header, $payload, $signature) = explode('.', $jwt);
        $decodedPayload = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        return $decodedPayload;
    }
}
