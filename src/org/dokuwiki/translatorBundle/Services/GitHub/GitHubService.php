<?php

namespace org\dokuwiki\translatorBundle\Services\GitHub;

use Github\Client;
use Github\HttpClient\CachedHttpClient;

class GitHubService {

    private $token;
    private $client;

    function __construct($gitHubApiToken, $dataFolder, $autoStartup = true) {
        if (!$autoStartup) {
            return;
        }
        $this->token = $gitHubApiToken;
        $this->client = new Client(
            new CachedHttpClient(array('cache_dir' => "$dataFolder/githubcache"))
        );
        $this->client->authenticate($gitHubApiToken, null, Client::AUTH_URL_TOKEN);
    }

    /**
     * @param string $url GitHub URL to create the fork from
     * @return string Git URL of the fork
     */
    public function createFork($url) {
        list($user, $repository) = $this->getUsernameAndRepositoryFromURL($url);
        $result = $this->client->api('repo')->forks()->create($user, $repository);
        return $result['git_url'];
    }

    public function getUsernameAndRepositoryFromURL($url) {
        $result = preg_replace('#^(https://github.com/|git@github.com:|git://github.com/)(.*)\.git$#', '$2', $url, 1, $counter);
        if ($counter === 0) {
            throw new GitHubServiceException('Invalid GitHub URL: ' . $url);
        }
        $result = explode('/', $result);

        return $result;
    }

}