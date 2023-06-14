<?php

namespace Tobizzlelol\Departures\Classes;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

date_default_timezone_set('Europe/Berlin');

class departureController
{

    private \PDO $conn;

    public function __construct(string $dsn, string $username, string $password)
    {
        $this->conn = new \PDO($dsn, $username, $password);
    }

    public function update()
    {
        $domDoc = $this->getDeparturesFromWeb('https://www.kvb.koeln/generated/?aktion=show&code=336');
        $this->saveToFile($domDoc);
        $departureArray = $this->getDeparturesFromXmlFile();
        $this->saveDeparturesToDb($departureArray);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function view()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__. '/../../resources');
        //@todo
        $twig = new \Twig\Environment($loader);
        $template = $twig->load('templates/default.html');
        global $currentTime;

        echo $template->render( ['departures' => $this->findDepartures($currentTime)]);
    }

    public function check()
    {
        global $currentTime;
        if (count($this->findDepartures($currentTime)) < 10) {
            $this->update();
        }
    }

    public function findDepartures($time)
    {
        $stmt = $this->conn->prepare('SELECT * FROM departure WHERE time>=:time');
        $stmt->bindParam(':time', $time);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function getDeparturesFromWeb($url)
    {
        $context = [
            'http' => [
                'header' => 'User-Agent: Chrome/113.0.0.0 Safari/537.36 Edg/113.0.1774.50'
            ]
        ];

        $context = stream_context_create($context);
        $rawXmlSting = file_get_contents($url, false, $context);
        $domDoc = new \DOMDocument();
        $domDoc->loadHTML($rawXmlSting, LIBXML_NOERROR);
        $domDoc->saveHTML();

        return $domDoc;
    }

    private function getDeparturesFromXmlFile()
    {
        $rawXmlSting = file_get_contents('packages/departures/resources/Departures.html');
        $xml = simplexml_load_string($rawXmlSting);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        return $array['body']['div']['table'][1]['tr'];
    }

    private function saveToFile($object)
    {
        $object->save('packages/departures/resources/Departures.html');
    }

    private function saveDeparturesToDb($data)
    {

        $this->truncateDb('departure');
        $data[0] = ['td' => ['||', '||', '||']];

        $station = 'Appellhofplatz';
        $stmt = $this->conn->prepare("INSERT INTO departure(station, time, line, direction) VALUES(:station, :time, :line, :direction)");
        foreach ($data as $departure) {

            $minutes = (int)substr($departure['td'][2], 0, -7);
            $time = date('H:i', strtotime('+' . $minutes . ' minutes'));
            $line = (int)substr($departure['td'][0], 2, -4);
            $direction = $departure['td'][1];
            $stmt->bindParam(':station', $station);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':line', $line);
            $stmt->bindParam(':direction', $direction);
            $stmt->execute();
        }
    }

    private function truncateDb($db): void
    {
        $stmt = $this->conn->prepare("TRUNCATE " . $db);
        $stmt->execute();
    }
}
