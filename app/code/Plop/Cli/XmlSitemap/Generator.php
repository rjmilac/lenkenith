<?php
	namespace Plop\Cli\XmlSitemap;
	class Generator {

		protected $_configuration, $configs;

		const PRIORITY = '0.5';
		const FREQUENCY = 'daily';
		const IGNORE_EMPTY_CONTENT_TYPE = false;
		const XMG_AGENT = 'Mozilla/5.0 (compatible; Plop PHP XML Sitemap Generator/1.0)';
		const NL = "\n";

		private $__URL = '', $site_scheme = '', $site_host = '', $_PF = '';

		private $output_file = '';

		private $scanned, $exception_list;

		public function __construct(\Lenkenith\Base\Configuration $configuration){
			$this->_configuration = $configuration;
			$this->configs = $this->_configuration->getConfigs();
			$this->output_file = $this->configs->directories->pub.'/sitemaps/sitemap_'.strtotime(date('Y-m-d H:i:s')).'.xml';
			$this->scanned = array();
			$this->exception_list = array(
					$this->configs->baseUrl.'bin',
					$this->configs->baseUrl.'var',
					$this->configs->baseUrl.'config'
				);
		}

		public function generate($url){
			echo self::NL;
			$this->__URL = rtrim($url,'/').'/';
			$this->site_scheme = parse_url($this->__URL, PHP_URL_SCHEME);
    		$this->site_host = parse_url ($this->__URL, PHP_URL_HOST);
    		$this->_PF = fopen ($this->output_file, "w");
		    if (!$this->_PF)
		    {
		        echo "Cannot create " . $this->output_file . "!" . self::NL;
		        return;
		    }

		    fwrite ($this->_PF, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                 "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"\n" .
                 "        xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n" .
                 "        xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9\n" .
                 "        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n" .
                 "  <url>\n" .
                 "    <loc>" . rtrim($this->__URL,'/') . "/</loc>\n" .
                 "    <changefreq>" . self::FREQUENCY . "</changefreq>\n" .
                 "  </url>\n");

		    $this->scan($this->getEffectiveUrl($this->__URL));
		    fwrite ($this->_PF, "</urlset>\n");	    
		    fclose ($this->_PF);
		    echo "Created : " . $this->output_file . self::NL;
		    echo "Updated : /sitemap.xml" . self::NL;
		    echo self::NL;
		    copy($this->output_file, 'sitemap.xml');
		    return true;
		}

		private function getPage ($url){
		    $ch = curl_init ($url);
		    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($ch, CURLOPT_USERAGENT, self::XMG_AGENT);
		    $data = curl_exec($ch);
		    curl_close($ch);
		    return $data;
		}

		private function getQuotedUrl($str){
		    $quote = substr ($str, 0, 1);
		    if (($quote != "\"") && ($quote != "'")) // Only process a string 
		    {                                        // starting with singe or
		        return $str;                         // double quotes
		    }                                                 

		    $ret = "";
		    $len = strlen ($str);    
		    for ($i = 1; $i < $len; $i++) // Start with 1 to skip first quote
		    {
		        $ch = substr ($str, $i, 1);
		        
		        if ($ch == $quote) break; // End quote reached

		        $ret .= $ch;
		    }
		    
		    return $ret;
		}

		private function getHrefValue($anchor){
		    $split1  = explode ("href=", $anchor);
		    $split2 = explode (">", $split1[1]);
		    $href_string = $split2[0];

		    $first_ch = substr ($href_string, 0, 1);
		    if ($first_ch == "\"" || $first_ch == "'")
		    {
		        $url = $this->getQuotedUrl ($href_string);
		    }
		    else
		    {
		        $spaces_split = explode (" ", $href_string);
		        $url          = $spaces_split[0];
		    }
		    return $url;
		}

		private function getEffectiveUrl($url){
		    // Create a curl handle
		    $ch = curl_init ($url);

		    // Send HTTP request and follow redirections
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt ($ch, CURLOPT_USERAGENT, self::XMG_AGENT);
		    curl_exec($ch);

		    // Get the last effective URL
		    $effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		    // ie. "http://example.com/show_location.php?loc=M%C3%BCnchen"

		    // Decode the URL, uncoment it an use the variable if needed
		    // $effective_url_decoded = curl_unescape($ch, $effective_url);
		    // "http://example.com/show_location.php?loc=München"

		    // Close the handle
		    curl_close($ch);

		    return $effective_url;
		}

		private function validateUrl($url_base, $url){			
		    $parsed_url = parse_url ($url);
		        
		    $scheme = $parsed_url["scheme"];
		        
		    // Skip URL if different scheme or not relative URL (skips also mailto)
		    if (($scheme != $this->site_scheme) && ($scheme != "")) return false;
		        
		    $host = $parsed_url["host"];
		                
		    // Skip URL if different host
		    if (($host != $this->site_host) && ($host != "")) return false;
		    

		    if ($host == "")    // Handle URLs without host value
		    {
		        if (substr ($url, 0, 1) == '#') // Handle page anchor
		        {
		            echo "Skip page anchor: $url" . self::NL;
		            return false;
		        }
		    
		        if (substr ($url, 0, 1) == '/') // Handle absolute URL
		        {
		            $url = $this->site_scheme . "://" . $this->site_host . $url;
		        }
		        else // Handle relative URL
		        {
		        
		            $path = parse_url ($url_base, PHP_URL_PATH);
		            
		            if (substr ($path, -1) == '/') // URL is a directory
		            {
		                // Construct full URL
		                $url = $this->site_scheme . "://" . $this->site_host . $path . $url;
		            }
		            else // URL is a file
		            {
		                $dirname = dirname ($path);

		                // Add slashes if needed
		                if ($dirname[0] != '/')
		                {
		                    $dirname = "/$dirname";
		                }
		    
		                if (substr ($dirname, -1) != '/')
		                {
		                    $dirname = "$dirname/";
		                }

		                // Construct full URL
		                $url = $this->site_scheme . "://" . $this->site_host . $dirname . $url;
		            }
		        }
		    }

		    // Get effective URL, follow redirected URL
		    $url = $this->getEffectiveUrl ($url); 

		    // Don't scan when already scanned    
		    if (in_array ($url, $this->scanned)) return false;
		    
		    return $url;
		}

		private function skipUrl($url){
		    if (is_array($this->exception_list))
		    {
		        foreach ($this->exception_list as $v)
		        {           
		            if (substr ($url, 0, strlen ($v)) == $v) return true; // Skip this URL
		        }
		    }

		    return false;            
		}

		private function scan($url){

		    ob_flush();

		    $this->scanned[] = $url;  // Add to URL to scanned array

		    if ($this->skipUrl ($url))
		    {
		        echo "Skipped URL $url" . self::NL;
		        return false;
		    }
		    
		    // Remove unneeded slashes
		    if (substr ($url, -2) == "//") 
		    {
		        $url = substr ($url, 0, -2);
		    }
		    // if (substr ($url, -1) == "/") 
		    // {
		    //     $url = substr ($url, 0, -1);
		    // }


		    echo self::NL . "Scan : $url" . self::NL;

		    $headers = get_headers ($url, 1);

		    // Handle pages not found
		    if (strpos ($headers[0], "404") !== false)
		    {
		        echo "Not found : $url" . self::NL;
		        return false;
		    }

		    // Handle redirected pages
		    if (strpos ($headers[0], "301") !== false)
		    {   
		        $url = $headers["Location"];     // Continue with new URL
		        echo "Redirected to : $url" . self::NL;
		    }
		    // Handle other codes than 200
		    else if (strpos ($headers[0], "200") == false)
		    {
		        $url = $headers["Location"];
		        echo "Skip HTTP code $headers[0] : $url" . self::NL;
		        return false;
		    }

		    // Get content type
		    if (is_array ($headers["Content-Type"]))
		    {
		        $content = explode (";", $headers["Content-Type"][0]);
		    }
		    else
		    {
		        $content = explode (";", $headers["Content-Type"]);
		    }
		    
		    $content_type = trim (strtolower ($content[0]));
		    
		    // Check content type for website
		    if ($content_type != "text/html") 
		    {
		        if ($content_type == "" && self::IGNORE_EMPTY_CONTENT_TYPE)
		        {
		            echo "Info : Ignoring empty Content-Type." . self::NL;
		        }
		        else
		        {
		            if ($content_type == "")
		            {
		                echo "Info : Content-Type is not sent by the web server. Change " .
		                     "'IGNORE_EMPTY_CONTENT_TYPE' to 'true' in the sitemap script " .
		                     "to scan those pages too." . self::NL;
		            }
		            else
		            {
		                echo "Info: $url is not a website: $content[0]" . self::NL;
		            }
		            return false;
		        }
		    }

		    $html = $this->getPage ($url);
		    $html = trim ($html);
		    if ($html == "") return true;  // Return on empty page
		    
		    $html = str_replace ("\r", " ", $html);        // Remove newlines
		    $html = str_replace ("\n", " ", $html);        // Remove newlines
		    $html = str_replace ("\t", " ", $html);        // Remove tabs
		    $html = str_replace ("<A ", "<a ", $html);     // <A to lowercase

		    $first_anchor = strpos ($html, "<a ");    // Find first anchor

		    if ($first_anchor === false) return true; // Return when no anchor found

		    $html = substr ($html, $first_anchor);    // Start processing from first anchor

		    $a1   = explode ("<a ", $html);
		    foreach ($a1 as $next_url)
		    {
		        $next_url = trim ($next_url);
		        
		        // Skip empty array entry
		        if ($next_url == "") continue; 
		        
		        // Get the attribute value from href
		        $next_url = $this->getHrefValue($next_url); 
		        
		        // Do all skip checks and construct full URL
		        $next_url = $this->validateUrl($url, $next_url);
		        
		        // Skip if url is not valid
		        if ($next_url == false) continue;

		        if ($this->scan($next_url))
		        {
		            // Add URL to sitemap
		            fwrite ($this->_PF, "  <url>\n" .
		                         "    <loc>" . htmlentities (rtrim($next_url,'/')) ."</loc>\n" .
		                         "    <changefreq>" . self::FREQUENCY . "</changefreq>\n" .
		                         "    <priority>" . self::PRIORITY . "</priority>\n" .
		                         "  </url>\n"); 
		        }
		    }
		    flush();
		    return true;
		}

	}
?>