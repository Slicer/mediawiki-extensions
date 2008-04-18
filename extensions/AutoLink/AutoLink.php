<?php
# Adds Auto Links in A page.
# Reads page 'AutoLinkPages', which works as Dictonary.
 
define('AUTOLINK_REPLACE_FIRST_ONLY', false);
define('AUTOLINK_EXCLUDED_PAGES', 'AutoLinkPages|Welcome to ASK');
define('AUTOLINK_LINK_DELIMITER', '|');
 
if (!defined('MEDIAWIKI' )) { die(); }                                    
 
$wgExtensionCredits['other'][] = array(                                     
    'name' => "AutoLink (version-1.3)",
    'author' => '[dirk.kreisel@gmx.net]'
);
 
$wgHooks['ArticleSave'][] = 'wfAddAutoLinks';                            
 
#Event Handler for ArticleSave Event.
function wfAddAutoLinks(&$article,&$user,&$text)                      
{                                                                                 
                #file_put_contents('c:\test.txt', $text);
        $content = $text;
        $pages = array();                                                                   
        #This should not work for the Page AutoLinkPages.
 
                $excludedPages = explode('|', AUTOLINK_EXCLUDED_PAGES);            
                foreach ($excludedPages as $excludedPage) {
                if ($article->mTitle->mTextform == $excludedPage) {          
                                return true;                                                                          
                        }                                                                 
                }
 
                $pages = getPages();                                                                         
 
                foreach ($pages as $page) {                                                                 
                        $pos = 0; $offset = 0;                                                                      
                        while ($pos !== false) {
                                if ($pos != 0) {
                                        $pos = $pos  + strlen($page) + $offset;                  
                                }
 
                                $pos = strpos($content, $page, $pos);                            
                                $offset = 0;
 
                                if ($pos === false) {
                                        continue;                                                                              
                                }
 
                                if (preg_match('/[a-zA-Z0-9\[\<</span>]/', $content[$pos-1]) == 0 &&
                                    preg_match('/[a-zA-Z0-9\]\>]/', $content[$pos+strlen($page)]) == 0) {                             
 
                                        $content = substr($content, 0, $pos) . 
                                                           '[[' . substr($content, $pos, strlen($page)) . ']]' .     
                                                           substr($content, $pos + strlen($page));
                                        $offset = 4;                                                                                                                                          
                                        if (AUTOLINK_REPLACE_FIRST_ONLY === true) {
                                                $pos = false;                                                                                                 
                                        }
                                }
 
                                if (AUTOLINK_REPLACE_FIRST_ONLY === true &&
                                    preg_match('/[\[]/', $content[$pos-1]) != 0 &&
                                    preg_match('/[\]]/', $content[$pos+strlen($page)]) != 0) {                    
                                $pos = false;
                                }
                        }
                }
 
                $text = $content;
                return true;
}
 
#Function to return page names from AutoLinkPages as an array()
function getPages()
{
        $title = Title::newFromURL('AutoLinkPages');
        $article = new Article($title);
        $pagenames = $article->getContent();
        $pages = explode(AUTOLINK_LINK_DELIMITER, $pagenames);
        return $pages;
}
?>