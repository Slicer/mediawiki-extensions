<?php

# Glossary MediaWiki Extention
# Created by Benjamin Kahn (xkahn@zoned.net) edited by wvanbusk 20070717 and Jperl 20070810.
#
# Based on the Emoticon MediaWiki Extension written by Alex Wollangk (alex@wollangk.com)

if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

global $wgHooks;
global $wgExtensionCredits;

$wgExtensionFunctions[] = "wfTooltip";

$wgExtensionCredits['parserhook'][] = array(
'name' => 'Glossary',
'status' => 'stable',
'type' => 'hook',
'author' => 'Benjamin Kahn (xkahn@zoned.net) based on code by Alex Wollangk (alex@wollangk.com) editet by wvanbusk and Jperl',
'version' => '1.0',
'update' => '10-09-2007',
'description' => 'Enable a Glossary within MediaWiki using tooltips.',
);

function wfTooltip() {
        global $wgParser;
        $wgParser->setHook( "tooltip", "tooltip" );
}

$wgHooks['ParserBeforeStrip'][] = 'fnTooltips';

function tooltip ( $input ) {
        $input = preg_replace ('/XQX/', '', $input);
        $split = explode("|", $input, 2);
        return "<span class=\"glossary\" title=\"" . $split[1] . "\">" . $split[0] . "</span>";
}

// The callback function for replacing acronyms with tooltip tags
function fnTooltips( &$parser, &$text, &$strip_state ) {
        global $action; // Access the global "action" variable
        // Only do the replacement if the action is not edit or history

        if
        (
                $action !== 'edit'
                        && $action !== 'history'
                        && $action !== 'delete'
                        && $action !== 'watch'
                        && $parser->mTitle->getNamespace() != NS_SPECIAL
                        && $parser->mTitle->mNamespace !== 8
                        && $parser->mTitle->mTextform !== "Glossary"
        )
        {

                $acro = array ();
                $repl = array ();

                // Get the list of emoticons from the "Glossary" article.
                $title = Title::makeTitle( null , 'Glossary' );
                $rev = Revision::newFromTitle( $title );
                if( $rev )
                {
                    $content = $rev->getText();
                }

                // If the content successfully loaded, do the replacement
                if( $content != "" )
                {
                        // makes glossary definitions to be on only one row, not two
                        $content = str_replace("\n:",":",$content);

                        $emoticonList = explode( "\n", $content );
                        foreach( $emoticonList as $index => $emoticon )
                        {
                                $currEmoticon = explode( ":", $emoticon, 2 );

                                if( count($currEmoticon) == 2 )
                                {
                                        // start by trimming the search value
                                        $currEmoticon[ 0 ] = trim( $currEmoticon[ 0 ] );
                                        // if the string begins with  ; lop it off
                                        if( substr( $currEmoticon[ 0 ], 0, 1 ) == ';' )
                                        {
                                                $currEmoticon[ 0 ] = trim( substr( $currEmoticon[ 0 ], 1 ) );
                                        }
                                        // trim the replacement value
                                        $currEmoticon[ 1 ] = trim( $currEmoticon[ 1 ] );
                                        if($currEmoticon[0])
                                        {
                                                array_push ($acro, '/(^| |\n)' . $currEmoticon[ 0 ] . '(\b)/');
                                                array_push ($repl, '$1<tooltip>XQX' . $currEmoticon[ 0 ] . "XQX|XQX" . $currEmoticon[ 1 ] . 'XQX</tooltip>');
                                        }
                                }
                        }

                        // and finally perform the replacement
                        //echo "[" . join($repl,"<br>") ."]<br>";
                        $text = preg_replace ( $acro, $repl, $text);
                }
        }
        return true;
}

?>