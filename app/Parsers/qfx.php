<?php
function import($file, $accountID)
{
    global $mysql;
    $parser = new Parser();
    $timestamp = date('Y-m-d H:i:s');

    $doc = $parser->loadFromString(file_get_contents($file));
  /*$doc = new DOMDocument("1.0", "utf-8");
  $doc->load($file); */
    $xpath = new DOMXPath($doc);

  // We starts from the root element
    $query = '//STMTTRN';

    $ledgerBalance = $xpath->query('//LEDGERBAL/BALAMT')[0]->nodeValue;
    $ledgerDate = DateTime::createFromFormat('YmdHis', substr($xpath->query('//LEDGERBAL/DTASOF')[0]->nodeValue, 0, 14))->format('Y-m-d');

    $mysql->query("insert IGNORE into account_balance (account_id,date,value) VALUES ($accountID,'$ledgerDate','$ledgerBalance')");


    $entries = $xpath->query($query);

    foreach ($entries as $entry) {
        $date = $xpath->query('DTPOSTED', $entry)[0]->nodeValue;
        if (!$date) {
              echo 'NO DATE!';
              break;
        }
        $date = (substr($date, 0, 14));

        $date = $mysql->real_escape_string(DateTime::createFromFormat('YmdHis', $date)->format('Y-m-d'));
        $value = $mysql->real_escape_string(- 1 * $xpath->query('TRNAMT', $entry)[0]->nodeValue);

        if ($xpath->query('MEMO', $entry)->length > 0) {
            $location = $mysql->real_escape_string($xpath->query('MEMO', $entry)[0]->nodeValue);
        } else {
            $location = $mysql->real_escape_string($xpath->query('NAME', $entry)[0]->nodeValue);
        }

        $fitid = $mysql->real_escape_string($xpath->query('FITID', $entry)[0]->nodeValue);
        $sql = "SELECT id from transactions where fitid='$fitid' AND account_id='$accountID'";

        $result = $mysql->query($sql);

        if ($result->num_rows == 0) {
            $sql = "INSERT INTO transactions (date,location,value,fitid,created_at,account_id) VALUES ('$date','$location','$value','$fitid','$timestamp','$accountID')";

            $mysql->query($sql);
        }
        $transactionID = $mysql->insert_id;
        if ($xpath->query('OPFT.CATEGORIES', $entry)->length > 0) {
            foreach ($xpath->query('OPFT.CATEGORIES/OPFT.CATEGORY', $entry) as $category) {
                $name = $mysql->real_escape_string($xpath->query('NAME', $category)[0]->nodeValue);
                $sql = "INSERT INTO transaction_detail SELECT null,$transactionID,id,$value,$date FROM categories where name = '$name'";

                $mysql->query($sql);
            }
        }
    }
}
class Parser
{
  /**
   * Load an OFX file into this parser by way of a filename
   *
   * @param string $ofxFile
   *          A path that can be loaded with file_get_contents
   * @return Ofx
   * @throws \Exception
   */
    public function loadFromFile($ofxFile)
    {
        if (! file_exists($ofxFile)) {
            throw new \InvalidArgumentException("File '{$ofxFile}' could not be found");
        }
        return $this->loadFromString(file_get_contents($ofxFile));
    }
  /**
   * Load an OFX by directly using the text content
   *
   * @param string $ofxContent
   * @return Ofx
   * @throws \Exception
   */
    public function loadFromString($ofxContent)
    {
        $ofxContent = utf8_encode($ofxContent);
        $ofxContent = $this->conditionallyAddNewlines($ofxContent);
        $sgmlStart = stripos($ofxContent, '<OFX>');
        $ofxSgml = trim(substr($ofxContent, $sgmlStart));
        $ofxXml = $this->convertSgmlToXml($ofxSgml);

        $xml = $this->xmlLoadString($ofxXml);

        return $xml;
    }
  /**
   * Detect if the OFX file is on one line.
   * If it is, add newlines automatically.
   *
   * @param string $ofxContent
   * @return string
   */
    public function conditionallyAddNewlines($ofxContent)
    {
      // if (preg_match('/<OFX>.*<\/OFX>/', $ofxContent) === 1) {
        return str_replace('<', "\n<", $ofxContent); // add line breaks to allow XML to parse
                                                 // }
        return $ofxContent;
    }
  /**
   * Load an XML string without PHP errors - throws exception instead
   *
   * @param string $xmlString
   * @throws \Exception
   * @return \SimpleXMLElement
   */
    public function xmlLoadString($xmlString)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xmlString);

        return $doc;
    }
  /**
   * Detect any unclosed XML tags - if they exist, close them
   *
   * @param string $line
   * @return string
   */
    public function closeUnclosedXmlTags($line)
    {
      // Matches: <SOMETHING>blah
      // Does not match: <SOMETHING>
      // Does not match: <SOMETHING>blah</SOMETHING>
        if (preg_match("/<([A-Za-z0-9.]+)>([\w�-��-�0-9\.\-\_\+\, ;:\[\]\'\&\/\\\*\(\)\+\{\|\}\!\�\$\?=@��#%��~`]+)$/", trim($line), $matches)) {
            return "<{$matches[1]}>{$matches[2]}</{$matches[1]}>";
        }
        return $line;
    }
  /**
   * Convert an SGML to an XML string
   *
   * @param string $sgml
   * @return string
   */
    public function convertSgmlToXml($sgml)
    {
        $sgml = str_replace([
        "\r\n",
        "\r"
        ], "\n", $sgml);
        $lines = explode("\n", $sgml);
        $xml = '';
        foreach ($lines as $lineID => $line) {
            if (isset($lines[$lineID+1]) && stripos($lines[$lineID+1], '</')===false) {
                $xml .= trim($this->closeUnclosedXmlTags($line)) . "\n";
            } else {
                $xml.=$line . "\n";
            }
        }
        return trim($xml);
    }
}
