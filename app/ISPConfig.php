<?php

namespace App;

use Zttp\Zttp;
use Exception;
use Illuminate\Support\Collection;

class ISPConfig
{
    protected $apiUrl;

    protected $apiUsername;

    protected $apiPassword;

    protected $verifySSL;

    protected $sessionId;

    public function __construct($url, $username, $password, $verifySSL = false)
    {
        $this->apiUrl = $url;
        $this->apiUsername = $username;
        $this->apiPassword = $password;
        $this->verifySSL = $verifySSL;
    }

    public function request($method, $data)
    {
        $url = "{$this->apiUrl}?{$method}";

        if (!$this->sessionId && $method !== 'login') {
            throw new \Exception('No session id defined. Call login first');
        } else {
            $data = array_merge($data, ['session_id' => $this->sessionId]);
        }

        return Zttp::withOptions(['verify' => $this->verifySSL])
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $data);
    }


    public function login(bool $refreshSessionId = false): string
    {
        if (!$refreshSessionId && $this->sessionId) {
            return $this->sessionId;
        }
        $url = $this->apiUrl . '?login';

        $response = $this->request('login', [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword,
        ]);

        $data = $response->json();

        if ($data['code'] !== 'ok') {
            throw new Exception($data['message']);
        }
        $this->sessionId = $data['response'];

        return $data['response'];
    }

    public function getServers(): Collection
    {
        $response = $this->request('server_get_all', []);

        return collect($response->json()['response']);
    }

    public function getServersServices(int $serverId): Collection
    {
        $response = $this->request('server_get_functions', ['server_id' => $serverId]);

        return collect($response->json()['response']);
    }

    public function getClients(int $clientId = null): Collection
    {
        if ($clientId) {
            $response = $this->request('client_get', ['client_id' => $clientId]);
        } else {
            $response = $this->request('client_get_all', []);
        }

        return collect($response->json()['response']);
    }

    public function getDatabases(int $clientId = null): Collection
    {
        $response = $this->request('sites_database_get_all_by_user', ['client_id' => $clientId]);

        return collect($response->json()['response']);
    }

    public function getSitesByUser(string $sysUserId = null, string $sysGroupId = null): Collection
    {
        $response = $this->request('client_get_sites_by_user', [
            'sys_userid' => $sysUserId,
            'sys_groupid' => $sysGroupId,
        ]);

        return collect($response->json()['response'])->sort();
    }

    public function getAvailableFunctions(): Collection
    {
        $response = $this->request('get_function_list', []);

        return collect($response->json()['response'])->sort();
    }

    public function getShellUsers(int $userId = -1): Collection
    {
        $response = $this->request('sites_shell_user_get', ['primary_id' => $userId]);

        return collect($response->json()['response'])->sort();
    }
}
