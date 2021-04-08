<?php
/**
* This program will check a file every 5 seconds to see if it has changed...if it has, the new metadata will be sent to the icecast server(s)
*/

//the path to the file where your song information is placed...please see below notes for formatting of metadata
DEFINE('songfile', "s:\Playlists\broad.txt");


//simply copy and paste this code block for each server you need to add
$serv["host"][] = "10.10.10."; //ip or hostname the server is running on
$serv["port"][] = 8000; //port the server is running on
$serv["mount"][] = "/iradio"; //this format: /mount
$serv["user"][] = ""; //icecast server username
$serv["passwd"][] = ""; //icecast server password

while(1)
{
    $t=time();
    clearstatcache();
    $mt=@filemtime(songfile);
    if ($mt===FALSE || $mt<1)
    {
        echo "file not found, will retry in 1 seconds";
        sleep(5);
        continue;
    }

    if ($mt==$lastmtime)
    {
        //file unchanged, will retry in 1 seconds
        sleep(5);
        continue;
    }
   
    $da="";
    $f=@fopen(songfile, "r");
    if ($f!=0)
    {
        $da=@fread($f, 4096);
        fclose($f);

        /**
        * Assuming that the file is in this format:
        *
        * Title: Superhero
        * Artist: Jane's Addiction
        * Time: N/A
        */
       
        //separate our file by lines
        $explode_da=explode("\n", $da);
       
        if(is_array($explode_da))
        {
            //remove "Title: "
            $title=preg_replace("/Title: /", '', $explode_da[0]);
           
            //remove "Artist: "
            $artist=preg_replace("/Artist: /", '', $explode_da[1]);
           
            //our metatdata to send to the server
            $final_metadata=urlencode(trim($artist.' - '.$title));
        }
        else
        {
            $final_metadata=urlencode("Parse Error - Please Check Your Output File");
        }
    }
    else
    {
        echo "error opening songfile, will retry in 1";
        sleep(5);
        continue;
    }
   
    $lastmtime=$mt;
   
    for($count=0; $count < count($serv["host"]); $count++)
    {
        $mysession = curl_init();
        curl_setopt($mysession, CURLOPT_URL, "http://".$serv["host"][$count].":".$serv["port"][$count]."/admin/metadata?mount=".$serv["mount"][$count]."&mode=updinfo&song=".$final_metadata);
        curl_setopt($mysession, CURLOPT_HEADER, false);
        curl_setopt($mysession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($mysession, CURLOPT_POST, false);
        curl_setopt($mysession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($mysession, CURLOPT_USERPWD, $serv["user"][$count].":".$serv["passwd"][$count]);
        curl_setopt($mysession, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($mysession, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
        curl_setopt($mysession, CURLOPT_CONNECTTIMEOUT, 2);
        curl_exec($mysession);
        curl_close($mysession);
    }
   
    echo "song updated";
   
    sleep(5);
}
?>