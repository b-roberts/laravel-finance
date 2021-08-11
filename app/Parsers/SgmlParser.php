<?php

namespace App\Parsers;

use DOMDocument;
use Exception;

class SgmlParser
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
    if (! file_exists ( $ofxFile ))
    {
      throw new \InvalidArgumentException ( "File '{$ofxFile}' could not be found" );
    }
    return $this->loadFromString ( file_get_contents ( $ofxFile ) );
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
	try {
	  $sgmlStart = stripos ( $ofxContent, '<OFX>' );
      $ofxSgml = trim ( substr ( $ofxContent, $sgmlStart ) );
	  $ofxXML = $this->xmlLoadString($ofxSgml);
	  return $ofxXML;
	}
	catch (Exception $e){}



    $ofxContent = utf8_encode ( $ofxContent );
    $ofxContent = $this->conditionallyAddNewlines ( $ofxContent );
    $sgmlStart = stripos ( $ofxContent, '<OFX>' );
    $ofxSgml = trim ( substr ( $ofxContent, $sgmlStart ) );





    $ofxXml = $this->convertSgmlToXml ( $ofxSgml );
    $xml = $this->xmlLoadString ( $ofxXml );
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
    return str_replace ( '<', "\n<", $ofxContent ); // add line breaks to allow XML to parse
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
    $doc = new DOMDocument ();
    $result = $doc->loadXML ( $xmlString );
	if(!$result){
		throw new Exception('Unable to load XML');
	}

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
    if (preg_match ( "/<([A-Za-z0-9.]+)>([\w�-��-�0-9\.\-\_\+\, ;:\[\]\'\&\/\\\*\(\)\+\{\|\}\!\�\$\?=@��#%��~`]+)$/", trim ( $line ), $matches ))
    {
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
    $sgml = str_replace ( [
        "\r\n",
        "\r"
    ], "\n", $sgml );
    $lines = explode ( "\n", $sgml );
    $xml = '';
    foreach ( $lines as $line )
    {
		$fixedLine = trim ( $this->closeUnclosedXmlTags ( $line ) ) . "\n";
      $xml .= $fixedLine;

    }
    return trim ( $xml );
  }
}
