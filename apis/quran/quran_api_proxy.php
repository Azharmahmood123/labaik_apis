<?php
// quran_api_proxy.php

class QuranAPIProxy
{
    private $baseUrl = 'https://api.alquran.cloud/v1';
    private $cacheExpiry = 3600; // 1 hour cache

    /**
     * Main handler for API requests
     */
    public function handleRequest()
    {
        // Enable CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }

        // Get the endpoint and parameters
        $endpoint = isset($_GET['endpoint']) ? trim($_GET['endpoint']) : '';
        $params = $this->getRequestParams();

        if (empty($endpoint)) {
            $this->sendResponse(400, ['error' => 'Missing endpoint parameter']);
            return;
        }

        // Execute the proxy request
        $result = $this->proxyRequest($endpoint, $params);

        // Send response
        $this->sendResponse(200, $result);
    }

    /**
     * Proxy request to Cloud Quran API
     */
    private function proxyRequest($endpoint, $params)
    {
        try {
            // Build the URL
            $url = $this->baseUrl . '/' . $endpoint;

            // Add query parameters if any
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }

            // Check cache first (optional)
            $cacheKey = md5($url);
            $cached = $this->getCache($cacheKey);
            if ($cached) {
                return json_decode($cached, true);
            }

            // Make the request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Quran-API-Proxy/1.0');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new Exception('Curl error: ' . $error);
            }

            if ($httpCode !== 200) {
                throw new Exception('API returned status code: ' . $httpCode);
            }

            // Cache the response
            $this->setCache($cacheKey, $response);

            return json_decode($response, true);

        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get request parameters
     */
    private function getRequestParams()
    {
        $params = [];

        // Get from GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $params = $_GET;
            // Remove 'endpoint' from params
            unset($params['endpoint']);
        }

        // Get from POST (JSON)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                $data = json_decode($input, true);
                if ($data) {
                    $params = $data;
                }
            }
        }

        return $params;
    }

    /**
     * Send JSON response
     */
    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Simple file-based cache (optional)
     */
    private function getCache($key)
    {
        $cacheFile = sys_get_temp_dir() . '/quran_cache_' . $key . '.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $this->cacheExpiry)) {
            return file_get_contents($cacheFile);
        }
        return false;
    }

    private function setCache($key, $data)
    {
        $cacheFile = sys_get_temp_dir() . '/quran_cache_' . $key . '.json';
        file_put_contents($cacheFile, $data);
    }
}

// Initialize and handle request
$proxy = new QuranAPIProxy();
$proxy->handleRequest();
?>