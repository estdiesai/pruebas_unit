<?php

namespace App\Service;

use App\Enum\HealthStatus;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubService
{
    public function __construct(private HttpClientInterface $httpClient, private LoggerInterface $logger)
    {
    }

    public function getHealthReport(string $dinosaurName): HealthStatus
    {
        $health = HealthStatus::HEALTHY;
        //$client = HttpClient::create();
        // $response = $client->request( // lo cambiamos por httpClient
        $response = $this->httpClient->request(
            method: 'GET',
            url: 'https://api.github.com/repos/SymfonyCasts/dino-park/issues'
        );

        // Para registar las peticiones a la API
        $this->logger->info('Resquest Dino Issues', [
            'dino' => $dinosaurName,
            'responseStatus' => $response->getStatusCode(),
        ]);

        foreach ($response->toArray() as $issue) {
            if (str_contains($issue['title'], $dinosaurName)) {
                $health = $this->getDinoStatusFromLabels($issue['labels']);
            }
        }

        return $health;
    }

    private function getDinoStatusFromLabels(array $labels): HealthStatus
    {
        $status = null;
        // si una etiqueta tiene el prefijo "Estado:",
        // lo cortamos, ponemos lo que queda en $status, y lo pasamos a tryFrom() para poder devolver un HealthStatus
        foreach ($labels as $label) {
            $label = $label['name'];
            // We only care about "Status" labels
            if (!str_starts_with($label, 'Status:')) {
                continue;
            }
            // Remove the "Status:" and whitespace from the label
            $status = trim(substr($label, strlen('Status:')));

            // Creamos una excepción sy TryForm devuelve null
            $health = HealthStatus::tryFrom($status);
            if(null === $health)
            {
                throw new \RuntimeException(sprintf('%s is an unkonown status label!', $label));// este mensaje se muestra con --testdox
            }
        }
        return $health ?? HealthStatus::HEALTHY;
    }
}
