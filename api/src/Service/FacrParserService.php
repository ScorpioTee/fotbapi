<?php

namespace App\Service;

use ApiPlatform\Metadata\Exception\RuntimeException;

class FacrParserService
{
    private const COMPETITION_SUBJECT_URL_PATTERN = 'https://www.fotbal.cz/souteze/subjekty/subjekt/%s';
    private const TABLE_URL_PATTERN =  'https://is1.fotbal.cz/souteze/tabulky-souteze.aspx?req=%s&sport=fotbal';

    private ?\DOMDocument $dom = null;
    private array $data;


    public function generateCompetitionData(): void
    {
        $subjectId = 248;

        $this->initDomDocument(sprintf(self::COMPETITION_SUBJECT_URL_PATTERN, $subjectId));
        $this->findCompetitionUUID();
    }

    public function competitionTable(string $uuid): void
    {
        $this->initDomDocument(sprintf(self::TABLE_URL_PATTERN, $uuid));
        $this->fillCompetitionTableData($uuid);
    }

    private function fillCompetitionTableData(string $uuid): void
    {
        $table = $this->dom->getElementsByTagName('table');
        $tableItem = $table->item(0);
        if ($tableItem === null) {
            throw new RuntimeException('Table not published OR parse error.');
            //echo "Table not published ...";
        }
        $rows = $tableItem->getElementsByTagName('tr');
        /** @var DOMElement $row */
        foreach($rows as $row) {
            if (strpos($row->nodeValue, 'Rk.') !== false) continue;

            $col = $row->getElementsByTagName('td');
            $team = trim(preg_replace('/\([0-9]+\)/','',$col->item(1)->nodeValue));
            $scoreArray = explode(':', $col->item(6)->nodeValue);
            $this->data[$uuid][] = [
                'position' => $col->item(0)->nodeValue,
                'club' => trim($team),
                'win' => $col->item(3)->nodeValue,
                'draw' => $col->item(4)->nodeValue,
                'lost' => $col->item(5)->nodeValue,
                'goalsScored' => $scoreArray[0],
                'goalsReceived' => $scoreArray[1],
                'points' => $col->item(7)->nodeValue,
            ];
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function resetData(): void
    {
        $this->data = [];
    }


    private function initDomDocument($url): void
    {
        $dom = new \DomDocument();
        $content = file_get_contents($url);
        try {
            libxml_use_internal_errors(true);
            $dom->loadHTML($content);
        } catch(\Exception $e) {
            return;
        }
        if ($dom === null) {
            return;
        }
        $this->dom = $dom;
    }

    private function findCompetitionUUID()
    {
        $table = $this->dom->getElementsByTagName('table');
        $tableItem = $table->item(0);
        $rows = $tableItem->getElementsByTagName('tr');
        /** @var DOMElement $row */
        foreach($rows as $row) {
            if (strpos($row->nodeValue, 'Soutěž') !== false) continue;
            /** @var \DomElement $anchor */
            $anchor = $row->getElementsByTagName('a')->item(0);
            $hrefItems = explode('/', $anchor->getAttribute('href'));
            $this->data[$hrefItems[count($hrefItems)-1]] = ['name' => $anchor->nodeValue];
        }
    }

    public function loadMatchStatistics()
    {
        $ch = curl_init("https://is1.fotbal.cz/hraci/statistiky.aspx?req=4f801458-28a5-4f7b-9e99-3094588deecc&sport=fotbal");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            '__EVENTTARGET' => 'ctl00$MainContent$gridData',
            '__EVENTARGUMENT' => 'Page$3',
            '__VIEWSTATE' => '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',
        ]);

        $data = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        if ($curl_errno > 0) {
            echo "cURL Error ($curl_errno): $curl_error\n";
        } else {
            echo "Data received: $data\n";
        }
    }

}
