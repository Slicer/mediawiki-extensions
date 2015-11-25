<?php
# Google Calendars
#
# Tag :
#   <googlecalendar>docid</googlecalendar>
# Ex :
#   from url http://calendar.google.com/calendarplay?docid=6444586097901795775
#   <googlecalendar>6444586097901795775</googlecalendar>
#
# Enjoy !

$wgExtensionFunctions[] = 'wfGoogleCalendar';
$wgExtensionCredits['parserhook'][] = array(
    'name' => 'Google Calendar',
    'description' => 'Display Google Calendar',
    'author' => 'Kasper Souren',
    'url' => 'http://wiki.couchsurfing.com/en/Google_Calendar_MediaWiki_plugin'
);

function wfGoogleCalendar() {
    global $wgParser;
    $wgParser->setHook('googlecalendar', 'renderGoogleCalendar');
}

# The callback function for converting the input text to HTML output
function renderGoogleCalendar($input, array $args) {
    $input = htmlspecialchars($input);

    $width = 480;
    if( isset( $args['width'] ) && $args['width'] ) {
        $width = $args['width'];
    }

    $height = 600;
    if( isset( $args['height'] ) && $args['height'] ) {
        $height = $args['height'];
    }

    $title = "Google Calendar";
    if( isset( $args['title'] ) && $args['title'] ) {
        $title = $args['title'];
    }
    
    $modeString = "";
    if( isset( $args['mode'] ) && $args['mode'] ) {
        if( strtolower($args['mode']) == "month" ) {
            # Month is default, empty it
            $modeString = "";
        }else {
            $modeString = 'mode='.$args['mode'];
        }
    }

    $dateString = "";
    if( isset( $args['showDate'] ) && $args['showDate'] ) {
        # Check date format
        if( $timestamp = strtotime($args['showDate']) != false )
        {
            #dates=YYYYmmdd%2FYYYYmmdd
            $dateString = '&dates='.date("YYYYmmdd", $timestamp).'%2F'.date("YYYYmmdd", $timestamp);
        }
    }

    $output = '<iframe src="http://www.google.com/calendar/embed?'.$modeString.'&src='.$input.'&title='.$title.'&chrome=NAVIGATION&height='.$height.'&epr=4" style=" border-width:0 " width="'.$width.'" frameborder="0" height="'.$height.'"'.$dateString.'></iframe>';

    return $output;
}
?>
