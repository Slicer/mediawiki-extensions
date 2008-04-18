<?php
# Poll extension for WikiMedia v1.2 Eric David / 2006
# http://fr.wikipedia.org/w/index.php?title=Utilisateur:Serenity/poll.php
# <Poll> 
# Question
# Answer 1
# Asnwer 2
# ...
# Answer n
# </Poll>
#
# To activate the extension, include it from your LocalSettings.php
# with: include("extensions/poll.php"); 
$wgExtensionFunctions[] = "wfPoll";

function wfPoll() {
        global $wgParser;
        # register the extension with the WikiText parser
        # the first parameter is the name of the new tag. 
        # In this case it defines the tag <Poll> ... </Poll>
        # the second parameter is the callback function for 
        # processing the text between the tags
        $wgParser->setHook( "poll", "renderPoll" );
	$wgParser->disableCache();
}

# The callback function for converting the input text to HTML output
# $argv is an array containing any arguments passed to the extension like <example argument="foo" bar>..

function renderPoll( $input, $argv=array() ) {
	global $wgParser,$wgUser,$wgScriptPath;

	$wgParser->disableCache();
	if ($wgUser->mName == "") $user = wfGetIP(); else $user = $wgUser->mName; 
	if (function_exists('memory_get_usage')) $memory = memory_get_usage(); else $memory = "unavailable";
	$ID = strtoupper(md5($input));
	$err = "";
        
	if (!empty($_POST['poll_ID']))  // POST Variables treatments
		{
		  $_POST['poll_ID'] = trim($_POST['poll_ID'],"/");
		  if (!empty($_POST['answer']))
		  {  
		  $_POST['answer'] = trim($_POST['answer'],"/");
		  if ($_POST['poll_ID'] == $ID)  // PROCESS THE VOTE
			{
               		$sql = "DELETE FROM `poll` WHERE `poll_id` = '".$ID."' and `poll_user` = '".$user."'";
                	mysql_query($sql);
			$err = $err.mysql_error();
                	$sql = "INSERT INTO `poll` "
                                        	."(`poll_id`, `poll_user`, `poll_ip`, `poll_answer`, `poll_date`)\n"
                                        	."\tVALUES ('".$ID."', '".$user."', '".wfGetIP()."', '".
                                        	$_POST['answer']."', '".date("Y-m-d H:i:s")."')";
	                mysql_query($sql);
			$err = $err.mysql_error();
			}			
		  }
		  if (!empty($_POST['comment']))
		  {  
		  $_POST['comment'] = trim($_POST['comment'],"/");
		  $_POST['comment'] = ereg_replace("'","''",$_POST['comment']);
		  if ($_POST['poll_ID'] == $ID)  // PROCESS THE COMMENT
			{
                	$sql = "DELETE FROM `comments` WHERE `poll_id` = '".$ID."' and `poll_user` = '".$user."'";
                	mysql_query($sql);
			$err = $err.mysql_error();
                	$sql = "INSERT INTO `comments` "
                                        ."(`poll_id`, `poll_user`, `poll_ip`, `poll_comment`, `poll_date`)\n"
                                        ."\tVALUES ('".$ID."', '".$user."', '".wfGetIP()."', '".
                                        $_POST['comment']."', '".date("Y-m-d H:i:s")."')";
                	mysql_query($sql);
			$err = $err.mysql_error();
			}
		  $_POST['comment'] = ereg_replace("''","'",$_POST['comment']); // sinon effet cumulAc
		  }
		}

                $lines = split("\n",$input);
		if ($lines[1] == "STATS") // special param : stats
		{
	                $sql = "SELECT COUNT(*),COUNT(DISTINCT poll_id),COUNT(DISTINCT poll_user),MAX(poll_date) FROM `poll`\n";
			$result = mysql_query($sql);
			$tab = mysql_fetch_array($result);
			ini_set( 'memory_limit',"20M");
			return "There are $tab[1] polls and $tab[0] votes given by $tab[2] different people.".
			"<br>The last vote has been given at <i>$tab[3]</i>.<BR>";
//			."<font size=1>memory_get_usage() : ".$memory."    ".
//			       "memory_limit : ".ini_get('memory_limit')."</font><BR>";
		}
		if ($lines[1] == "STATSFR") // special param : stats
		{
	                $sql = "SELECT COUNT(*),COUNT(DISTINCT poll_id),COUNT(DISTINCT poll_user),MAX(poll_date) FROM `poll`\n";
			$result = mysql_query($sql);
			$tab = mysql_fetch_array($result);
			ini_set( 'memory_limit',"20M");
			return "Il y a actuellement $tab[1] sondages et $tab[0] opinions émises par $tab[2] internautes.".
			"<br>La dernière opinion a été émise à la date <i>$tab[3]</i>.<BR>";
//			."<font size=1>( memory_get_usage() : ".$memory."    ".
//			       "memory_limit : ".ini_get('memory_limit').") </font><BR>";
		}
		// Getting the votes
                $sql = "SELECT `poll_answer`, COUNT(*)"
                        ."\tFROM `poll`\n"
                        ."\tWHERE "."`poll_id` = '".$ID."'"."\n"
                        ."\tGROUP BY `poll_answer`";
                $result = mysql_query($sql);
		$err = $err.mysql_error();
                while ($row = mysql_fetch_array($result)) $poll_result[$row[0]] = $row[1]; 
		if (empty($poll_result)) $total = 0; else $total = array_sum($poll_result);

		// has the user already voted ?
                $sql = "SELECT COUNT(*) FROM `poll`\n"
                        ."\tWHERE `poll_id` = '".$ID."' AND `poll_user` = '".$user."'\n";
                $result = mysql_query($sql);
		$err = $err.mysql_error();
		$row = mysql_fetch_array($result);
		$deja_vote = $row[0];

		// Getting the comments
                $sql = "SELECT `poll_user`,`poll_comment` FROM `comments`\n"
                        ."\tWHERE "."`poll_id` = '".$ID."' ORDER BY `poll_date`\n";
                $comresult = mysql_query($sql);
		$err = $err.mysql_error();
		$i = 0;
                while ($comrow = mysql_fetch_array($comresult)) $compoll_result[$i++] = "$comrow[1]";
		$comtotal = $i;

		// building HTML
		$str = '<a name="'.$ID.'" id="'.$ID.'"></a>'.
			'<b>'.$lines[1].'</b> <font size=1>('.$total.' votes)</font>'.
			'<table border="0" cellpadding=0 cellspacing=0>'."\n";
		$nbansw = count($lines)-1;
		for ($i=2;$i<$nbansw;$i++)
			{ $str .= '<tr>';
			  $str .= '<td><form name="poll" method="POST" action="#'.$ID.'">'."\n".
			'<input type="hidden" name="poll_ID" value="'.($ID).'">'."\n".
			'<input type="hidden" name="answer" value="'.$i.'">'."\n".
			'<input type="submit" value="  ->  "> '.$lines[$i].'  </form></td>'."\n"; 
			if ($total>0)
				{
				if (empty($poll_result[$i])) $res = 0; else $res = $poll_result[$i];
				$str .= '<td width=160><font size=1>';
				$a = $deja_vote==1?"2":"";
				if ($res>0)
					$str .= '<img src="'.$wgScriptPath.'/images/left'.$a.'.gif">'.
				'<img src="'.$wgScriptPath.'/images/middle'.$a.'.gif" alt="'.$res.' votes" height=10 width='.round($res*100/$total).'>'.
				'<img src="'.$wgScriptPath.'/images/right'.$a.'.gif">';
				$str .= ' '.round($res*100/$total).'% </font></td>';

// 			bar in HTML
//					'<table border=0 cellspacing=0 cellpadding=0><tr height=3><td bgcolor='.
//			($deja_vote==1?"#0000FF":"#C00000").' width='.round($res*100/$total).'> </td></tr></table></font></td>';

				}
			$str .= '</tr>'."\n";
			}
		$str .= '</table><form name=poll method=POST action="#'.$ID.'"><font size=1>  '."\n".
			'<input type="hidden" name="poll_ID" value="'.($ID).'">'."\n".
			'Message : <input type="text" name="comment" size=50 value="Type your message">    <select>'."\n";
		$str .= "<option>$comtotal message"; if ($comtotal>1) $str .= "s";
		for ($i=0;$i<$comtotal;$i++)
			$str .= "<option>$compoll_result[$i]\n";
		$str .= "</select></font></form>";
		if ($err != NULL) return "<B>Error</B><BR>".$err;
		return $str;
//	"<font size=1> ( ".round($memory/1024/1024)." / ".ini_get('memory_limit').") </font><BR>";
        }

?>